<?php

namespace api\Core\Models\UserAuth;

use Core\Localizations\Localizations;

class UserInfo
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $photo,
        public ?string $birthday,
        public ?string $gender
    ) {}

    public static function getFullName(string $firstName, string $lastName, string $secondName): string
    {
        $name = $firstName . ' ' . $lastName . ' ' . $secondName;
        if (empty(trim($name))) {
            $name = Localizations::get('noName');
        }
        return $name;
    }
}