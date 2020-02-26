<?php

return [
  'backends' => [
    'vault' => [
      'class' => ProvisionsGroup\ConfigSync\Backends\ConfigBackendVault::class,
    ],
  ],

  'environments' => [
    'local' => [
      'class' => ProvisionsGroup\ConfigSync\Environments\ConfigEnvironmentLocalVault::class,
      'base_uri' => env('VAULT_ADDR', 'http://localhost:8200'),
      'mount_to_sync' => 'auditor-portal',
      'secret_to_sync' => 'local/developer',
      'api_version' => 'v1',
      'auth' => 'token', //can be token, password, or kubernetes
      'token' => 'HEY12345', //only used if authType = token
      'sealwrap' => false,
      'config_file_path' => env('APP_CONFIG_FILE','./config.safe.env')
    ],
    'developer' => [
      'class' => ProvisionsGroup\ConfigSync\Environments\ConfigEnvironmentDeveloperVault::class,
      'base_uri' => env('VAULT_ADDR', 'https://vault.provisionsgroupllc.com'),
      'mount_to_sync' => env('VAULT_MOUNT',''),
      'secret_to_sync' => env('VAULT_SECRET',''),
      'api_version' => 'v1',
      'auth' => 'ldap', //can be token, ldap, or kubernetes
      'sealwrap' => false,
      'config_file_path' => env('APP_CONFIG_FILE','./config.safe.env')
    ],
    'kubernetes' => [
      'class' => ProvisionsGroup\ConfigSync\Environments\ConfigEnvironmentKubernetesVault::class,
      'base_uri' => env('VAULT_ADDR', 'https://vault.provisionsgroupllc.com'),
      'mount_to_sync' => env('VAULT_MOUNT',''),
      'secret_to_sync' => env('VAULT_SECRET',''),
      'api_version' => 'v1',
      'auth' => 'kubernetes', //can be token, ldap, or kubernetes
      'role' => 'auditor-portal',
      'sealwrap' => false,
      'config_file_path' => env('APP_CONFIG_FILE','./config.safe.env')
    ],
  ]
];
