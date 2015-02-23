# pdns-console

PDNS-Console provided a CLI to manage PowerDNS application and improved it by adding a powerfull DNS zone versionning. 

# Installation

## Requirements

* PHP >= 5.4
* PDO with modules to connect on your database)
* Instance of PDNS (Powerdns) >= 3.1 with MySQL/PostgreSQL/SQLite backend

## Downloading sources

```shell
cd /usr/local/src/
git clone https://github.com/simmstein/pdns-console.git
cd pdns-console
```

### Dependances

```
curl -sS https://getcomposer.org/installer | php
./composer.phar install
```

### Database

#### Edit *propel.yaml*

* ```dsn: "mysql:host=localhost;dbname=pdns"```
  * Change *localhost* with the database server name
  * Change "pdns" with the database name

* Change user and password values with your pdsn database login

Run `./app/propel/console config:convert`

#### Models

The sources does not contain all application models. You have to generate them:

Run `./app/propel/console --recursive model:build`

#### Database updates

pdns-console needs to update pdns original tables and uses 3 more.

```shell
./app/propel/console --recursive migration:diff
./app/propel/console --recursive migration:migrate
```

## Symlink

To access the console without using the full path of `app/console`, make a symlink:

```ln -s $PWD/app/console /usr/local/bin/pdns-console```
