<?php

namespace api\Services;

use api\Core\Enums\MessageType;
use api\Core\Enums\ScopeType;
use api\Core\Models\Message;
use api\Core\Models\Result;
use api\Core\Models\Scope\ScopeData;
use api\Core\Models\Scope\ScopeId;
use api\Core\Models\Scope\ScopeList;
use api\Core\Models\Scope\ScopeName;
use api\Core\Models\Scope\ScopePhoto;
use api\Core\Request;
use Core\Services\EnvService;
use Core\Services\WhiteList;

class LichiIdService
{
    public static function get(?string $token): ?ScopeList
    {
        if (!$token) {
            return null;
        }

        $config = EnvService::get(
            path: CONFIG_SRC . '/.env.lichiid',
            keys: ['lichi_id_secret', 'lichi_id_scopes']
        );

        if (!$config) {
            return null;
        }

        $token = hash('sha256', $token.$config['lichi_id_secret']);
        $request = Request::instancePassportLichi()->common->get($token);

        if (!$request->status) {
            return null;
        }

        $scopesValue = $request->data;
        $scopes = [];

        foreach ($config['lichi_id_scopes'] as $scopeKey) {
            $scopeName = $scopeKey['scope_name'];

            if (!array_key_exists($scopeName, $scopesValue)) {
                return null;
            }
            $scopeType = ScopeType::tryFrom($scopeName);

            $scopeValue = $scopesValue[$scopeName];

            $scopeData = null;

            switch ($scopeType) {
                case ScopeType::userId:
                    $scopeData = ScopeId::fromJson($scopeValue);
                    break;
                case ScopeType::userPhoto:
                    $scopeData = ScopePhoto::fromJson($scopeValue);
                    break;
                case ScopeType::userData:
                    $scopeData = ScopeData::fromJson($scopeValue);
                    break;
                case ScopeType::userName:
                    $scopeData = ScopeName::fromJson($scopeValue);
                    break;
            }

            if (!$scopeData) {
                return null;
            }

            $scopes[$scopeType->value] = $scopeData;
        }

        return new ScopeList(
            id: $scopes['user.id'],
            info: $scopes['user.name'],
            photo: $scopes['user.photo'],
            data: $scopes['user.data']
        );
    }

    public static function scopeArray(array $configScopes): array
    {
        $scopes = [];

        foreach ($configScopes as $item) {
            $scopes[] = ($item['required'] ? '!' : '') . $item['scope_name'];
        }
        return $scopes;
    }
}