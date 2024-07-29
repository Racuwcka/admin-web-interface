<?php

namespace api\Core\Enums;

enum AuthType: string {
    case authSuccess = 'authSuccess';
    case authRequired = 'authRequired';
}