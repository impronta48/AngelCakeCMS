<?php

use Cake\Core\Configure;

return [
  'Filesystem' => [
    'default' => [
      'adapter' => 'Local', // default
      'adapterArguments' => [WWW_ROOT .  Configure::read('sitedir')]
    ],
    'images' => [
      'adapter' => 'Local', // default
      'adapterArguments' => [WWW_ROOT .  Configure::read('sitedir') . DS . 'img']
    ],
  ]
];
