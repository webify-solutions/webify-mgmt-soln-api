<?php

namespace App\Utils;

use APP\Controller\ApiController;

use APP\Exception\BadRequestException;

use Slim\Http\Response;
use Slim\Http\Request;

use \Medoo\Medoo;

use Monolog\Logger;

class CommonUtils
{
  static function processRequest(Request $request, string $methodName, string $httpMethod, Medoo $database, Logger $logger, $useTokenHeader = false)
  {
    $controller = new ApiController($database, $logger);
    $parsedBody = $request->getParsedBody();

    if ($httpMethod === 'GET' && $useTokenHeader == true) {
      // $logger->info('GET Using token header');
      $token = $controller->getTokenHeader($request);
      // $logger->info(json_encode($token));

      $entity = $controller->$methodName($token);
    } else if ($httpMethod !== 'GET' && $useTokenHeader == true) {
      // $logger->info('Using token header');
      $token = $controller->getTokenHeader($request);
      // $logger->info(json_encode($token));

      $entity = $controller->$methodName($parsedBody, $token);
    } else if ($httpMethod !== 'GET') {
      // $logger->info('Not using token header');
      $entity = $controller->$methodName($parsedBody);
    } else {
      $entity = $controller->$methodName();
    }

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
