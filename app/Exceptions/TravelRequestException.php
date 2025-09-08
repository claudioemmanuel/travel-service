<?php

namespace App\Exceptions;

use Exception;

class TravelRequestException extends Exception
{
    public function __construct($message = 'Travel request exception')
    {
        parent::__construct($message);
    }
}
