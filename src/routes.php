<?php

use App\Controller\ApiController;

use APP\Exception\BadCredentialsException;
use APP\Exception\BadRequestException;
use APP\Exception\DatabaseErrorException;
use APP\Exception\UnauthorizedException;

use App\Utils\RoutersCommonUtils;

use Slim\Http\Request;
use Slim\Http\Response;

const base_path = '/api/v1';

// Routes

$app->get(str_replace("{base_path}", base_path, "{base_path}/status"), function (Request $request, Response $response, array $args)
{
  $this->get('logger')->info("Requesting API status");
  $message = [
    "message" => "System Online!"
  ];
  $this->logger->info("System Online!");
  return $response->withJson($message);
});

$app->post(str_replace("{base_path}", base_path, "{base_path}/login"), function (Request $request, Response $response, array $args)
{
  $this->logger->info("Requesting login");
  try {
    $message = RoutersCommonUtils::processRequest($request, $args, 'login', $this->database, $this->logger);

  } catch (BadCredentialsException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 401, $this->logger);
  } catch (BadRequestException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 400, $this->logger);
  } catch (UnauthorizedException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 403, $this->logger);
  } catch (Exception $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 500, $this->logger);
  }

  // $this->logger->info(json_encode($user));
  return RoutersCommonUtils::prepareSuccessResponse($response, $message, 200, $this->logger);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/logout"), function (Request $request, Response $response, array $args)
{
  $this->logger->info("Requesting logout");
  try {
    $message = RoutersCommonUtils::processRequest($request, $args, 'logout', $this->database, $this->logger);

  } catch (BadCredentialsException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 401, $this->logger);
  } catch (BadRequestException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 400, $this->logger);
  } catch (UnauthorizedException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 403, $this->logger);
  } catch (Exception $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 500, $this->logger);
  }

  // $this->logger->info(json_encode($message));
  return RoutersCommonUtils::prepareSuccessResponse($response, $message, 200, $this->logger);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/customers"), function (Request $request, Response $response, array $args)
{
  $this->logger->info("Requesting customers");
  try {
    $message = RoutersCommonUtils::processRequest($request, $args, 'getCustomers', $this->database, $this->logger);

  } catch (BadCredentialsException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 401, $this->logger);
  } catch (BadRequestException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 400, $this->logger);
  } catch (UnauthorizedException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 403, $this->logger);
  } catch (Exception $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 500, $this->logger);
  }

  // $this->logger->info(json_encode($message));
  return RoutersCommonUtils::prepareSuccessResponse($response, $message, 200, $this->logger);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/products"), function (Request $request, Response $response, array $args)
{
  $this->logger->info("Requesting products");
  try {
    $message = RoutersCommonUtils::processRequest($request, $args, 'getProducts', $this->database, $this->logger);

  } catch (BadCredentialsException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 401, $this->logger);
  } catch (BadRequestException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 400, $this->logger);
  } catch (UnauthorizedException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 403, $this->logger);
  } catch (Exception $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 500, $this->logger);
  }

  // $this->logger->info(json_encode($message));
  return RoutersCommonUtils::prepareSuccessResponse($response, $message, 200, $this->logger);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/technicians"), function (Request $request, Response $response, array $args)
{
  $this->logger->info('Requesting technicians');
  try {
    $message = RoutersCommonUtils::processRequest($request, $args, 'getTechnicians', $this->database, $this->logger);

  } catch (BadCredentialsException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 401, $this->logger);
  } catch (BadRequestException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 400, $this->logger);
  } catch (UnauthorizedException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 403, $this->logger);
  } catch (Exception $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 500, $this->logger);
  }

  // $this->logger->info(json_encode($message));
  return RoutersCommonUtils::prepareSuccessResponse($response, $message, 200, $this->logger);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/issues"), function (Request $request, Response $response, array $args)
{
  $this->logger->info("Requesting issues");
  try {
    $message = RoutersCommonUtils::processRequest($request, $args, 'getIssues', $this->database, $this->logger);

  } catch (BadCredentialsException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 401, $this->logger);
  } catch (BadRequestException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 400, $this->logger);
  } catch (UnauthorizedException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 403, $this->logger);
  } catch (Exception $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 500, $this->logger);
  }

  // $this->logger->info(json_encode($message));
  return RoutersCommonUtils::prepareSuccessResponseWithMetadata($response, $message, 200, $this->logger);
});

$app->post(str_replace("{base_path}", base_path, "{base_path}/issues"), function (Request $request, Response $response, array $args)
{
  $this->logger->info("Posting issue");
  try {
    $message = RoutersCommonUtils::processRequest($request, $args, 'createIssue', $this->database, $this->logger);

  } catch (BadCredentialsException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 401, $this->logger);
  } catch (BadRequestException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 400, $this->logger);
  } catch (UnauthorizedException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 403, $this->logger);
  } catch (Exception $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 500, $this->logger);
  }

  // $this->logger->info(json_encode($user));
  return RoutersCommonUtils::prepareSuccessResponse($response, $message, 201, $this->logger);
});

$app->patch(str_replace("{base_path}", base_path, "{base_path}/issues/{issue_id}"), function (Request $request, Response $response, array $args)
{
  $this->logger->info(str_replace("{issue_id}", $args['issue_id'], "Patching issue {issue_id}"));

  try {
    $message = RoutersCommonUtils::processRequest($request, $args, 'updateIssue', $this->database, $this->logger);

  } catch (BadCredentialsException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 401, $this->logger);
  } catch (BadRequestException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 400, $this->logger);
  } catch (UnauthorizedException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 403, $this->logger);
  } catch (Exception $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 500, $this->logger);
  }

  // $this->logger->info(json_encode($user));
  return RoutersCommonUtils::prepareSuccessResponse($response, $message, 200, $this->logger);
});

$app->put(str_replace("{base_path}", base_path, "{base_path}/user/device/token"), function (Request $request, Response $response, array $args)
{
  $this->logger->info("Updateing user device token");

  try {
    $message = RoutersCommonUtils::processRequest($request, $args, 'updateUserDeviceToken', $this->database, $this->logger);

  } catch (BadCredentialsException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 401, $this->logger);
  } catch (BadRequestException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 400, $this->logger);
  } catch (UnauthorizedException $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 403, $this->logger);
  } catch (Exception $e) {
    return RoutersCommonUtils::prepareErrorResponse($response, $e->getMessage(), 500, $this->logger);
  }

  // $this->logger->info(json_encode($user));
  return RoutersCommonUtils::prepareSuccessResponse($response, $message, 200, $this->logger);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}"), function (Request $request, Response $response, array $args)
{
    // Sample log message
    $this->logger->info("Requesting API definition");

    // Render index view
    return $this->renderer->render($response, "index.html", $args);
});
