<?php

namespace Core\Services;

use FTP\Connection;

class Ftp
{
    private static ?Connection $instance = null;

    private function __construct(){}

    private function __clone(){}

    /**
     * @throws \Exception
     */
    private static function get(): Connection
    {
        if (is_null(self::$instance)) {
            $config = EnvService::get(
                path: CONFIG_SRC . '/.env.ftp.storage',
                keys: ['host', 'user', 'password']
            );

            if (!$config) {
                throw new \Exception();
            }

            $ftp_conn = ftp_connect($config['host']);
            if (!$ftp_conn) {
                throw new \Exception();
            }

            if (!ftp_login($ftp_conn, $config['user'], $config['password'])) {
                throw new \Exception();
            }

            if (!ftp_pasv($ftp_conn, true)) {
                throw new \Exception();
            }

            self::$instance = $ftp_conn;
        }
        return self::$instance;
    }

    public static function fput(string $filename, $stream): bool
    {
        try {
            return ftp_fput(
                ftp: self::get(),
                remote_filename: $filename,
                stream: $stream,
                mode: FTP_ASCII
            );
        }
        catch (\Throwable) {
            return false;
        }
    }

    public static function put(string $remote_filename, string $local_filename): bool
    {
        try {
            return ftp_put(
                ftp: self::get(),
                remote_filename: $remote_filename,
                local_filename: $local_filename
            );
        }
        catch (\Throwable) {
            return false;
        }
    }

    public static function nlist(string $directory): array
    {
        try {
            return ftp_nlist(ftp: self::get(), directory: $directory);
        }
        catch (\Throwable) {
            return [];
        }
    }

    public static function delete(string $filename): bool
    {
        try {
            return ftp_delete(ftp: self::get(), filename: $filename);
        }
        catch (\Throwable) {
            return false;
        }
    }

    public static function fget(string $local_filename, string $remote_filename): bool
    {
        try {
            return ftp_get(
                ftp: self::get(),
                local_filename: $local_filename,
                remote_filename: $remote_filename
            );
        }
        catch (\Throwable) {
            return false;
        }
    }
}