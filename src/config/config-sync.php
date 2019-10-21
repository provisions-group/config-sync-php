<?php

return [
  'backends' => [
    'vault' => [
      'class' => CashExpress\ConfigSync\Backends\ConfigBackendVault::class,
    ],
  ],

  'environments' => [
    'local' => [
      'class' => CashExpress\ConfigSync\Environments\ConfigEnvironmentLocalVault::class,
      'base_uri' => 'http://' . env("VAULT", "localhost") . ':8200',
      'mount_to_sync' => 'auditor-portal',
      'secret_to_sync' => 'local/developer',
      'api_version' => 'v1',
      'auth' => 'token', //can be token, password, or kubernetes
      'token' => 'HEY12345', //only used if authType = token
      'sealwrap' => false,
      'config_file_path' => env('APP_CONFIG_FILE','./config.safe.env')
    ],
    'developer' => [
      'class' => CashExpress\ConfigSync\Environments\ConfigEnvironmentDeveloperVault::class,
      'auth' => 'password', //can be token, password, or kubernetes
      'sealwrap' => false,
    ],
  ]
];
