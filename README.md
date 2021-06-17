# config-sync

Config Sync is a generic PHP Composer package that can be extended to support multiple backends - where today it only supports HashiCorp Vault - whose purpose is to synchronize a set of backend secrets data to a locally encrypted file.  Compared to a typical PHP Laravel apps, the design goals of the Config Sync are to,

1. Replace the .env file; especially for secrets
1. Replace the ENVIRONMENT variables; especially for secrets
1. Decrease coupling for an app when accessing secrets or configuration data from a backend
1. Support Kubernetes Pod secrets/configuration bootstrapping while still supporting local development

## Prerequisites

- [composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
- [PHP](https://www.php.net/manual/en/install.php)
- access to somewhere that HashiCorp vault is hosted
- Configuration stored in developer mount in Vault
- `developer` environment (see below) will require additional setup in your `.env` file

## Command

```
php artisan config:sync --backend=vault --environment=developer
```

## Options

There are four options that can be specified:

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

The `developer` environment will create a Config Safe from your developer mount in Vault. The first time, you will need to add the following to your local `.env` file:

> VAULT_MOUNT=location of Vault mount

> VAULT_SECRET=vault secret name

When authenticating with Vault, Config Sync assumes that you will use your LDAP username and password, but this can be changed in the config.

### kubernetes environment

The `kubernetes` environment will create a Config Safe from the Vault in the environment where the applicaiton is running. This will use the JWT (Kubernetes ServiceAccount token) and the role (i.e. auditor-portal) to authenticate with Vault and fetch the secret for that role to create the Config Safe.
