<?php

namespace App\Exceptions;


class ApiExceptions extends \Exception
{
    public function __construct($message, $code = 1000)
    {
        parent::__construct($message, $code);
    }


}