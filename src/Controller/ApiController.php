<?php

namespace App\Controller;

use APP\Exception\BadCredentialsException;
use APP\Exception\BadRequestException;
use APP\Exception\DatabaseErrorException;
use APP\Exception\UnauthorizedException;

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

  public function getTokenHeader($request) {
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
      // $this->logger->info($this->database->isSuccess($results) === false ? 'false' : 'true');
      if ($this->database->isSuccess($results) === false) {
        foreach ($this->database->error() as $error) {
          $this->logger->error($error);
        }
        throw new DatabaseErrorException(implode("\n",$this->database->error()));
      }
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
    // $this->logger->info($this->database->isSuccess($results) === false ? 'false' : 'true');
    if ($this->database->isSuccess($results) === false) {
      foreach ($this->database->error() as $error) {
        $this->logger->error($error);
      }
      throw new DatabaseErrorException(implode("\n",$this->database->error()));
    }

    if ($results->rowCount() <= 0) {
        throw new UnauthorizedException('Invalid Token');
    }
    return ["message" => "Logout successfully"];
  }
}
