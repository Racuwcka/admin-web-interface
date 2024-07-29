<?php

namespace api\Services;

use api\Core\Repositories\Trace\TraceRepository;
use Core\Services\Ftp;

class TraceService
{
    public static function getList(): array
    {
        $traceList = TraceRepository::getList();

        foreach ($traceList as $trace) {
            $result[$trace->terminalId][] = $trace->name;
        }
        return $result ?? [];
    }

    public static function download(string $name): bool
    {
        try {
            header("Access-Control-Expose-Headers: Content-Disposition");
            header('Content-Type: application/zip');
            header("Content-Disposition: attachment; filename=$name");

            if (Ftp::fget(local_filename: 'php://output', remote_filename: "tracing/$name")) {
                return true;
            }

            return false;
        }
        catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            return false;
        }
    }
}