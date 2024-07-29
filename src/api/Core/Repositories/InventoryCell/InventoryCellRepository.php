<?php

namespace api\Core\Repositories\InventoryCell;

use api\Core\Classes\DataBase;
use api\Core\Repositories\InventoryCell\DTO\InventoryCellActionDataDTO;
use api\Core\Repositories\InventoryCell\DTO\InventoryCellActionDTO;
use api\Core\Repositories\InventoryCell\DTO\InventoryCellActionResultDTO;
use api\Core\Repositories\InventoryCell\DTO\InventoryCellShelfActionCountDTO;
use api\Services\ThrowableLogger;
use Database\Core\Enums\DataBaseType;

class InventoryCellRepository
{
    /**
     * Получение количества пройденных инвентаризацию ячеек на этаже в каждом стеллаже
     * @param int $eventId идентификатор события
     * @param string $letter буквенное обозначение
     * @param int $floor этаж
     * @return array<InventoryCellShelfActionCountDTO>
     */
    public static function getListFloorActionCount(int $eventId, string $letter, int $floor): array
    {
        try {
            $dbData = DataBase::instance()->getDataBaseName(DataBaseType::data);
            $dbMain = DataBase::instance()->getDataBaseName(DataBaseType::main);

            $requestData = DataBase::instance()->execute(
                query: "SELECT
                            cell.shelf as shelf,
                            COUNT(DISTINCT cellCorrection.cell) as qty
                            FROM $dbData.log_cell_correction as cellCorrection
                            LEFT JOIN $dbMain.cell ON cell.id = cellCorrection.cell WHERE
                            cell.letter = ? AND
                            cell.floor = ? AND
                            cellCorrection.eventId=? GROUP BY cell.shelf ORDER BY cell.shelf ASC",
                args: [$letter, $floor, $eventId]
            );
            $data = $requestData->fetchAll();
            return InventoryCellShelfActionCountDTO::fromArrayToList($data);
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получить результаты всех действий события
     * @param int $eventId идентификатор события
     * @return array<InventoryCellActionResultDTO>
     */
    public static function getListActionResult(int $eventId): array
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::data);

            $requestData = DataBase::instance()->execute(
                query: "SELECT * FROM $dataBaseName.log_cell_correction WHERE `id` IN(SELECT MAX(id)
                                    FROM $dataBaseName.log_cell_correction WHERE eventId = ? GROUP BY cell)",
                args: [$eventId]
            );

            $data = $requestData->fetchAll();
            return InventoryCellActionResultDTO::fromArrayToList($data);
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Получение всех действий события в стеллаже
     * @param int $eventId идентификатор события
     * @param string $letter буквенное обозначение
     * @param int $floor этаж
     * @param int $shelf стеллаж
     * @return array<InventoryCellActionDTO>
     */
    public static function getListShelfAction(int $eventId, string $letter, int $floor, int $shelf): array
    {
        try {
            $dbData = DataBase::instance()->getDataBaseName(DataBaseType::data);
            $dbMain = DataBase::instance()->getDataBaseName(DataBaseType::main);

            $requestData = DataBase::instance()->execute(
                query: "SELECT 
                            cell.tier as tier,
                            cell.pos as pos,
                            cellCorrection.type,
                            cellCorrection.extraQty,
                            cellCorrection.missingQty,
                            cellCorrection.resultQty,
                            cellCorrection.userName,
                            cellCorrection.date
                            FROM $dbData.log_cell_correction as cellCorrection
                            LEFT JOIN $dbMain.cell ON cell.id = cellCorrection.cell WHERE 
                                cellCorrection.id IN (SELECT MAX(id)
                                FROM $dbData.log_cell_correction WHERE
                                eventId = ? GROUP BY cell) AND
                            cell.letter = ? AND
                            cell.floor = ? AND
                            cell.shelf = ?",
                args: [$eventId, $letter, $floor, $shelf]
            );

            $data = $requestData->fetchAll();
            return InventoryCellActionDTO::fromArrayToList($data);
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Поиск по данным действия события
     * @param int $eventId идентификатор события
     * @param string $query искомая фраза
     * @return array<InventoryCellActionDataDTO>
     */
    public static function search(int $eventId, string $query): array
    {
        try {
            $dbData = DataBase::instance()->getDataBaseName(DataBaseType::data);
            $dbMain = DataBase::instance()->getDataBaseName(DataBaseType::main);

            $requestDb = DataBase::instance()->execute(
                query: "SELECT
                            cell.id as cellId,
                            cell.name as cellName,
                            data.barcode,
                            data.articul,
                            data.size,
                            data.qty
                            FROM $dbData.log_cell_correction as cellCorrection
                            LEFT JOIN $dbMain.cell ON cell.id = cellCorrection.cell
                            LEFT JOIN $dbData.log_cell_correction_data as data ON data.operationId = cellCorrection.operationId WHERE 
                                cellCorrection.id IN (SELECT
                                MAX(id)
                                FROM $dbData.log_cell_correction WHERE
                                data.type = 'result' AND
                                eventId = ? GROUP BY cell) AND
                            (data.barcode = ? OR data.articul = ?)",
                args: [$eventId, $query, $query]
            );

            $data = $requestDb->fetchAll();
            return InventoryCellActionDataDTO::fromArrayToList($data);
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }
}