<?php
$config = [];

$config['database'] = [

  'ip' => '127.0.0.1',

  'port' => 3306,

  'encoding' => 'utf-8',

  'dbname' => 'gbot_free',

  'type' => 'mysql',

  'login' => 'serveradmin',

  'passwd' => 'admin1234',

  'enabled' => false,
];

/*
 Tworzymy tablicę $config[1]
*/
$config[1] = [
  'conn' => [

    'ip' => '127.0.0.1',

    'voicePort' => 9987,

    'queryPort' => 10011,

    'login' => 'serveradmin',

    'passwd' => 'Cepolekj1',

    'channelId' => 5,

    'delay' => 1,

    'botName' => '@Aktualizator',
  ],

  'events' => [

    'enabled' => ['test'],

    'intervals' => [
      'test' => 3,
    ],

    'cfg' => [

    ],
  ],

  'plugins' => [

    'enabled' => [],

    'cfg' => [

    ],
  ],
];
 ?>
