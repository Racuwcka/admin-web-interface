<?php

namespace api\Core\Models\Warehouse;

use api\Core\Repositories\Warehouse\DTO\WarehouseDTO;

class Warehouse {
    public function __construct(
        public string $id,
        public string $name,
        public bool $address,
        public bool $retail,
        public bool $reciept,
        public bool $storage,
        public string $region,
        public bool $consolidated,
        public bool $virtual,
        public ?string $parent,
        public bool $supportPackage,
        public bool $supportAcceptPackage
    ) {}

    public static function fromDTO(WarehouseDTO $data): ?self
    {
        try {
            return new self(
                id: $data->id,
                name: $data->name,
                address: $data->address == 1,
                retail: $data->retail == 1,
                reciept: $data->reciept == 1,
                storage: $data->storage == 1,
                region: $data->region,
                consolidated: $data->consolidated == 1,
                virtual: $data->virtual == 1,
                parent: $data->parent,
                supportPackage: $data->supportPackage == 1,
                supportAcceptPackage: $data->supportAcceptPackage == 1
            );
        }
        catch (\Exception $e) {
            return null;
        }
    }
}
