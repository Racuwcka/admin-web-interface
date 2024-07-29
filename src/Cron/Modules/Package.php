<?php

namespace Cron\Modules;

use api\Core\Enums\Queue\QueueStatusType;
use api\Core\Models\Queue\Queue;
use api\Core\Repositories\Queue\DTO\QueueDTO;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Cron\Core\DataBase;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorEntryType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;
use Database\Core\Models\Operators\EntryOperator;
use Fpdf\FPDF;

class Package {
    /** Выполнение cron создание файла упаковок */
    public static function do(): void
    {
        $queueDTO = QueueDTO::fromArrayToList(DataBase::$instance->selectAll(
            type: DataBaseType::mdm,
            table: DataBaseTable::queue,
            where: (new Where(
                new CompareOperator(
                    field: 'name',
                    value: 'package',
                    operator: OperationType::Equals
                )))->add(logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'status',
                    value: QueueStatusType::created->value,
                    operator: OperationType::Equals
                ))
        ));

        $queues = Queue::fromListDTO($queueDTO);

        if (empty($queues)) exit;

        DataBase::$instance->update(
            type: DataBaseType::mdm,
            table: DataBaseTable::queue,
            values: ['status' => QueueStatusType::busy->value],
            where: new Where(
                new EntryOperator(
                    field: 'id',
                    value: array_column($queues, 'id'),
                    type: OperatorEntryType::In
                ))
        );

        if (!file_exists(ROOT_DIRECTORY.'/tmp')) {
            mkdir(ROOT_DIRECTORY . '/tmp');
        }

        foreach ($queues as $queue) {
            self::update(id: $queue->id, statusType: QueueStatusType::busy);

            $result = self::generate(id: $queue->id, count: $queue->count);

            self::update(id: $queue->id, statusType: $result ? QueueStatusType::done : QueueStatusType::rejected);
        }
        exit;
    }

    public static function generate(int $id, int $count): bool
    {
        try {
            $pdf = new FPDF("P", "mm", [70 ,70]);
            $pdf->AddFont('arial-amu','','arial-amu.php');
            $pdf->AddFont('arial-black','','arial-black.php');
            $pdf->SetAutoPageBreak(false);

            $dirPackage = ROOT_DIRECTORY . "/tmp/packages_$id";
            if (!file_exists($dirPackage)) {
                mkdir($dirPackage);
            }

            $renderer = new ImageRenderer(
                new RendererStyle(
                    size: 180,
                ),
                new ImagickImageBackEnd('jpg', 0)
            );

            $writer = new Writer($renderer);

            for ($i = 0; $i < $count; $i++) {

                $uniq = uniqid();
                $filename = "$dirPackage/qrcode_$uniq.jpg";

                $json = json_encode(['typecode' => 'package', 'data' => ['id' => $uniq]]);
                $writer->writeFile($json, $filename);

                $pdf->AddPage();
                $pdf->Image($filename, 10, 0, 50);

                $pdf->SetXY(10, 45);
                $pdf->SetFont('arial-black','', 12);
                $pdf->MultiCell(50, 10, $uniq, "0", "C");

                $pdf->SetFont('arial-amu','', 11);
                $pdf->MultiCell(50, 5, iconv('utf-8', 'cp1251', 'Уникальный код идентификации'), "0", "C");
                unlink($filename);
            }
            $pdf->Output("F", "$dirPackage/packages.pdf", true);

            return true;
        } catch (\Exception $e) {
            self::log($e->getMessage());
            return false;
        }
    }

    private static function update(int $id, QueueStatusType $statusType): void
    {
        DataBase::$instance->update(
            type: DataBaseType::mdm,
            table: DataBaseTable::queue,
            values: ['status' => $statusType->value],
            where: new Where(
                new CompareOperator(
                    field: 'id',
                    value: $id,
                    operator: OperationType::Equals
                ))
        );
    }

    private static function log(string $data): void
    {
        DataBase::$instance->insert(
            type: DataBaseType::mdm,
            table: DataBaseTable::log_queue,
            values: [
                'name' => 'package',
                'output' => $data,
                'created_at' => time()
            ]
        );
    }
}
