<?php

namespace api\Core\Models\Scope;
use api\Core\Interfaces\ScopeInterface;

class ScopeData implements ScopeInterface {
    public function __construct(
        public ?string $birthday,
        public ?string $gender
    ) {}

    static function fromJson(?array $json): ?ScopeData
    {
        try {
            return new ScopeData(
                birthday: $json['PERSONAL_BIRTHDAY'] ?? null,
                gender: $json['PERSONAL_GENDER'] ?? null
            );
        }
        catch (\Exception $e) {
            return null;
        }
    }
}