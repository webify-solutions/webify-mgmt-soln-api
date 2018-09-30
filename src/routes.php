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

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, "index.phtml", $args);
});
