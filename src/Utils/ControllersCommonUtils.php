<?php

namespace App\Utils;

use APP\Controller\ApiController;

use APP\Exception\BadRequestException;
use APP\Exception\DatabaseErrorException;

use App\Notification\FirebaseNotification;

use Slim\Http\Response;
use Slim\Http\Request;

use \Medoo\Medoo;

use Monolog\Logger;

class ControllersCommonUtils
{
  public static function validateDatabaseExecResults($database, $results, $logger)
  {
    $errorDump = $database->error();
    if (isset($errorDump[0]) && $errorDump[0] !== '00000') {
      $logger->error(json_encode($database->log()));
      $logger->error(json_encode($database->error()));

      throw new DatabaseErrorException('Error occured, please contact your adminstrator');
    }

    return;
  }

  public static function skipOnNull($results, $logger) {
    $newResults = [];
    // $logger->info('Filter the results: ' . json_encode($results));
    foreach ($results as $result) {
      $newResult = [];
      foreach ($result as $key => $value) {
        if ($value !== null) {
          $newResult[$key] = $value;
        }
      }

      if ($newResult !== []) {
        $newResults[] = $newResult;
      }
    }

    return $newResults;
  }

  public static function broadcastToAllAdmins($message, $organizationId, $database, $logger) {
    $admins = $database->select(
      'user',
      ['id', 'name(admin_name)', 'device_token'],
      ['organization_id' => $organizationId, 'role' => 'Admin']
    );
    // $this->logger->info(json_encode($admins));
    foreach ($admins as $admin) {
      if ($admin['device_token'] !== null) {
        $firebaseNotification = new FirebaseNotification($admin['device_token'], $logger);
        // $this->logger->info($firebaseNotification->getDeviceToken());
        $results = $firebaseNotification->sendFirebaseNotification(
            $message['title'],
            $message['body'],
            $message
        );
        unset($results);
        unset($firebaseNotificaiton);
      }
    }
    unset($admins);
  }
}
