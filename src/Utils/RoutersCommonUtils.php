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

  static function processRequest(Request $request, array $args, string $methodName, Medoo $database, Logger $logger)
  {
    $controller = new ApiController($database, $logger);
    $data = $request->getOriginalMethod() === 'GET' ? $request->getQueryParams() : $request->getParsedBody();
    $user = null;

    if ($methodName === 'login') {
      $response = $controller->$methodName($data, $args);
    } else if ($methodName === 'logout') {
      $token = RoutersCommonUtils::getTokenHeader($request);
      $response = $controller->$methodName($data, $args, $token);
    } else {
      $token = RoutersCommonUtils::getTokenHeader($request);
      $user = $controller->login(['token' => $token]);
      // $this->logger->info(json_encode($user));
      $response = $controller->$methodName($data, $args, $user);
    }

    return ['response' => $response, 'user' => $user];
  }

  static function prepareErrorResponse(Response $response, string $message, int $httpStatusCode, $loggers)
  {
    return $response->withJson(["message" => $message], $httpStatusCode);
  }

  static function prepareSuccessResponse(Response $response, $message, int $httpStatusCode, $logger)
  {
    return $response->withJson($message['response'], $httpStatusCode);
  }

  static function prepareSuccessResponseWithMetadata(Response $response, $message, int $httpStatusCode, $logger)
  {
    $newMessage = [
      'user_role' => $message['user']['role'],
      'data' => $message['response']
    ];
    return $response->withJson($newMessage, $httpStatusCode);
  }
}
