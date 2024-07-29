<?php

namespace api\Core\Mappers\DTO;

use api\Core\Repositories\Session\DTO\SessionDataItemDTO;

class SessionDataItemDTOMapper
{
    /**
     * @param string $sessionId
     * @param string|null $warehouse
     * @return array<SessionDataItemDTO>
     */
    public static function toList(
        string $sessionId,
        ?string $warehouse
    ): array
    {
        return [
            new SessionDataItemDTO(
                data_id: md5($sessionId . "warehouse"),
                session_id: $sessionId,
                attr: "warehouse",
                value: $warehouse
            )
        ];
    }
}