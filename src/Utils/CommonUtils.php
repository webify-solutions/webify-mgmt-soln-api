<?php

namespace App\Utils;

use APP\Controller\EntitiesController;

use Slim\Http\Response;
use Slim\Http\Request;

use \Medoo\Medoo;

use Monolog\Logger;

class CommonUtils
{
  function processRequest(Request $request, string $methodName, Medoo $database, Logger $logger)
  {
    $controller = new EntitiesController($database, $logger);

    $parsedBody = $request->getParsedBody();
    $entity = $controller->$methodName($parsedBody);

    return $entity;
  }

  static function prepareErrorResponse(Response $response, string $message, int $httpStatusCode)
  {
    return $response
            ->withJson(["message" => $message])
            ->withStatus($httpStatusCode);
  }

  static function prepareSuccessResponse(Response $response, array $data, int $httpStatusCode)
  {
    return $response
            ->withJson($data)
            ->withStatus($httpStatusCode);
  }
}
