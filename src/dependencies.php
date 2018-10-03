<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['database'] = function($c) {
    $dbconfig = $c->get('settings')['Datasources'];
    return new \Medoo\Medoo([
        'database_type' => 'mysql',
        'database_name' => $dbconfig['name'],
        'server' => $dbconfig['host'],
        'username' => $dbconfig['username'],
        'password' => $dbconfig['password']
    ]);
};
