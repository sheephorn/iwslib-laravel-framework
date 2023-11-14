<?php

namespace IwslibLaravel\Codes;

enum EnvironmentName: string
{
    case TEST = 'testing';
    case LOCAL = 'local';
    case STAGING = 'staging';
    case PRODUCTOIN = 'production';
}
