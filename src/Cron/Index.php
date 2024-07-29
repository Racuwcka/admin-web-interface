<?php

namespace Cron;

use Cron\Core\Classes\Scheduler;
use Cron\Core\Models\CronTask;

class Index {
    public function __construct()
    {
        $cron = new Scheduler();

        $cron->add(
            expression: '*/1 * * * *',
            cronTask: new CronTask(
                module: "package",
                method: "do"
            ));
        $cron->run();
    }
}