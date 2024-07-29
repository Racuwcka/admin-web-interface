<?php

namespace api\Core\Repositories\TradingHall;

use api\Core\Classes\DataBase;
use api\Core\Repositories\TradingHall\DTO\TradingHallItemDTO;
use api\Core\Repositories\TradingHall\DTO\TradingHallRemainsDTO;
use api\Core\Repositories\TradingHall\DTO\TradingHallRemainsItemDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;

class TradingHallRepository
{
    /**
     * Сохранение остатков склада за определенный день
     * @param string $warehouse идентификатор склада
     * @param string $day день в виде 20230130
     * @param array<TradingHallRemainsItemDTO> $data
     * @return bool
     */
    public static function saveRemains(string $warehouse, string $day, array $data): bool
    {
        try {
            $data = TradingHallRemainsItemDTO::toList($data);
            if (count($data) < 1) {
                return false;
            }

            DataBase::instance()->upsert(
                type: DataBaseType::data,
                table: DataBaseTable::hall_remains,
                values: [[
                    "warehouse" => $warehouse,
                    "day" => $day,
                    "data" => json_encode($data)
                ]]);

            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Получение остатков склада за текущий день
     * @param string $warehouse идентификатор склада
     * @return TradingHallRemainsDTO|null
     */
    public static function getRemains(string $warehouse): ?TradingHallRemainsDTO
    {
        try {
            $where = new Where(
                new CompareOperator(
                    field: 'warehouse',
                    value: $warehouse,
                    operator: OperationType::Equals
                ));
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'day',
                    value: date('Ymd'),
                    operator: OperationType::Equals
                ));

            $data = DataBase::instance()->selectOne(
                type: DataBaseType::data,
                table: DataBaseTable::hall_remains,
                where: $where);
            return $data ? TradingHallRemainsDTO::fromArray(data: $data) : null;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return null;
        }
    }

    /**
     * Сохранение отсканированных позиции на склад
     * @param array<TradingHallItemDTO> $items
     * @return bool
     */
    public static function saveItems(array $items): bool
    {
        try {
            DataBase::instance()->upsert(
                type: DataBaseType::data,
                table: DataBaseTable::hall_items,
                values: TradingHallItemDTO::toList($items)
            );

            return true;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    /**
     * Получение отсканированных позиции со склада за текущий день
     * @return array<TradingHallItemDTO>
     */
    public static function getItems(string $warehouse): array
    {
        try {
            $where = new Where(
                new CompareOperator(
                    field: 'warehouse',
                    value: $warehouse,
                    operator: OperationType::Equals
                ));
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new CompareOperator(
                    field: 'day',
                    value: date('Ymd'),
                    operator: OperationType::Equals
                ));

            $data = DataBase::instance()->selectAll(
                type: DataBaseType::data,
                table: DataBaseTable::hall_items,
                where: $where);
            return TradingHallItemDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * Возвращает массив ввида - ключ (артикул) => значение (массив размеров)
     * @return array<string, array<string>> $data
     */
    public static function getItemsArticulSizes(string $warehouse): array
    {
        $data = self::getItems(warehouse: $warehouse);

        $items = [];
        foreach ($data as $item) {
            $items[$item->articul][] = $item->size;
        }

        return $items;
    }
}