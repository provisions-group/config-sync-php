<?php

namespace ProvisionsGroup\ConfigSync\Connections;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Jippi\Vault\ServiceFactory;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ConnectException;
use ProvisionsGroup\ConfigSync\Connections\ConnectionBase;

class ConnectionVault extends ConnectionBase
{
  private $client;
  private $connection;

  public function __construct($baseUri, $apiVersion="v1") {
    $this->client = new Client(["base_uri" => "{$baseUri}/{$apiVersion}/", 'timeout'  => 5.0]);
  }

  public function connectByToken(string $vaultToken) {
    $options['headers']['X-Vault-Token'] = $vaultToken;
    try {
      $response = json_decode($this->client->post("auth/token/create", $options)->getBody());
    }
    catch(ConnectException $ce) {
      Log::channel("stderr")->error("Cannot connect to {$this->client->getConfig()['base_uri']->getHost()}.");
      Log::channel("stderr")->error("Connection timed out.  Is the Vault Container running? Do you need to VPN?");
      exit();
    }
    $this->setClientAndConnectionFromResponse($response);
  }

  public function connectByLdapUserPass(string $username, string $password) {
    $options['json']['password'] = $password;

    try {
      $response = json_decode($this->client->post("auth/ldap/login/{$username}", $options)->getBody());
    }
    catch(ConnectException $ce) {
      Log::channel("stderr")->error("Cannot connect to {$this->client->getConfig()['base_uri']->getHost()}.");
      Log::channel("stderr")->error("Connection timed out.  Is the Vault Container running? Do you need to VPN?");
      exit();
    }
     $this->setClientAndConnectionFromResponse($response);
  }

  public function connectByK8sJwt(string $jwt, string $role) {
    $options['json']['jwt'] = $jwt;
    $options['json']['role'] = $role;

    try {
      $response = json_decode($this->client->post("auth/kubernetes/login", $options)->getBody());
    }
    catch(ConnectException $ce) {
      Log::channel("stderr")->error("Cannot connect to {$this->client->getConfig()['base_uri']->getHost()}.");
      Log::channel("stderr")->error("Connection timed out.  Is the Vault Container running? Do you need to VPN?");
      exit();
    }
     $this->setClientAndConnectionFromResponse($response);
  }

  private function setClientAndConnectionFromResponse($response) {
    if($response != null) {
      //set the default header to use for future requests
      $clientConfig = $this->client->getConfig();
      $clientConfig['headers']['X-Vault-Token'] = $response->auth->client_token; 
      $this->client = new Client($clientConfig);
      //populate the connection
      $this->connection = $response;
    }
  }

  public function getClient() {
    return $this->client;
  }

  public function getConnection() {
    return $this->connection;
  }
}