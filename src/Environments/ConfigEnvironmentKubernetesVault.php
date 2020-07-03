<?php

namespace ProvisionsGroup\ConfigSync\Environments;

use ProvisionsGroup\ConfigSync\Connections\ConnectionVault;

class ConfigEnvironmentKubernetesVault extends ConfigEnvironmentBase
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
        $connectionVault->connectByK8sJwt( $credentials['jwt'], $credentials['role'], $this->config['auth_path'] );

        return $connectionVault;
    }
}
