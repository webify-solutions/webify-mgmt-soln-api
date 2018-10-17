<?php

namespace App\Controller;

use APP\Exception\BadCredentialsException;
use APP\Exception\BadRequestException;
use APP\Exception\DatabaseErrorException;
use APP\Exception\UnauthorizedException;

use App\Utils\ControllersCommonUtils;
use App\Notification\FirebaseNotification;

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

  public function login($data, $args=null)
  {
    $where = ['user.active'=> true];
    if (isset($data['login_name']) && isset($data['password']) && isset($data['token'])) {
      $where['user.login_name'] = $data['login_name'];
      // $where['password'] = $data['password'];
    } else if (isset($data['token'])) {
      $where['mobile_token'] = $data['token'];
    } else {
      throw new BadRequestException('User credentials are missing');
    }
    // $this->logger->info(json_encode($where));
    $user = $this->database->get(
      'user',
      [
        '[>]customer' => ['login_name' => 'login_name']
      ],
      ['user.id', 'user.organization_id', 'user.login_name', 'user.name', 'password', 'mobile_token',
        'device_token', 'user.phone', 'user.email', 'role', 'customer.id(customer_id)'],
      $where
    );

    // $this->logger->info(json_encode( $this->database->log() ));
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

  public function logout($queryParams, $args, $token)
  {
    // $this->logger->info('Logging out user mobile token ' . $token);
    $results = $this->database->update(
      'user', ['mobile_token' => null], ['mobile_token' => $token]
    );
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $results, $this->logger);

    if ($results->rowCount() <= 0) {
        throw new UnauthorizedException('Invalid Token');
    }
    return ['message' => 'Logout successfully'];
  }

  public function getCustomers($queryParams, $args, $user)
  {
    if (in_array($user['role'], ['Admin', 'Technician']) === false) {
      throw new UnauthorizedException("You're not authorized to access this resource");
    }
    // $this->logger->info($organizationId);
    $customers = $this->database->select(
      'customer',
      ['id', 'login_name', 'customer_name' =>  Medoo::raw("CONCAT(customer_number, ': ', name)")],
      ['organization_id' => $user['organization_id']]
    );
    // $this->logger->info(json_encode($customers));
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $customers, $this->logger);

    return ControllersCommonUtils::skipOnNull($customers, $this->logger);;
  }

  public function getProducts($queryParams, $args, $user)
  {
    if (in_array($user['role'], ['Admin', 'Technician', 'Customer']) === false) {
      throw new UnauthorizedException("You're not authorized to access this resource");
    }
    $customerId = null;
    // $this->logger->info($user['role'] == 'Customer'? 'true' : 'false');
    if ($user['role'] === 'Customer') {
      $customerId = $user['customer_id'];
    } else {
      $customerId = $queryParams['customer_id'];
      if ($customerId === null) {
        throw new BadRequestException('customer_login_name query parameter is missing');
      }
    }

    $organizationId = $user['organization_id'];
    $queryString = "
      SELECT p.id, p.name AS product_name, MAX(o.order_date) AS ordered_date
      FROM product p
      INNER JOIN order_item oi ON (oi.product_id = p.id)
      INNER JOIN `order` o on (o.id = oi.order_id)
      WHERE o.customer_id = '" . $customerId . "' AND p.organization_id = " . $organizationId . "
      GROUP BY p.id, p.name;";
    // $this->logger->info($queryString);
    $productQuery = $this->database->query($queryString);

    $products = $productQuery->fetchAll(PDO::FETCH_ASSOC);

    // $this->logger->info(json_encode($products));
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $products, $this->logger);

    return $products;
  }

  public function getTechnicians($queryParams, $args, $user)
  {
    if (in_array($user['role'], ['Admin']) === false) {
      throw new UnauthorizedException("You're not authorized to access this resource");
    }
    // $this->logger->info($organizationId);
    $technicians = $this->database->select(
      'user',
      ['id', 'name(technician_name)'],
      ['organization_id' => $user['organization_id'], 'role' => 'Technician']
    );

    // $this->logger->info(json_encode($customers));
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $technicians, $this->logger);

    return $technicians;
  }

  public function getIssues($queryParams, $args, $user)
  {
    if (in_array($user['role'], ['Admin', 'Technician', 'Customer']) === false) {
      throw new UnauthorizedException("You're not authorized to access this resource");
    }

    if ($user['role'] === 'Customer') {
      $customer_id = $user['customer_id'];
    } else if ($user['role'] === 'Technician') {
      $technician_id = $user['id'];
    }

    $organization_id = $user['organization_id'];
    $queryString = "
      SELECT i.id, i.`subject` AS title, i.description, i.customer_id,
        CONCAT(customer_number, ' : ', c.`name`) AS customer_name, c.address,
        c.longitude, c.latitude, i.product_id, p.`name` AS product_name,
        o.order_date as ordered_date,  i.technician_id, t.`name` as technician_name, i.`status`
      FROM issues i
      INNER JOIN customer c ON (c.id = i.customer_id)
      INNER JOIN product p ON (p.id = i.product_id)
      INNER JOIN order_item oi ON (oi.product_id = p.id)
      INNER JOIN `order` o ON (o.id = oi.order_id)
      LEFT JOIN user t ON (t.id = i.technician_id)
      WHERE i.organization_id = " . $organization_id;

    if (isset($customer_id)) {
      $queryString .= " AND i.customer_id = " . $customer_id;
    } else if (isset($technician_id)) {
      $queryString .= " AND i.technician_id = " . $technician_id;
    }
    $queryString .= " GROUP BY i.id";
    $queryString .= " ORDER BY i.created_at DESC";

    // $this->logger->info($queryString);
    $issuesQuery = $this->database->query($queryString);
    $issues = $issuesQuery->fetchAll(PDO::FETCH_ASSOC);
    // $this->logger->info(json_encode($this->database->log()));

    // $this->logger->info('Query results: ' . json_encode($issues));
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $issues, $this->logger);

    return ControllersCommonUtils::skipOnNull($issues, $this->logger);
  }

  public function createIssue($data, $args, $user)
  {
    if (in_array($user['role'], ['Admin', 'Technician', 'Customer']) === false) {
      throw new UnauthorizedException("You're not authorized to access this resource");
    }
    $organization_id = $user['organization_id'];
    // Data mapping
    $dataMapping = [];
    $dataMapping['organization_id'] = $organization_id;

    if ($user['customer_id'] !== null) {
      $dataMapping['customer_id'] = $user['customer_id'];
    } else if (isset($data['customer_id'])) {
      $dataMapping['customer_id'] = $data['customer_id'];
    } else {
      throw new BadRequestException('customer_number field is missing in the JSON request');
    }

    if (isset($data['title'])) { $dataMapping['subject'] = $data['title']; }
    if (isset($data['description'])) { $dataMapping['description'] = $data['description']; }
    if (isset($data['product_id'])) { $dataMapping['product_id'] = $data['product_id']; }
    if (isset($data['technician_id'])) { $dataMapping['technician_id'] = $data['technician_id']; }
    if (isset($data['status'])) { $dataMapping['status'] = $data['status']; }

    // $this->logger->info(json_encode($dataMapping));
    $results = $this->database->insert("issues",
      $dataMapping
    );
    // $this->logger->info('Query results: ' . json_encode($issues));
    // $this->logger->info(json_encode($products));
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $results, $this->logger);

    $data['id'] = $this->database->id();

    if ($data['status'] === 'Draft') {
      $this->logger->info('Notify all admins of new issue');
      // $this->logger->info($organizationId);
      $admins = $this->database->select(
        'user',
        ['id', 'name(admin_name)', 'device_token'],
        ['organization_id' => $user['organization_id'], 'role' => 'Admin']
      );
      // $this->logger->info(json_encode($admins));
      foreach ($admins as $admin) {
        if ($admin['device_token'] !== null) {
          $firebaseNotification = new FirebaseNotification($admin['device_token'], $this->logger);
          $this->logger->info($firebaseNotification->getDeviceToken());
          $results = $firebaseNotification->sendFirebaseNotification(
              'New Issue Created',
              'A new issue has been created in your organization ',
              [
                'title' => 'New Issue Created',
                'body' => 'A new issue has been created in your organization ',
                'id' => $data['id']
              ]
          );
          unset($results);
          unset($firebaseNotificaiton);
        }
      }
      unset($admins);
    }

    return $data;
  }

  public function updateIssue($data, array $args, $user)
  {
    if (in_array($user['role'], ['Admin', 'Technician', 'Customer']) === false) {
      throw new UnauthorizedException("You're not authorized to access this resource");
    }

    $organization_id = $user['organization_id'];
    // Data mapping
    $dataMapping = [];
    $dataMapping['organization_id'] = $organization_id;

    if (isset($data['customer_id'])) {
      throw new BadRequestException("Can't update the customer issue in the JSON request");
    }
    if (isset($data['product_id'])) {
      throw new BadRequestException("Can't update the product issue in the JSON request");
    }

    if (isset($data['title'])) { $dataMapping['subject'] = $data['title']; }
    if (isset($data['description'])) { $dataMapping['description'] = $data['description']; }
    if (isset($data['technician_id'])) { $dataMapping['technician_id'] = $data['technician_id']; }
    if (isset($data['status'])) { $dataMapping['status'] = $data['status']; }

    // $this->logger->info(json_encode($dataMapping));
    $results = $this->database->update("issues",
      $dataMapping,
      [
        'id' => $args['issue_id']
      ]
    );

    $organization_id = $user['organization_id'];
    $queryString = "
    SELECT i.id, i.`subject` AS title, i.description, i.customer_id,
      cu.device_token as customer_device_token, i.technician_id,
      t.device_token as technician_device_token, i.`status`
    FROM issues i
    INNER JOIN customer c on (c.id = i.customer_id)
    LEFT JOIN user cu ON (cu.login_name = c.login_name)
    LEFT JOIN user t ON (t.id = i.technician_id)
      WHERE i.id = " . $args['issue_id'];
    $queryString .= " GROUP BY i.id";
    $queryString .= " ORDER BY i.created_at DESC LIMIT 1";

    // $this->logger->info($queryString);
    $issuesQuery = $this->database->query($queryString);
    $issue = $issuesQuery->fetchAll(PDO::FETCH_ASSOC)[0];

    // $this->logger->info($issue);
    if ($data['status'] === 'Assigned')
    {
      // $this->logger->info('Notify technician of new assignment ' . $issue['customer_device_token']);
      $firebaseNotification = new FirebaseNotification($issue['technician_device_token'], $this->logger);
      $results = $firebaseNotification->sendFirebaseNotification(
          'New Issue Assigned',
          "A new issue has been assigned to you",
          [
              'id' => $args['issue_id']
          ]
      );
    } else if ($data['status'] === 'PendingCustomerApproval')
    {
      // $this->logger->info('Notify customer of issue completed ' . $issue['customer_device_token']);
      $firebaseNotification = new FirebaseNotification($issue['customer_device_token'], $this->logger);
      $results = $firebaseNotification->sendFirebaseNotification(
          'تم حل المشكلة',
          "Your issue '" . $issue['title'] . "' has been resolved. Please close issue if you're satisfied",
          [
            'title' => 'تم حل المشكلة',
            'body' => "Your issue '" . $issue['title'] . "' has been resolved. Please close issue if you're satisfied",
            'id' => $args['issue_id']
          ]
      );

      // $this->logger->info($results);
    }

    // $this->logger->info('Query results: ' . json_encode($issues));
    // $this->logger->info(json_encode($products));
    ControllersCommonUtils::validateDatabaseExecResults($this->database, $results, $this->logger);
    return $data;
  }

  public function updateUserDeviceToken($data, array $args, $user)
  {
    if (isset($data['device_token'])) {
      // $this->logger->info('Updating ' . $data['id'] . ' user mobile token');
      $results = $this->database->update(
        'user',
        ['device_token' => $data['device_token']],
        ['id' => $user['id']]
      );
      ControllersCommonUtils::validateDatabaseExecResults($this->database, $results, $this->logger);
    } else {
      throw new BadRequestException("device_token field is missing in the JSON request");
    }

    return $data;
  }
}
