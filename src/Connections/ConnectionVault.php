<?php

namespace CashExpress\ConfigSync\Connections;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Jippi\Vault\ServiceFactory;
use Illuminate\Support\Facades\Log;
use CashExpress\ConfigSync\Connections\ConnectionBase;

class ConnectionVault extends ConnectionBase
{
  private $client;
  private $connection;

  public function __construct($baseUri, $apiVersion="v1") {
    $this->client = new Client(["base_uri" => "{$baseUri}/{$apiVersion}/"]);
  }

  public function connectByToken(string $vaultToken) {
    $options['headers']['X-Vault-Token'] = $vaultToken;
    $response = json_decode($this->client->post("auth/token/create", $options)->getBody());
    if($response != null) {
      //set the default header to use for future requests
      $clientConfig = $this->client->getConfig();
      $clientConfig['headers']['X-Vault-Token'] = $response->auth->client_token;
      $this->client = new Client($clientConfig);
      //populate the connection
      $this->connection = $response;
    }
  }

  public function connectByLdapUserPass(string $username, string $password) {
    $options = [
      'json' => [
          "password" => "{$password}"
         ]
     ];
    Log::channel("stderr")->info($this->client->post("/auth/ldap/login/{$username}", $options));
  }

  public function getClient() {
    return $this->client;
  }

  public function getConnection() {
    return $this->connection;
  }
}