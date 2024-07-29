<?php

namespace api\Core\Enums;

enum ScopeType: string {
    case userId = 'user.id';
    case userPhoto = 'user.photo';
    case userData = 'user.data';
    case userName = 'user.name';
}