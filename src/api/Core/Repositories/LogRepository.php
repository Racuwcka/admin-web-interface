<?php

namespace api\Core\Repositories;

use api\Core\Classes\DataBase;
use api\Services\ThrowableLogger;
use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;

class LogRepository
{
    public static function insertMultipleLog(string $table, array $itemsData): bool
    {
        try {
            DataBase::instance()->insertMultiple(
                type: DataBaseType::data,
                table: DatabaseTable::tryFrom($table),
                values: $itemsData
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function insertLog(string $table, array $items): bool
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::data,
                table: DatabaseTable::tryFrom($table),
                values: $items
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }

    public static function getFromTable($table, $fieldFooting, $arg, $type, $dateFrom, $dateTo): ?array
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::data);
            $requestData = DataBase::instance()->execute(
                query: "SELECT * FROM $dataBaseName.$table WHERE $fieldFooting = ? AND `type` = ? AND `date` >= ? AND `date` <= ? AND `basic` IS NULL",
                args: [$arg, $type, $dateFrom, $dateTo]
            );
            return $requestData->fetchAll();
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function getLogCell($partRequest, $cell, $limit): ?array
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::data);
            $requestData = DataBase::instance()->execute(
                query: "(SELECT 'interaction' as typeLog, 
                                            '' as extractionCreateDocumentName,
                                            '' as extractionDocumentName,
                                            '' as movingCellFrom,
                                            '' as movingCellTo,
                                            operationId,
                                            qty,
                                            userName,
                                            date,
                                            basic,
                                            type as interactionType 
                                                FROM $dataBaseName.log_cell WHERE `cell`=?$partRequest 
                                UNION ALL 
                                SELECT 'moving' as typeLog,
                                        '' as extractionCreateDocumentName,
                                        '' as extractionDocumentName,
                                        cellFrom as movingCellFrom,
                                        cellTo as movingCellTo,
                                        operationId,
                                        qty,
                                        userName,
                                        date,
                                        basic,
                                        '' as interactionType 
                                            FROM $dataBaseName.log_cell_moving WHERE `cellFrom`=? OR `cellTo`=?$partRequest 
                                UNION ALL 
                                SELECT 'extraction' as typeLog,
                                        createDocumentName as extractionCreateDocumentName,
                                        documentName as extractionDocumentName,
                                        '' as movingCellFrom,
                                        '' as movingCellTo,
                                        operationId,
                                        qty,
                                        userName,
                                        date,
                                        basic,
                                        'take' as interactionType 
                                            FROM $dataBaseName.log_cell_extraction WHERE `cell`=?$partRequest) 
                                ORDER BY `date` DESC LIMIT ?",
                args: [$cell, $cell, $cell, $cell, $limit]
            );
            return $requestData->fetchAll();
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function getAllByOperationId($table, $operationId): ?array
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::data);
            $requestData = DataBase::instance()->execute(
                query: "SELECT * FROM $dataBaseName.$table WHERE `operationId`=?",
                args: [$operationId]
            );
            return $requestData->fetchAll();
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function getLogPackage($partRequest, $packageId, $limit): ?array
    {
        try {
            $dataBaseName = DataBase::instance()->getDataBaseName(DataBaseType::data);
            $requestData = DataBase::instance()->execute(
                query: "(SELECT 'interaction' as typeLog, 
                                            '' as placementType,
                                            '' as movingWarehouseFrom,
                                            '' as movingWarehouseTo,
                                            '' as placementCell,
                                            operationId,
                                            qty,
                                            userName,
                                            date,
                                            basic,
                                            type as interactionType 
                                                FROM $dataBaseName.log_package WHERE `packageId`=?$partRequest 
                                UNION ALL 
                                SELECT 'moving' as typeLog,
                                        '' as placementType,
                                        warehouseFrom as movingWarehouseFrom,
                                        warehouseTo as movingWarehouseTo,
                                        '' as placementCell,
                                        operationId,
                                        qty,
                                        userName,
                                        date,
                                        basic,
                                        'info' as interactionType 
                                            FROM $dataBaseName.log_package_moving WHERE `packageId`=?$partRequest 
                                UNION ALL 
                                SELECT 'placement' as typeLog,
                                        type as placementType,
                                        '' as movingWarehouseFrom,
                                        '' as movingWarehouseTo,
                                        cell as placementCell,
                                        operationId,
                                        qty,
                                        userName,
                                        date,
                                        basic,
                                        'info' as interactionType 
                                            FROM $dataBaseName.log_package_placement WHERE `packageId`=?$partRequest) 
                                ORDER BY `date` DESC LIMIT ?",
                args: [$packageId, $packageId, $packageId, $limit]
            );
            return $requestData->fetchAll();
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return [];
        }
    }

    public static function insertLogLogisticsConfirm(array $items): bool
    {
        try {
            DataBase::instance()->insert(
                type: DataBaseType::data,
                table: DatabaseTable::log_logistics_confirm,
                values: $items
            );
            return true;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }
}