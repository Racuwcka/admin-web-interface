<?php

namespace api\Core\Models\Scope;
use api\Core\Interfaces\ScopeInterface;

class ScopeName implements ScopeInterface {
    public function __construct(
        public string $name,
        public string $lastName,
        public string $secondName
    ) {}

    static function fromJson(?array $json): ?ScopeName
    {
        try {
            if (!isset($json['NAME']) || !isset($json['LAST_NAME']) || !isset($json['SECOND_NAME'])) {
                return null;
            }

            return new ScopeName(
                name: $json['NAME'],
                lastName: $json['LAST_NAME'],
                secondName: $json['SECOND_NAME']);
        }
        catch (\Exception $e) {
            return null;
        }
    }
}