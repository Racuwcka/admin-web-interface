<?php

namespace api\Core\Mappers\DTOToEntity;

use api\Core\Models\Integration\IntegrationResourceData;
use api\Core\Repositories\Integration\DTO\IntegrationResourceDataDTO;

class IntegrationResourceDataMapper
{
    public static function toIntegrationResourceData(IntegrationResourceDataDTO $data): IntegrationResourceData
    {
        return new IntegrationResourceData(
            production_url: $data->production_url,
            production_login: $data->production_login,
            production_password: $data->production_password,
            debug_url: $data->debug_url,
            debug_login: $data->debug_login,
            debug_password: $data->debug_password,
        );
    }
}