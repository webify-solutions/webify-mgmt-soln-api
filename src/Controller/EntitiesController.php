<?php

namespace App\Controller;

use APP\Exception\BadCredentialsException;
use APP\Exception\BadRequestException;
use APP\Exception\DatabaseErrorException;
use APP\Exception\UnauthorizedErrorException;

use \Medoo\Medoo;

use Monolog\Logger;

class EntitiesController
{
  protected $database;
  protected $logger;

  public function __construct(Medoo $database, Logger $logger) {
    $this->database = $database;
    $this->logger = $logger;
  }

  public function login($data)
  {
    $where = ['active'=> true];
    if (isset($data['login_name']) && isset($data['password'])) {
      $where['login_name'] = $data['login_name'];
      // $where['password'] = $data['password'];
    } else if (isset($data['token'])) {
      $where['mobile_token'] = $data['token'];
    } else {
      throw new BadRequestException('User credentials are missing');
    }

    $users = $this->database->select(
      'user',
      [
        'id',
        'organization_id',
        'login_name',
        'name',
        'password',
        'phone',
        'email',
        'role'
      ],
      $where
    );

    // $this->logger->info($users === []);
    // $this->logger->info($users[0]['password']);
    // $this->logger->info($data['password']);
    // $this->logger->info(password_verify($data['password'], $users[0]['password']));
    if ($users === [] || password_verify($data['password'], $users[0]['password']) === false) {
      throw new BadCredentialsException('Invalid Credentials');
    }

    $user = $users[0];
    // $this->logger->info(json_encode($user));
    $results = $this->database->update(
      'user',
      [
        'mobile_token' => $data['token']
      ],
      [
        'id' => $user['id']
      ]
    );
    // $this->logger->info($this->database->isSuccess($results) === false ? 'false' : 'true');
    if ($this->database->isSuccess($results) === false) {
      foreach ($this->database->error() as $error) {
        $this->logger->error($error);
      }

      throw new DatabaseErrorException(implode("\n",$this->database->error()));
    }

    return $user;
  }
}
