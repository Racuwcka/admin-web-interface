<?php

namespace Cron\Modules;

use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Cron\Core\DataBase;
use Cron\Core\Request;

class Nomenclature
{
    public function syncNomenclature(): void
    {
        $request = Request::$oneC->common->syncNomenclature();

        if ($request->status) {
            $chunkCount  = 1000;
            $parts = ceil(count($request->data) / $chunkCount);

            for ($i = 0; $i < $parts; $i++) {
                $currentPart = $i;
                $start = $currentPart * $chunkCount;
                $end = $i >= $parts - 1 ? null : ($currentPart * $chunkCount) + $chunkCount;

                $nomenclature = array_slice($request->data, $start, $end);

                $temp = [];
                foreach ($nomenclature as $item) {
                    if (strlen($item['barcode']) !== 13 || $item['articul'] === '' || $item['size'] === '') {
                        continue;
                    }

                    $temp[] = [
                        'name' => $item['name'],
                        'barcode' => $item['barcode'],
                        'articul' => $item['articul'],
                        'size' => $item['size'],
                        'marked' => intval($item['markirovka'])
                    ];
                }

                DataBase::$instance->upsert(
                    type: DataBaseType::main,
                    table: DataBaseTable::nomenclature,
                    values: $temp
                );
            }
            echo true;
        }
        echo false;
    }
}