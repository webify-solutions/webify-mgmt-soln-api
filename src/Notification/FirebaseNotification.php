<?php

namespace App\Notification;

class FirebaseNotification
{
  protected $deviceToken;
  protected $logger;
  protected $apiAccessKey;
  protected $apiURL;

  public function __construct($deviceToken, $logger)
  {
    // $logger->info('Initialize new Firebase Notification');
    $this->deviceToken = $deviceToken;
    $this->logger = $logger;
    $this->apiAccessKey = 'AIzaSyD2oRpLTBAQHHLIi9KbD4gqFMrmGtYjBz0';
    $this->apiURL = 'https://fcm.googleapis.com/fcm/send';
    // $logger->info('Initilized new Firebase Notification');
  }

  public function getDeviceToken()
  {
    return $this->deviceToken;
  }

  public function sendFirebaseNotification($title, $body, $data)
  {
    #API access key from Google API's Console
    define( 'API_ACCESS_KEY', $this->apiAccessKey );

    #prep the bundle
    $msg = array
    (
        'body' => $body,
        'title'	=> $title
    );
    // $this->logger->info(json_encode($msg));

    $fields = array
    (
       'priority' => 'high',
       'to' => $this->deviceToken,
       'notification' => $msg,
       'data' => $data
    );
    // $this->logger->info(json_encode($fields));

    $headers = array
    (
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
    );
    // $this->logger->info(json_encode($headers));

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, $this->apiURL );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    // $this->logger->info($ch);
    $results = curl_exec($ch );
    $this->logger->info($results);
    curl_close( $ch );

    return $results;
  }
}
