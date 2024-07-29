<?php

namespace api\Core;

use api\Core\Classes\ApiResponse;
use api\Core\Models\Integration\Integration;
use api\Services\RequestLogger;
use Core\Services\EnvService;
use Requests\Modules\Logistics\RequestLogisticsIndex;
use Requests\Modules\LID\RequestLIDIndex;
use Requests\Modules\OneC\RequestOneCIndex;

class Request
{
    public static RequestOneCIndex $instanceOneC;
    public static RequestLogisticsIndex $instanceLogistics;
    public static RequestLIDIndex $instancePassportLichi;

    public static function instanceOneC(): RequestOneCIndex
    {
        if (!isset(self::$instanceOneC)) {
            ApiResponse::error("1cIntegrationIsNotConnected");
        }
        return self::$instanceOneC;
    }

    public static function instanceLogistics(): RequestLogisticsIndex
    {
        if (!isset(self::$instanceLogistics)) {
            ApiResponse::error("LogisticsIntegrationIsNotEnabled");
        }
        return self::$instanceLogistics;
    }

    public static function instancePassportLichi(): RequestLIDIndex
    {
        if (!isset(self::$instancePassportLichi)) {
            ApiResponse::error("LichiPassportIntegrationIsNotEnabled");
        }
        return self::$instancePassportLichi;
    }

    public static function set(
        Integration $integration,
        bool $prod_1c,
        bool $prod_logistic,
        string $lang,
        ?string $userName,
        ?string $warehouseId): void
    {
        $requestLogger = new RequestLogger();

        $lichi_id_config = EnvService::get(
            path: CONFIG_SRC . '/.env.lichiid',
            keys: ['lichi_id_client']
        );

        if (isset($lichi_id_config['lichi_id_client'])) {
            $lid = new RequestLIDIndex();
            $lid->params->set(
                clientId: $lichi_id_config['lichi_id_client']);
            $lid->send->setLogger($requestLogger);
            self::$instancePassportLichi = $lid;
        }

        if (!is_null($integration->oneC)) {
            if (!is_null($userName) && !is_null($warehouseId)) {
                $oneC = new RequestOneCIndex();
                $oneC->params->set(
                    url: $prod_1c ? $integration->oneC->production_url : $integration->oneC->debug_url,
                    login: $prod_1c ? $integration->oneC->production_login : $integration->oneC->debug_login,
                    password: $prod_1c ? $integration->oneC->production_password : $integration->oneC->debug_password,
                    user: $userName,
                    warehouse: $warehouseId,
                    lang: $lang
                );
                $oneC->send->setLogger($requestLogger);
                self::$instanceOneC = $oneC;
            }
        }

        if (!is_null($integration->logistics)) {
            $logistics = new RequestLogisticsIndex();
            $logistics->params->set(
                url: $prod_logistic ? $integration->logistics->production_url : $integration->logistics->debug_url,
                lang: $lang,
                userData: 'S3sRASJrGAt0e3ZzR1d1AGgbbw15HwcTIzkOChJjHiMGK2tjeA0SdRJjZmVXXSEkBD97NUABcwtgXSUZCChvNlFCcHcBPFtkFit0Y00'
            );
            $logistics->send->setLogger($requestLogger);
            self::$instanceLogistics = $logistics;
        }
    }
}