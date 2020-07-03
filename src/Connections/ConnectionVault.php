<?php

namespace ProvisionsGroup\ConfigSync\Connections;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\ConnectException;
use ProvisionsGroup\ConfigSync\Connections\ConnectionBase;

class ConnectionVault extends ConnectionBase
{
    /**
     * @var mixed
     */
    private $client;

    /**
     * @var mixed
     */
    private $connection;

    /**
     * @param $baseUri
     * @param $apiVersion
     */
    public function __construct( $baseUri, $apiVersion = 'v1' )
    {
        $this->client = new Client( ['base_uri' => "{$baseUri}/{$apiVersion}/", 'timeout' => 5.0, 'verify' => false] );
    }

    /**
     * @param string      $jwt
     * @param string      $role
     * @param $authPath
     */
    public function connectByK8sJwt( string $jwt, string $role, $authPath )
    {
        $options['json']['jwt']  = $jwt;
        $options['json']['role'] = $role;

        try {
            $response = json_decode( $this->client->post( "{$authPath}", $options )->getBody() );
        } catch ( ConnectException $ce ) {
            Log::channel( 'stderr' )->error( "Cannot connect to {$this->client->getConfig()['base_uri']->getHost()}." );
            Log::channel( 'stderr' )->error( 'Connection timed out.  Is the Vault Container running? Do you need to VPN?' );
            exit();
        }
        $this->setClientAndConnectionFromResponse( $response );
    }

    /**
     * @param string $username
     * @param string $password
     */
    public function connectByLdapUserPass( string $username, string $password, string $authPath )
    {
        $options['json']['password'] = $password;

        try {
            $response = json_decode( $this->client->post( "{$authPath}/{$username}", $options )->getBody() );
        } catch ( ConnectException $ce ) {
            Log::channel( 'stderr' )->error( "Cannot connect to {$this->client->getConfig()['base_uri']->getHost()}." );
            Log::channel( 'stderr' )->error( 'Connection timed out.  Is the Vault Container running? Do you need to VPN?' );
            exit();
        }
        $this->setClientAndConnectionFromResponse( $response );
    }

    /**
     * @param string $vaultToken
     */
    public function connectByToken( string $vaultToken, string $authPath )
    {
        $options['headers']['X-Vault-Token'] = $vaultToken;
        try {
            $response = json_decode( $this->client->post( "{$authPath}", $options )->getBody() );
        } catch ( ConnectException $ce ) {
            Log::channel( 'stderr' )->error( "Cannot connect to {$this->client->getConfig()['base_uri']->getHost()}." );
            Log::channel( 'stderr' )->error( 'Connection timed out.  Is the Vault Container running? Do you need to VPN?' );
            exit();
        }
        $this->setClientAndConnectionFromResponse( $response );
    }

    /**
     * @param string $username
     * @param string $password
     */
    public function connectByVaultUserPass( string $username, string $password, string $authPath )
    {
        $options['json']['password'] = $password;

        try {
            $response = json_decode( $this->client->post( "{$authPath}/{$username}", $options )->getBody() );
        } catch ( ConnectException $ce ) {
            Log::channel( 'stderr' )->error( "Cannot connect to {$this->client->getConfig()['base_uri']->getHost()}." );
            Log::channel( 'stderr' )->error( 'Connection timed out.  Is the Vault Container running? Do you need to VPN?' );
            exit();
        }
        $this->setClientAndConnectionFromResponse( $response );
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param $response
     */
    private function setClientAndConnectionFromResponse( $response )
    {
        if ( $response !== null ) {
            //set the default header to use for future requests
            $clientConfig                             = $this->client->getConfig();
            $clientConfig['headers']['X-Vault-Token'] = $response->auth->client_token;
            $this->client                             = new Client( $clientConfig );
            //populate the connection
            $this->connection = $response;
        }
    }
}
