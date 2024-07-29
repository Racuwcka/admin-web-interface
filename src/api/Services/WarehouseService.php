<?php

namespace api\Services;

use api\Core\Models\Warehouse\Warehouse;
use api\Core\Models\Warehouse\WarehouseIdName;
use api\Core\Repositories\Warehouse\DTO\WarehouseDTO;
use api\Core\Repositories\Warehouse\WarehouseRepository;
use api\Core\Storage\SessionStorage;
use BaconQrCode\QrCode;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Core\Localizations\Localizations;
use Core\Services\ConvertEncoding;
use Fpdf\FPDF;

class WarehouseService
{
    public static function get(?string $id): ?Warehouse
    {
        $id = $id ?? SessionStorage::warehouse();
        if (is_null($id)) {
            return null;
        }

        $warehouseDTO = WarehouseRepository::get($id);
        if (is_null($warehouseDTO)) {
            return null;
        }

        return Warehouse::fromDTO($warehouseDTO);
    }

    /** Поиск склада по имени */
    public static function search(string $name): array
    {
        $warehouseListDTO = WarehouseRepository::search($name);
        return self::toList($warehouseListDTO);
    }

    /** Получение складов по параметру консолидации */
    public static function getList(?bool $consolidated = null): array
    {
        $warehouseListDTO = WarehouseRepository::getByConsolidated($consolidated);
        return self::toList($warehouseListDTO);
    }

    /** @param array<WarehouseDTO> $warehouseListDTO */
    private static function toList(array $warehouseListDTO): array
    {
        $regions = ModuleService::getListRegion();
        $regionList = array_column($regions, 'name', 'id');

        foreach ($warehouseListDTO as $warehouseDTO) {

            $data = [
                'id' => $warehouseDTO->id,
                'name' => $warehouseDTO->name
            ];

            if (!isset($warehouses[$warehouseDTO->region])) {
                $warehouses[$warehouseDTO->region]['country'] = Localizations::get($regionList[$warehouseDTO->region]);
            }
            $warehouses[$warehouseDTO->region]['list'][] = $data;
        }
        return $warehouses ?? [];
    }

    /** Генерация и скачивание pdf файла qr-кода склада  */
    public static function generate(string $id): bool
    {
        try {
            $warehouse = self::get($id);
            if (is_null($warehouse)) {
                return false;
            }

            $hash = [
                'typecode' => 'warehouse',
                'data' => [
                    'id' => $warehouse->id,
                    'name' => $warehouse->name
                ]
            ];

            $qrCode = QrCode::renderFile(json_encode($hash), 400, 'jpeg');

            header("Access-Control-Expose-Headers: Content-Disposition");

            $pdf = new FPDF("L", "pt", [400, 700]);

            $pdf->AddPage();
            $pdf->AddFont('arial-amu','','arial-amu.php');
            $pdf->AddFont('arial-black', '', 'arial-black.php');
            $pdf->Image($qrCode, 40, 50, 300);

            $pdf->SetXY(370, 150);
            $pdf->SetFont('arial-black', '', 26);

            if ($warehouse->region != 'RU' || $warehouse->region != 'KZ') {
                $warehouseName = ConvertEncoding::cp1251($warehouse->name);
            } else {
                $warehouseName = ConvertEncoding::ascii($warehouse->name);
            }

            $pdf->MultiCell(270, 30, $warehouseName, "0", "C");

            $pdf->SetXY(370, 250);
            $pdf->SetFont('arial-amu', '', 20);
            $pdf->MultiCell(270, 20, ConvertEncoding::cp1251(Localizations::get('code.choose.warehouse')), "0", "C");

            $pdf->Output("D", "$warehouse->name.pdf", true);

            unlink($qrCode);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getName(string $id): ?string
    {
        return WarehouseRepository::getName($id);
    }

    private static function isConsolidated(string $id): bool
    {
        $consolidated = WarehouseRepository::getConsolidated($id);
        return $consolidated === 1;
    }

    public static function isGroupCurrentWarehouseUser(string $warehouseId): bool
    {
        try {
            if (!self::isConsolidated(SessionStorage::user()->warehouse)) {
                if (SessionStorage::user()->warehouse === $warehouseId) {
                    return true;
                }
            }

            return !is_null(WarehouseRepository::getThisParent(
                id: $warehouseId,
                parent: SessionStorage::user()->warehouse
            ));
        }
        catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Получение дочерних складов для консолидированного склада
     * @return array<WarehouseIdName>
     */
    public static function getAccessList(): array
    {
        if (WarehouseService::isConsolidated(SessionStorage::user()->warehouse)) {
            $warehouseIdNameListDTO = WarehouseRepository::getByParent(SessionStorage::user()->warehouse);
            return WarehouseIdName::fromListDTO($warehouseIdNameListDTO);
        }
        return [];
    }
}
