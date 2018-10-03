<?php
return [
  'settings' => [
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header

    // Renderer settings
    'renderer' => [
      'template_path' => __DIR__ . '/../templates/',
    ],

    // Monolog settings
    'logger' => [
      'name' => 'slim-app',
      'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
      'level' => \Monolog\Logger::DEBUG,
    ],

    /**
    * Connection information used by the ORM to connect
    * to your application's datastores.
    *
    */
    'Datasources' => [
      'className' => '',
      'driver' => 'mysql',
      'persistent' => false,
      'host' => 'onnjomlc4vqc55fw.chr7pe7iynqr.eu-west-1.rds.amazonaws.com',

      //'port' => 'non_standard_port_number',
      'username' => 'duv2xuiuk7ql3pph',
      'password' => 'wk5zi4u457mown5q',
      'name' => 'b0fp0i70l498lbvh',
      /*
      * You do not need to set this flag to use full utf-8 encoding (internal default since CakePHP 3.6).
      */
      //'encoding' => 'utf8mb4',
      'timezone' => 'UTC',
      'flags' => [],
      'cacheMetadata' => true,
      'log' => false,

      /**
      * Set identifier quoting to true if you are using reserved words or
      * special characters in your table or column names. Enabling this
      * setting will result in queries built using the Query Builder having
      * identifiers quoted when creating SQL. It should be noted that this
      * decreases performance because each query needs to be traversed and
      * manipulated before being executed.
      */
      'quoteIdentifiers' => true,

      /**
      * During development, if using MySQL < 5.6, uncommenting the
      * following line could boost the speed at which schema metadata is
      * fetched from the database. It can also be set directly with the
      * mysql configuration directive 'innodb_stats_on_metadata = 0'
      * which is the recommended value in production environments
      */
      //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],

      'url' => null,
    ]
  ]
];
