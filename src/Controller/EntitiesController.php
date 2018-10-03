<?php

namespace App\Controller;

use APP\Exception\BadCredentialsException;
use APP\Exception\BadRequestException;
use APP\Exception\DatabaseErrorException;
use APP\Exception\UnauthorizedErrorException;

use \Medoo\Medoo;

class EntitiesController
{
  protected $database;

  public function __construct(Medoo $database) {
    $this->database = $database;
  }

  public function login($data)
  {
    $where = ['active'=> true];
    if (isset($data['login_name']) && isset($data['password'])) {
      $where['login_name'] = $data['login_name'];
      $where['password'] = $data['password'];
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
        'phone',
        'email',
        'role'
      ],
      $where
    );

    if ($users == []) {
      throw new BadCredentialsException('Invalid Credentials');
    }

    $user = $users[0];
    $results = $this->database->update(
      'user',
      [
        'mobile_token' => $token
      ],
      [
        'id' => $user['id']
      ]
    );
    if (!$this->database->isSuccess($results)) {
      throw new DatabaseErrorException(implode("\n",$database->error()));
    }

    return $user;
  }
}
