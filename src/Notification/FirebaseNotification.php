<?php

namespace App\FirebaseNotification;

class FirebaseNotification
{
  protected $deviceToken;
  protected $apiAccessKey;
  protected $apiURL;

  public public function __construct($deviceToken)
  {
    $this->deviceToken = $deviceToken;
    $this->apiAccessKey = 'AAAAVWj2ErU:APA91bHAKAqrl67wpDTbDC1aGa4BIbBx3eCEfO4QcEsNNbbR1ReJNZv7g-4-X1jHrXCOolyVebmpdTZXR7AqRi9_NBHSLKleaJihYjuueI4s_OcEpngwzLirNW5tjlNnXlLSvlRd-d';
    $this->apiURL = 'https://fcm.googleapis.com/fcm/send';
  }

  public function sendFirebaseNotification($title, $body, $data)
  {
    #API access key from Google API's Console
    define( 'API_ACCESS_KEY', $this->deviceToken );

    #prep the bundle
    $msg = array
    (
        'body' => $body,
        'title'	=> $title
    );

   $fields = array
   (
       'priority' => 'high',
       'to' => $deviceToken,
       'notification' => $msg,
       'data' => $data
   );

    $headers = array
    (
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, $this->apiURL );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $results = curl_exec($ch );
    curl_close( $ch );

    return $results;
  }
}
