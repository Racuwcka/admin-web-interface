<?php

namespace api\Services;

use api\Core\Models\WorkArea\WorkArea;
use api\Core\Repositories\WorkArea\WorkAreaRepository;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Core\Localizations\Localizations;
use Core\Services\ConvertEncoding;
use Fpdf\FPDF;

class WorkAreaService
{
    public static function get(int $id): ?WorkArea
    {
        $workAreaDTO = WorkAreaRepository::get($id);
        if (is_null($workAreaDTO)) {
            return null;
        }

        return WorkArea::fromDTO($workAreaDTO);
    }

    /** Получение списка рабочих зон */
    public static function getList(): array
    {
        $workAreaListDTO = WorkAreaRepository::getListAll();
        $workAreaList =  WorkArea::fromListDTO($workAreaListDTO);

        foreach ($workAreaList as $workArea) {
            if (!isset($result[$workArea->warehouse])) {
                $result[$workArea->warehouse]['id'] = $workArea->warehouse;
                $result[$workArea->warehouse]['name'] = WarehouseService::getName($workArea->warehouse);
            }

            $result[$workArea->warehouse]['workAreas'][] = [
                'name' => $workArea->name,
                'id' => $workArea->id
            ];
        }

        return array_values($result ?? []) ;
    }

    /**
     * Поиск рабочих зон по названию
     * @return array<WorkArea>
     */
    public static function search(string $name): array
    {
        $workAreaListDTO = WorkAreaRepository::search($name);
        return WorkArea::fromListDTO($workAreaListDTO);
    }

    /** Генерация и скачивание pdf файла qr-кода рабочей зон  */
    public static function generate(int $id): bool
    {
        try {
            $workArea = self::get($id);
            if (is_null($workArea)) {
                return false;
            }

            $renderer = new ImageRenderer(
                new RendererStyle(
                    size: 350,
                ),
                new ImagickImageBackEnd('jpeg')
            );

            $writer = new Writer($renderer);

            if (!file_exists('tmp')) {
                mkdir(ROOT_DIRECTORY . '/tmp');
            }

            $hash = [
                'typecode' => 'workarea',
                'data' => [
                    'id' => $workArea->id,
                    'name' => $workArea->name,
                    'warehouse' => $workArea->warehouse
                ]
            ];

            $qr = 'tmp/qrcodeWorkArea.jpeg';
            $writer->writeFile(json_encode($hash), $qr);

            header("Access-Control-Expose-Headers: Content-Disposition");

            $pdf = new FPDF("L", "pt", [400, 700]);

            $pdf->AddPage();
            $pdf->AddFont('arial-amu','','arial-amu.php');
            $pdf->AddFont('arial-black', '', 'arial-black.php');
            $pdf->Image($qr, 25, 25, 350);
            unlink($qr);

            $pdf->SetXY(370, 100);
            $pdf->SetFont('arial-black', '', 24);
            $pdf->MultiCell(270, 20, ConvertEncoding::cp1251($workArea->name), "0", "C");

            $pdf->SetXY(370, 220);
            $pdf->SetFont('arial-amu', '', 22);
            $pdf->MultiCell(270, 20, ConvertEncoding::cp1251(WarehouseService::getName($workArea->warehouse)), "0", "C");

            $pdf->SetXY(370, 280);
            $pdf->SetFont('arial-amu', '', 20);
            $pdf->MultiCell(270, 20, ConvertEncoding::cp1251(Localizations::get('code.access')), "0", "C");

            $pdf->Output("D", "$workArea->name.pdf", true);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
