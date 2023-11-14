<?php

namespace IwslibLaravel\Codes;

enum HTTPResultCode: int
{
    case SECCESS = 0;
    case FAILED = 1;
    case UNAUTHORIZED = 2;
    case EXCLUSIVE_ERROR = 3;
}
