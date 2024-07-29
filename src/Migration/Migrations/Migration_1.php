<?php

namespace Migration\Migrations;

use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Migration\Core\AbstractClasses\MigrationAbstract;
use Migration\Core\DataBase;
use Migration\Core\Interfaces\MigrationInterface;
use Migration\Core\Models\SqlRequest;

final class Migration_1 extends MigrationAbstract implements MigrationInterface
{
    public function __construct(string $migrationName)
    {
        parent::__construct($migrationName);
    }

    public function up(): bool
    {
        $dataBaseName = DataBase::$instance->getDataBaseName(DataBaseType::main);
        $table = DataBaseTable::build_access->value;

        return $this->runExecute(
            method: 'DDL',
            requests: [
                new SqlRequest(
                    query: "ALTER TABLE $dataBaseName.$table ADD `exclude_test` INT(1) NULL AFTER `typeId`",
                    args: []
                )
            ]
        );
    }
    public function down(): bool
    {
        DataBase::$instance->beginTransaction();

        $dataBaseName = DataBase::$instance->getDataBaseName(DataBaseType::main);
        $table = DataBaseTable::build_access->value;

        $result = $this->runExecute(
            method: 'DML',
            requests: [
                new SqlRequest(
                    query: "INSERT INTO $dataBaseName.$table (`id`, `buildId`, `typeId`, `exclude`, `value`) VALUES (NULL, '11111', '11111', '1', '11111')",
                    args: []
                )
            ]
        );

        if ($result) {
            DataBase::$instance->commit();
            return true;
        }

        return false;
    }
}