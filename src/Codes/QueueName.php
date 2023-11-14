<?php

namespace IwslibLaravel\Codes;

enum QueueName: string
{
    case EMAIL = 'email';
    case JOB = 'job';
}
