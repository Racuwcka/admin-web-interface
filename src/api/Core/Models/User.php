<?php
namespace api\Core\Models;

use api\Core\Repositories\Account\DTO\AccountDTO;
use Core\Localizations\Localizations;

class User {
    public function __construct(
        public int     $id,
        public string  $name,
        public string  $firstName,
        public string  $lastName,
        public ?string $secondName,
        public ?string $photo,
        public ?string $birthday,
        public ?string $gender,
        public ?int    $role,
        public ?string $warehouse,
        public bool    $active
    ) {}

    public static function fromDTO(AccountDTO $data): ?User
    {
        try {
            if ($data->first_name === null ||
                $data->last_name === null ||
                $data->gender === null) {
                return null;
            }

            $name = $data->last_name . ' ' . $data->first_name . ' ' . ($data->second_name ?? '');
            if (empty(trim($name))) {
                $name = Localizations::get('noName');
            }

            return new User(
                id: $data->id,
                name: $name,
                firstName: $data->first_name,
                lastName: $data->last_name,
                secondName: $data->second_name,
                photo: $data->photo,
                birthday: $data->birthday,
                gender: $data->gender,
                role: $data->roleId,
                warehouse: $data->warehouse,
                active: boolval($data->active)
            );
        } catch (\Exception $e) {
            return null;
        }
    }
}
