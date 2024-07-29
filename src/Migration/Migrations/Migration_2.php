<?php

namespace Migration\Migrations;

use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Migration\Core\AbstractClasses\MigrationAbstract;
use Migration\Core\DataBase;
use Migration\Core\Interfaces\MigrationInterface;
use Migration\Core\Models\SqlRequest;

final class Migration_2 extends MigrationAbstract implements MigrationInterface
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
                    query: "ALTER TABLE $dataBaseName.$table ADD `exclude_test1` INT(1) NOT NULL AFTER `typeId`",
                    args: []
                )
            ]
        );
    }
    public function down(): bool
    {
        return true;
    }
}