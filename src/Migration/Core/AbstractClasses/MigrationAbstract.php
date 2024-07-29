<?php

namespace Migration\Core\AbstractClasses;

use Core\Logger\Logger;
use Migration\Core\DataBase;

abstract class MigrationAbstract
{
    private string $fileLog;

    public function __construct(
        private readonly string $migrationName
    ) {
        $this->fileLog = date('Y-m-d_H-i-s', time());

        Logger::logger(
            dir: 'migrations',
            filename: $this->fileLog,
            text: "# {$this->migrationName} началась"
        );
    }

    public function __destruct()
    {
        Logger::logger(
            dir: 'migrations',
            filename: $this->fileLog,
            text: "# {$this->migrationName} завершена" . PHP_EOL
        );
    }

    public function run(): bool
    {
        return $this->up() && $this->down();
    }

    protected function runExecute(string $method, array $requests): bool
    {
        for ($i = 0; $i < count($requests); $i++) {
            $result = DataBase::$instance->execute(
                query: $requests[$i]->query,
                args: $requests[$i]->args,
            );

            if ($result instanceof \Exception) {
                if (DataBase::$instance->inTransaction()) {
                    DataBase::$instance->rollBack();
                }
                Logger::logger(
                    dir: 'migrations',
                    filename: $this->fileLog,
                    text: 'Ошибка: ' . $result->getMessage()
                );
                return false;
            }
            else {
                Logger::logger(
                    dir: 'migrations',
                    filename: $this->fileLog,
                    text: 'Миграция ' . $method . ' прошла'
                );
            }
        }
        return true;
    }
}