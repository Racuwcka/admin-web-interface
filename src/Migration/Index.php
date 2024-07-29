<?php

namespace Migration;

use Database\Core\Enums\DataBaseTable;
use Database\Core\Enums\DataBaseType;
use Migration\Core\DataBase;

class Index
{
    private array $listErrors = [
        '1' => 'Не удалось получить версию миграции из БД',
        '2' => 'Неправильное имя файла миграции',
        '3' => 'Миграция не прошла',
        '4' => 'Не удалось записать версию миграциии в БД'
    ];

    public function __construct()
    {
        DataBase::setUp();
        $this->migrate();
    }

    private function getCurrentVersion(): ?int
    {
        try {
            DataBase::$instance->selectRow(
                type: DataBaseType::main,
                table: DataBaseTable::db_version
            );
            return true;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function setCurrentVersion(int $version): bool
    {
        try {
            DataBase::$instance->updateRow(
                type: DataBaseType::main,
                table: DataBaseTable::db_version,
                values: ['version' => $version]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function migrate(): void
    {
        $currentMigration = $this->getCurrentVersion();
        if (is_null($currentMigration)) {
            exit('1');
        }

        $migrations = glob(str_replace('\\', '/', realpath('src/Migration/Migrations')) . '/Migration_*.php');

        foreach ($migrations as $migration) {
            $migrationFileName = pathinfo($migration, PATHINFO_FILENAME);

            $temp = explode("_", $migrationFileName);
            $migrationNumber = intval(array_pop($temp));

            if ($migrationNumber == 0) {
                exit('2');
            }

            if ($currentMigration < $migrationNumber) {
                $class = 'Migration\\Migrations\\' . $migrationFileName;

                if (method_exists($class, "run")) {
                    $migrateRun = (new $class($migrationFileName))->run();

                    if (!$migrateRun) {
                        exit('3');
                    }

                    if (!$this->setCurrentVersion($migrationNumber)) {
                        exit('4');
                    }
                }
            }
        }
    }
}