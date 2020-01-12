# config-sync

This repo contains a PHP library used for creating a Config Safe for the business applications that will be running either on your laptop or in one of the Kubernetes clusters deployed in AWS.

## Prerequisites

- [composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
- [PHP](https://www.php.net/manual/en/install.php)
- access to https://vault.cashexpressllc.com through either
  - Cash Express network access
  - VPN access into the environment
- domain account in AWS based Cash Express LLC domain
- Configuration stored in developer mount in Vault
- See Mike Rabius if you need a developer account or access to VPN
- `developer` environment (see below) will require additional setup in your `.env` file

## Command

```
php artisan config:sync --backend=vault --environment=developer
```

## Options

There are two options that can be specified:

```
--backend=vault : The backend to use for config values
--environment=developer : The backend environment to use
--watch : Flag that keeps the process running rather than running once
--refresh=10 : Only used if the --watch flag is set; frequency of checking with backend and refreshing file
```

Currently, the only option for `--backend` is `vault` and the default is `vault` so it is not required to specify this option when running the command. The code was written in such a way, that should another backend be chosen in the future, as long as it aligns with the interface, it could easily be swapped out in lieu of Vault.

The `--environment` option has three choices:

- `local`
- `developer`
- `kubernetes`

### local environment

The `local` environment will create a Config Safe from your local Vault server using the Vault auth token to authenticate and retrieve the secret to create the Config Safe for your local running application.

### developer environment

The `developer` environment will create a Config Safe from your developer mount in Vault. This requires that Mike Rabius has setup your developer mount in the https://vault.cashexpressllc.com Vault. The first time, you will need to add the following to your local `.env` file:

> VAULT_MOUNT=auditor-portal/kv/local/your.username
> VAULT_SECRET=app

When authenticating with Vault, you will use your AD username and password, then you will have access to the appropriate developer mount.

### kubernetes environment

The `kubernetes` environment will create a Config Safe from the Vault in the environment where the applicaiton is running (i.e. https://vault-dev.cashexpressllc.com if the application is running in the DEV environment). This will use the JWT (Kubernetes ServiceAccount token) and the role (i.e. auditor-portal) to authenticate with Vault and fetch the secret for that role to create the Config Safe.
