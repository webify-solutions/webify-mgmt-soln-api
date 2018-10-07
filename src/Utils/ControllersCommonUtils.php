<?php

namespace App\Utils;

use APP\Controller\ApiController;

use APP\Exception\BadRequestException;

use Slim\Http\Response;
use Slim\Http\Request;

use \Medoo\Medoo;

use Monolog\Logger;

class ControllersCommonUtils
{
  public static function validateDatabaseExecResults($database, $results, $logger)
  {
    // $this->logger->info($this->database->isSuccess($results) === false ? 'false' : 'true');
    if ($database->isSuccess($results) === false) {
      foreach ($database->error() as $error) {
        $logger->error($error);
      }
      throw new DatabaseErrorException(implode("\n",$database->error()));
    }

    return;
  }

  public static function resultsFilter($results, $logger) {
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
