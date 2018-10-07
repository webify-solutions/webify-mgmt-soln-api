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
    $user = RoutersCommonUtils::processRequest($request, 'login', 'POST', $this->database, $this->logger);

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
  return RoutersCommonUtils::prepareSuccessResponse($response, $user, 200, $this->logger);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/logout"), function (Request $request, Response $response, array $args)
{
  $this->logger->info("Requesting logout");
  try {
    $message = RoutersCommonUtils::processRequest($request, 'logout', 'GET', $this->database, $this->logger, true);

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
    $customers = RoutersCommonUtils::processRequest($request, 'getCustomers', 'GET', $this->database, $this->logger, true);

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
  return RoutersCommonUtils::prepareSuccessResponse($response, $customers, 200, $this->logger);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/products"), function (Request $request, Response $response, array $args)
{
  $this->logger->info("Requesting products");
  try {
    $products = RoutersCommonUtils::processRequest($request, 'getProducts', 'GET', $this->database, $this->logger, true);

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
  return RoutersCommonUtils::prepareSuccessResponse($response, $products, 200, $this->logger);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/technicians"), function (Request $request, Response $response, array $args)
{
  $this->logger-info('Requesting technicians');
  try {
    $products = RoutersCommonUtils::processRequest($request, 'getTechnicians', 'GET', $this->database, $this->logger, true);

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
  return RoutersCommonUtils::prepareSuccessResponse($response, $products, 200, $this->logger);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/issues"), function (Request $request, Response $response, array $args)
{
  $this->logger->info("Requesting issues");
  try {
    $issues = RoutersCommonUtils::processRequest($request, 'getIssues', 'GET', $this->database, $this->logger, true);

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
  return RoutersCommonUtils::prepareSuccessResponse($response, $issues, 200, $this->logger);
});

$app->post(str_replace("{base_path}", base_path, "{base_path}/issues"), function (Request $request, Response $response, array $args)
{
  $this->logger->info("Posting issue");

  return $response->withStatus(501);
});

$app->patch(str_replace("{base_path}", base_path, "{base_path}/issues/{issue_id}"), function (Request $request, Response $response, array $args)
{
  $this->logger->info(str_replace("{issue_id}", $args['issue_id'], "Patching issue {issue_id}"));

  return $response->withStatus(501);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}"), function (Request $request, Response $response, array $args)
{
    // Sample log message
    $this->logger->info("Requesting API definition");

    // Render index view
    return $this->renderer->render($response, "index.html", $args);
});
