<?php

namespace api\Core\Models\Scope;

use api\Core\Interfaces\ScopeInterface;

class ScopeId implements ScopeInterface {
    public function __construct(public int $id) {}

    static function fromJson(?array $json): ?ScopeId
    {
        if (!isset($json['ID'])) {
            return null;
        }

        try {
            return new ScopeId(
                id: $json['ID']);
        }
        catch (\Exception $e) {
            return null;
        }
    }
}