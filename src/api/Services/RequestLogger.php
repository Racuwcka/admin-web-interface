<?php

namespace api\Services;

use Core\Services\Ftp;

class RequestLogger implements \Requests\Core\Interfaces\RequestLogger
{
    public function send(string $tag, string $uri, array $args, $result): void
    {
        try {
            $path = str_replace('/', '_', trim(parse_url($uri)['path'], '/'));
            $filename = $tag . '_' . $path . '_' . gmdate('Y-m-d H_i_s') . '.json';

            $data = [
                'uri' => $uri,
                'args' => $args,
                'result' => str_replace(["\r", "\n", '"'], ['', '', "'"], $result)
            ];

            $file = fopen("php://temp", 'r+');
            fwrite($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            rewind($file);

            if (!Ftp::fput(filename: "request_logs/$filename", stream: $file)) {
                return;
            }

            fclose($file);
        }
        catch (\Throwable) {
            if (isset($file) && $file) {
                fclose($file);
            }
            return;
        }
    }
}