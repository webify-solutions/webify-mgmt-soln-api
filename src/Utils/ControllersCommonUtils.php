<?php

namespace App\Utils;

use APP\Controller\ApiController;

use APP\Exception\BadRequestException;
use APP\Exception\DatabaseErrorException;

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
}
