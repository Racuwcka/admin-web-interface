<?php

namespace api\Core\Enums;

enum MessageType: string {
    case error = 'error';
    case warning = 'warning';
    case success = 'success';
}