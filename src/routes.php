<?php

use Slim\Http\Request;
use Slim\Http\Response;

const base_path = '/api/v1';

// Routes

$app->get(str_replace("{base_path}", base_path, "{base_path}/status"), function (Request $request, Response $response, array $args) {
  $this->logger->info("Requesting API status");
  $message = [
    "message" => "System Online!"
  ];
  $this->logger->info("System Online!");
  return $response->withJson($message);
});

$app->post(str_replace("{base_path}", base_path, "{base_path}/login"), function (Request $request, Response $response, array $args) {
  $this->logger->info("Requesting login");

  return $response->withStatus(501);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/logout"), function (Request $request, Response $response, array $args) {
  $this->logger->info("Requesting logout");

  return $response->withStatus(501);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/customers"), function (Request $request, Response $response, array $args) {
  $this->logger->info("Requesting customers");

  return $response->withStatus(501);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/products"), function (Request $request, Response $response, array $args) {
  $this->logger->info("Requesting products");

  return $response->withStatus(501);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/technicians"), function (Request $request, Response $response, array $args) {
  $this->logger->info("Requesting technicians");

  return $response->withStatus(501);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}/issues"), function (Request $request, Response $response, array $args) {
  $this->logger->info("Requesting issues");

  return $response->withStatus(501);
});

$app->post(str_replace("{base_path}", base_path, "{base_path}/issues"), function (Request $request, Response $response, array $args) {
  $this->logger->info("Posting issue");

  return $response->withStatus(501);
});

$app->patch(str_replace("{base_path}", base_path, "{base_path}/issues/{issue_id}"), function (Request $request, Response $response, array $args) {
  $this->logger->info(str_replace("{issue_id}", $args['issue_id'], "Patching issue {issue_id}"));

  return $response->withStatus(501);
});

$app->get(str_replace("{base_path}", base_path, "{base_path}"), function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Requesting API definition");

    // Render index view
    return $this->renderer->render($response, "index.html", $args);
});
