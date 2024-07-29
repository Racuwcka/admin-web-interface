<?php

namespace api\Core\Models\Scope;
use api\Core\Interfaces\ScopeInterface;

class ScopePhoto implements ScopeInterface {
    public function __construct(public ?string $photo) {}

    static function fromJson(?array $json): ?ScopePhoto
    {
        try {
            return new ScopePhoto(
                photo: $json[1] ?? null);
        }
        catch (\Exception $e) {
            return null;
        }
    }
}