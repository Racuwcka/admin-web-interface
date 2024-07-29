<?php

namespace api\Services;

use api\Core\Mappers\DTOToEntity\IntegrationResourceDataMapper;
use api\Core\Models\Integration\Integration;
use api\Core\Repositories\Integration\IntegrationRepository;
use Core\Localizations\Localizations;

class IntegrationService
{
    public static function get(?string $region) : Integration
    {
        $integrationResourceDataListDTO = IntegrationRepository::getList(region: $region);

        $platforms = [];

        foreach ($integrationResourceDataListDTO as $item) {
            if (isset($platforms[$item->platform])) {
                throw new \Error(Localizations::get(
                    keyword: "integrationPlatformIncorrectConfig",
                    values: ["platform" => $item->platform]
                ));
            }

            $platforms[$item->platform] = IntegrationResourceDataMapper::toIntegrationResourceData($item);
        }

        return new Integration(
            oneC: $platforms['1c'] ?? null,
            logistics: $platforms['logistics'] ?? null
        );
    }
}