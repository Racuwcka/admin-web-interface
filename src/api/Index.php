<?php

namespace api;

use api\Core\Classes\ApiResponse;
use api\Core\Classes\DataBase;
use api\Core\Classes\HandledError;
use api\Core\Enums\ResponseType;
use api\Core\Models\Result;
use api\Core\Repositories\HttpToken\HttpTokenRepository;
use api\Core\Request;
use api\Core\Storage\SessionStorage;
use api\Core\Storage\VariableStorage;
use api\Services\FetchService;
use api\Services\IntegrationService;
use api\Services\SessionService;
use api\Services\ThrowableLogger;

if (($_SERVER['REQUEST_METHOD'] ?? false) === 'OPTIONS') {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 1728000');
    header('Content-Length: 0');
    header('Content-Type: */*; charset=utf-8');
    die();
}

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Credentials: true');
}
header('Content-type: application/json; charset=utf-8');
error_reporting(E_ERROR);
ini_set('display_startup_errors', 1);
ini_set('display_errors', '1');

class Index {
    public function __construct(private readonly string $module, private readonly string $method) {
        try {
            $this->setUpVariable();
            $this->setUpDatabase();
            $this->authenticateRequest();
            $this->setUpRequest();
            $this->handleRequest();
        } catch (HandledError $e) {
            ApiResponse::error($e->getMessage());
        } catch (\Throwable $e) {
            ThrowableLogger::catch($e);
            ApiResponse::error("system.unexpected_error");
        }
    }

    private function setUpVariable(): void
    {
        VariableStorage::$token = $_REQUEST['auth'] ?? '';
        VariableStorage::$lang = strtolower($_REQUEST['lang'] ?? '');
        VariableStorage::$responseType = ResponseType::tryFrom($_REQUEST['response_type']) ?? ResponseType::json;
    }

    private function setUpDatabase(): void
    {
        DataBase::setUp(Config::$prod_db);
    }

    private function authenticateRequest(): void
    {
        if (!empty(VariableStorage::$token)) {
            if (!SessionService::getSession()) {
                throw new HandledError("accessDenied");
            }
        }
        else {
            $allowedEndpoints = [
                "passportLichi/register",
                "passportLichi/getAuthInfo"
            ];

            $endpoint = $this->module . "/" . $this->method;

            if (!in_array($endpoint, $allowedEndpoints)) {
                $token = md5(substr($_SERVER['HTTP_AUTHORIZATION'], 7));

                if (!HttpTokenRepository::get($token)) {
                    throw new HandledError("accessDenied");
                }
            }
        }
    }

    private function setUpRequest(): void
    {
        $integration = IntegrationService::get(region: SessionStorage::hasWarehouse() ? SessionStorage::warehouse()->region : null);

        Request::set(
            integration: $integration,
            prod_1c: Config::$prod_1c,
            prod_logistic: Config::$prod_logistic,
            lang: VariableStorage::$lang,
            userName: SessionStorage::has() ? SessionStorage::user()->name : null,
            warehouseId: SessionStorage::hasWarehouse() ? SessionStorage::warehouse()->id : null
        );
    }

    private function handleRequest(): void
    {
        $controllerPath = 'api\\Controllers\\' . ucfirst($this->module) . 'Controller';
        if (!class_exists($controllerPath)) {
            throw new HandledError('moduleDoesNotExist');
        }

        if (!method_exists($controllerPath, $this->method)) {
            throw new HandledError('MethodDoesNotExist');
        }

        $argumentsValue = FetchService::getArgumentsApi($this->method, $controllerPath);
        $response = $controllerPath::{$this->method}(...$argumentsValue);
        if ($response instanceof Result) {
            ApiResponse::send($response);
        }
    }
}