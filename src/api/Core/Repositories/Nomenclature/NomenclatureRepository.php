<?php

namespace api\Core\Repositories\Nomenclature;

use api\Core\Classes\DataBase;
use api\Core\Repositories\Nomenclature\DTO\NomenclatureDTO;
use api\Services\ThrowableLogger;
use Database\Core\Entity\Where;
use Database\Core\Enums\Condition\OperationType;
use Database\Core\Enums\Condition\OperatorEntryType;
use Database\Core\Enums\Condition\OperatorLogisticType;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Database\Core\Models\Operators\CompareOperator;
use Database\Core\Models\Operators\EntryOperator;

class NomenclatureRepository
{
    /**
     * Получение всей номенклатуры
     * @param int $page идентификатор страницы
     * @param int $limit лимит
     * @return array<NomenclatureDTO>
     */
    public static function getAll(int $page, int $limit, ?string $lastDate = null): array
    {
        try {
            $where = new Where();

            if ($lastDate !== null) {
                $where->add(
                    logisticOperatorType: OperatorLogisticType::And,
                    operator: new CompareOperator(
                        field: "date",
                        value: $lastDate,
                        operator: OperationType::Greater
                    )
                );
            }

            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::nomenclature,
                where: $where,
                select_fields: ["name", "barcode", "articul", "size", "marked"],
                limit: $limit,
                offset: $limit * ($page - 1)
            );

            return NomenclatureDTO::fromArrayToList($data);


        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function getRandom(int $count): array
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::main);
            $requestData = DataBase::instance()->execute(
                query: "SELECT * FROM $dataBaseName.nomenclature ORDER BY RAND() LIMIT ?",
                args: [$count]);
            $data = $requestData->fetchAll();
            return NomenclatureDTO::fromArrayToList($data);
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /**
     * @param array<string> $barcodes
     * @return array<NomenclatureDTO>
     */
    public static function getFromBarcodes(array $barcodes): array
    {
        try {
            $where = new Where();
            $where->add(
                logisticOperatorType: OperatorLogisticType::And,
                operator: new EntryOperator(
                    field: "barcode",
                    value: $barcodes,
                    type: OperatorEntryType::In
                )
            );

            $data = DataBase::instance()->selectAll(
                type: DataBaseType::main,
                table: DataBaseTable::nomenclature,
                where: $where
            );
            return NomenclatureDTO::fromArrayToList($data);
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    /** Получение кол-во номенклатуры */
    public static function getCount(?string $lastDate = null): int
    {
        try {
            $where = new Where();

            if ($lastDate !== null) {
                $where->add(
                    logisticOperatorType: OperatorLogisticType::And,
                    operator: new CompareOperator(
                        field: "date",
                        value: $lastDate,
                        operator: OperationType::Greater
                    )
                );
            }

            return DataBase::instance()->count(
                type: DataBaseType::main,
                table: DataBaseTable::nomenclature,
                where: $where
            );
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return 0;
        }
    }

    public static function getLastDate(): ?string
    {
        try {
            $data = DataBase::instance()->selectOne(
                type: DataBaseType::main,
                table: DataBaseTable::nomenclature,
                order_value: ["date" => "DESC"],
                select_fields: ["date"],
                fetchColumn: true,
                implicitCondition: true
            );

            return $data ?: null;
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return 0;
        }
    }
}