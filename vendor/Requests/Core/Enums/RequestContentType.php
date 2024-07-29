<?php

namespace Requests\Core\Enums;

enum RequestContentType: string
{
    case textJson = "text/json";
    case multipartFormData = "multipart/form-data";
}