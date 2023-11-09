# Demo for Symfony IMAP-Bundle

Demo-App for [imap-bundle](https://github.com/secit-pl/imap-bundle)
Which includes [php-imap](https://github.com/barbushin/php-imap) integration for Symfony 4+

## Installation

#### 1. Composer
From the command line run

```
$ composer require dukecity/demo-imap-bundle
```

As long as it is not published by Packagist, please just download the code via GitHub !!!

## Configuration

Setup your mailbox configuration in `config/packages/imap.yaml` and adjust its content.

Create a file ```.env.local``` and set the sensitive Env-Variables.

Here is the example configuration:

```yaml
imap:
  connections:
    example_connection:
      mailbox:  '%env(EXAMPLE_CONNECTION_MAILBOX)%'
      username: '%env(EXAMPLE_CONNECTION_USERNAME)%'
      password: '%env(EXAMPLE_CONNECTION_PASSWORD)%'
```

## Test 
Start server and go to https://127.0.0.1:8000 and see if it works.

```
symfony serve
```

## Validate if the mailboxes can connect correct

```
php bin/console imap-bundle:validate
```

Result:
```
+--------------------------+----------------+-------------------------------+--------------------+
| Connection               | Connect Result | Mailbox                       | Username           |
+--------------------------+----------------+-------------------------------+--------------------+
| example_connection       | SUCCESS        | {imap.strato.de:993/imap/ssl} | user@mail.com      |
| example_WRONG_connection | FAILED         | {imap.strato.de:993/imap/ssl} | WRONG              |
+--------------------------+----------------+-------------------------------+--------------------+
```

This command can take some while if a connect failed. That is because of a long connection-timeout.
If you use this in CI-Pipeline add the parameter `-q`.
Password is not displayed for security reasons.
You can set an array of connections to validate.

```
php bin/console imap-bundle:validate example_connection example_connection2
```