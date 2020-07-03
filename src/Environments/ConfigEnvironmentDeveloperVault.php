<?php

namespace ProvisionsGroup\ConfigSync\Environments;

use ProvisionsGroup\ConfigSync\Connections\ConnectionVault;

class ConfigEnvironmentDeveloperVault extends ConfigEnvironmentBase
{
    /**
     * @var mixed
     */
    private $config;

    /**
     * @var mixed
     */
    private $environment;

    /**
     * @param $environment
     */
    public function __construct( $environment )
    {
        $this->environment = $environment;
        $this->config      = config( 'config-sync.environments.'.$environment );
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $credentials
     * @return mixed
     */
    public function getEnvironmentConnection( $credentials ): ConnectionVault
    {
        $connectionVault = new ConnectionVault( $this->config['base_uri'] );
        if ( $this->getConfig()['auth'] === 'userpass' ) {
            $connectionVault->connectByVaultUserPass( $credentials['username'], $credentials['password'], $this->config['auth_path'] );
        } elseif ( $this->getConfig()['auth'] === 'ldap' ) {
            $connectionVault->connectByLdapUserPass( $credentials['username'], $credentials['password'], $this->config['auth_path'] );
        }

        return $connectionVault;
    }
}
