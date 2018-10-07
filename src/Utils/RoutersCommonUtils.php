<?php

namespace App\Utils;

use APP\Controller\ApiController;

use APP\Exception\BadRequestException;

use Slim\Http\Response;
use Slim\Http\Request;

use \Medoo\Medoo;

use Monolog\Logger;

class RoutersCommonUtils
{
  static function getTokenHeader($request)
  {
    // $this->logger->info('Retrieving token header');
    $token = $request->getHeader('X-Token');
    $count = sizeof($token);
    // $this->logger->info('Token header count ' . $count);
    if ($count !== 1) {
      throw new BadRequestException('X-Token header is missing');
    }

    return $token[0];
  }

  static function processRequest(Request $request, string $methodName, string $httpMethod, Medoo $database, Logger $logger, $useTokenHeader = false)
  {
    $controller = new ApiController($database, $logger);
    if ($httpMethod === 'GET' && $useTokenHeader == true) {
      // $logger->info('GET Using token header');
      $entity = $controller->$methodName($request->getQueryParams(), RoutersCommonUtils::getTokenHeader($request));

    } else if ($httpMethod !== 'GET' && $useTokenHeader == true) {
      // $logger->info('Using token header');
      $entity = $controller->$methodName($request->getParsedBody(), RoutersCommonUtils::getTokenHeader($request));

    } else if ($httpMethod !== 'GET') {
      // $logger->info('Not using token header');
      $entity = $controller->$methodName($request->getParsedBody());
    } else {
      // $logger->info(json_encode($queryParams))
      $entity = $controller->$methodName($request->getQueryParams());
    }

    return $entity;
  }

  static function prepareErrorResponse(Response $response, string $message, int $httpStatusCode, $loggers)
  {
    return $response->withJson(["message" => $message], $httpStatusCode);
  }

  static function prepareSuccessResponse(Response $response, array $data, int $httpStatusCode, $logger)
  {
    return $response->withJson($data, $httpStatusCode);
  }
}
