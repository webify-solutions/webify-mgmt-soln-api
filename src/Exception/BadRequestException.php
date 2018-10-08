<?php

namespace App\Exception;

use Exception;

class BadRequestException extends Exception
{
  public function __construct (string $message = "", int $code = 0, Throwable $previous = NULL)
  {
    parent::__construct($message, $code, $previous);
  }
}
