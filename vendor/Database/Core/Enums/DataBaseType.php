<?php

namespace Database\Core\Enums;

enum DataBaseType
{
    case main;
    case data;
    case mdm;
    //Для трансфера данный
    case data_prod;
    case data_debug;
}