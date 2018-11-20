<?php

namespace App\Http\Controllers;
use Throwable;

class ApiException extends \Exception
{
    //
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
