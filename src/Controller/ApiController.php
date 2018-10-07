<?php

namespace App\Controller;

use APP\Exception\BadCredentialsException;
use APP\Exception\BadRequestException;
use APP\Exception\DatabaseErrorException;
use APP\Exception\UnauthorizedException;

use App\Utils\ControllersCommonUtils;

use PDO;
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
    if (in_array($user['role'], ['Admin', 'Technician']) === false) {
      throw new UnauthorizedException("You're not authorized to access this resource");
    }

    // $this->logger->info($organizationId);
    $customers = $this->database->select(
      'customer',
      ['id', "customer_name" =>  Medoo::raw("CONCAT(customer_number, ': ', name)")],
      ['organization_id' => $user['organization_id']]
    );
    // $this->logger->info(json_encode($customers));
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $customers, $this->logger);

    return $customers;
  }

  public function getProducts($queryParams, $token) {
    $user = $this->login(["token" => $token]);
    if (in_array($user['role'], ['Admin', 'Technician', 'Customer']) === false) {
      throw new UnauthorizedException("You're not authorized to access this resource");
    }

    $customerNumber = null;
    // $this->logger->info($user['role'] == 'Customer'? 'true' : 'false');
    if ($user['role'] === 'Customer') {
      $customerNumber = $user['login_name'];
    } else {
      $customerNumber = $queryParams['customer_number'];
      if ($customerNumber === null) {
        throw new BadCredentialsException("customer_number query parameter is missing");
      }
    }

    $organizationId = $user['organization_id'];
    $queryString = "
      SELECT p.id, p.name AS product_name, MAX(o.order_date) AS ordered_date
      FROM product p
      INNER JOIN order_item oi ON (oi.product_id = p.id)
      INNER JOIN `order` o on (o.id = oi.order_id)
      INNER JOIN customer c on (c.id = o.customer_id)
      WHERE c.customer_number = '" . $customerNumber . "' AND p.organization_id = " . $organizationId . "
      GROUP BY p.id, p.name;";
    // $this->logger->info($queryString);
    $productQuery = $this->database->query($queryString);

    $products = $productQuery->fetchAll(PDO::FETCH_ASSOC);

    // $this->logger->info(json_encode($products));
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $products, $this->logger);

    return $products;
  }
}
