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

### Configure database access

In propel.yaml:

* ```dsn: "mysql:host=localhost;dbname=pdns"```
  * Change *localhost* by the database server name
  * Change "pdns" by the database name

* Change user and password values with you pdsn database login
