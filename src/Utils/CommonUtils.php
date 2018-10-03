<?php

namespace App\Utils;

use Slim\Http\Response;

class CommonUtils
{
  static function prepareErrorResponse(Response $response, string $message, int $httpStatusCode) {
    return $response
            ->withJson(["message" => $message])
            ->withStatus($httpStatusCode);
  }
}
