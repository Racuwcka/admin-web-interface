<?php

namespace Cron\Core\Classes;

use Cron\Core\Models\CronTask;

class Scheduler
{
    private array $task_list = [];
    private array $time_map = [];
    private array $time = [];
    private const PHP_CLI = '/usr/bin/php8.1';
    private array $errors = [];

    public function __construct()
    {
        foreach (['minute'=>'i', 'hour'=>'H', 'day'=>'d', 'month'=>'m', 'week'=>'w',] as $key => $time_format) {
            $this->time[$key] = (int) date($time_format);
            $this->time_map[$key] = function ($value) use ($key) {
                return $this->_check_time($this->time[ $key ], $value);
            };
        }
    }

    /**
     * Получить текущее время CRON
     * @return array
     */
    public function get_time(): array
    {
        return $this->time;
    }

    private function _check_time($time, $value): bool
    {
        if ( count($value) === 2 ) {
            if ( ( $time % $value[1] ) === 0 ) {
                return true;
            }
        } else {
            if ( $value[0] === '*' ) {
                return true;
            }

            $value = (int) $value[0];

            if ( $time === $value ) {
                return true;
            }
        }

        return false;
    }

    private function allow_run(array $crontab): bool {
        foreach ( $crontab as $t=>$item ) {
            if (!$item['allow_run'] ) {
                return false;
            }
        }
        return true;
    }

    private function check_and_processing_expression(string $expression): array|bool
    {
        $time = explode(' ', $expression);
        if ( count($time) !== 5 ) {
            return false;
        }
        $map_time = array_keys($this->time_map);
        $parsed_time = [];
        foreach ($time as $i=>$el) {
            $cronObject = [
                'time'=>[],
                'allow_run'=>false
            ];
            $el = explode('/', $el);
            if ( count($el) === 2 ) {
                if ( $el[0] !== '*' ) {
                    $this->errors[] = $expression;
                    return false;
                }

                if ( !is_numeric($el[1]) || $el[1] <= 0 ) {
                    $this->errors[] = $expression;
                    return false;
                }
            } elseif ( $el[0] !== '*' && !is_numeric($el[0]) ) {
                $this->errors[] = $expression;
                return false;
            }

            $cronObject['time'] = $el;
            $cronObject['allow_run'] = $this->time_map[ $map_time[$i] ]($el);

            $parsed_time[ $map_time[$i] ] = $cronObject;
        }

        return $parsed_time;
    }

    public function get_errors()
    {
        return $this->errors;
    }

    /**
     * Добавить задачу
     * @param string $expression * - мин(0-59) * - часы(0-23) * - дни(1-31) * - месяцы(1-12) * - неделя (0-6)
     * @param CronTask $cronTask [action, method]
     * @return $this
     */
    public function add(string $expression, CronTask $cronTask): static {
        if ( $crontab = $this->check_and_processing_expression($expression) ) {
            $this->task_list[] = [
                'expression' => $expression,
                'crontab' => $crontab,
                'command' => vsprintf('php ' . ROOT_DIRECTORY . '/run.php --source=%s --args=%s', ["cron", escapeshellarg(serialize(json_encode($cronTask->getParams())))]),
                'method' => $cronTask->method
            ];
        }
        return $this;
    }

    /**
     * Запустить задачи по времени
     * @return void
     */
    public function run(): void {
        foreach ($this->task_list as $task) {
            if ( $this->allow_run($task['crontab']) ) {
                echo '[CronScheduler] Run command -> ', $task['expression'], ' | ', $task['command'], "\n";
                exec($task['command'], $output); var_dump($output);
                // TODO Logger
//                $output = array_shift($output);
//                $db = DbId::system->value; // log
//                DatabaseCron::$instance->execute("INSERT INTO $db.log_cron (`id`, `date`, `output`) VALUES (NULL, current_timestamp(), '$output')");
            }
        }
    }
}