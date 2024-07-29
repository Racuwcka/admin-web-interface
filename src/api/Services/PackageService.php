<?php

namespace api\Services;

class PackageService {

    public static function download(int $id, string $root): bool
    {
        header("Access-Control-Expose-Headers: Content-Disposition");
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="packages.pdf"');
        readfile($root);

        if (!unlink($root)) {
            return false;
        }

        return rmdir(ROOT_DIRECTORY . "/tmp/packages_$id");
    }
}