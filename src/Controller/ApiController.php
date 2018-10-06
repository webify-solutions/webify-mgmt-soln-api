<?php

namespace App\Controller;

use APP\Exception\BadCredentialsException;
use APP\Exception\BadRequestException;
use APP\Exception\DatabaseErrorException;
use APP\Exception\UnauthorizedException;

use App\Utils\ControllersCommonUtils;

use \Medoo\Medoo;

use Monolog\Logger;

class ApiController
{
  protected $database;
  protected $logger;

  public function __construct(Medoo $database, Logger $logger) {
    $this->database = $database;
    $this->logger = $logger;
  }

  public function getTokenHeader($request)
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

  public function login($data)
  {
    $where = ['active'=> true];
    if (isset($data['login_name']) && isset($data['password']) && isset($data['token'])) {
      $where['login_name'] = $data['login_name'];
      // $where['password'] = $data['password'];
    } else if (isset($data['token'])) {
      $where['mobile_token'] = $data['token'];
    } else {
      throw new BadRequestException('User credentials are missing');
    }
    // $this->logger->info(json_encode($where));

    $user = $this->database->get(
      'user',
      ['id', 'organization_id', 'login_name', 'name', 'password', 'mobile_token',
        'phone', 'email', 'role'],
      $where
    );
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $user, $this->logger);

    // $this->logger->info(json_encode($user));
    // $this->logger->info(password_verify($data['password'], $user['password']));
    if (
      $user == null || (
          isset($data['password'])
          && password_verify($data['password'], $user['password']) === false
        )
    ) {
      throw new BadCredentialsException('Invalid Credentials');
    }

    if (
      isset($data['login_name'])
      && isset($data['password'])
      && $data['token'] !== $user['mobile_token']
    ) {
      // $this->logger->info('Updating ' . $data['id'] . ' user mobile token');
      $results = $this->database->update(
        'user',
        ['mobile_token' => $data['token']],
        ['id' => $user['id']]
      );
      ControllersCommonUtils::validateDatabaseExecResults($this->database, $results, $this->logger);
    }
    unset($user['password']);
    unset($user['mobile_token']);
    return $user;
  }

  public function logout($token)
  {
    $this->logger->info('Logging out user mobile token ' . $token);
    $results = $this->database->update(
      'user', ['mobile_token' => null], ['mobile_token' => $token]
    );
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $results, $this->logger);

    if ($results->rowCount() <= 0) {
        throw new UnauthorizedException('Invalid Token');
    }
    return ["message" => "Logout successfully"];
  }

  public function getCustomers($queryParams, $token) {
    $user = $this->login(["token" => $token]);
    // $this->logger->info(json_encode($user));
    if ($user['role'] === 'Customer') {
      throw new UnauthorizedException("You're not authorized to access this resource");
    }

    $organizationId = $user['organization_id'];
    $this->logger->info($organizationId);
    $customers = $this->database->select(
      'customer',
      ['id', "customer_name" =>  Medoo::raw("CONCAT(customer_number, ': ', name)")],
      ['organization_id' => $organizationId]
    );
    $this->logger->info(json_encode($customers));
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $customers, $this->logger);

    return $customers;
  }
}
