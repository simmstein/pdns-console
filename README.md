# pdns-console

PDNS-Console provided a CLI to manage PowerDNS application and improved it by adding a powerfull DNS zone versionning.

* [Installation](#installation)
* [Available commands](#available-commands)
* [Example](#example)

# Commands

## Available commands

### Helpers

```
 about                   Show information about pdns-console
 help                    Displays help for a command
 list                    Lists commands
```

### Manage domains

```
 domain:add              Add a domain
 domain:list             List domains
 domain:remove           Remove a domain
```

### Manage zones

```
 zone:add                Add a zone
 zone:assign             Assign a zone to a domain.
 zone:list               List DNS zones
 zone:push               Push activated zones to PowerDNS tables
 zone:record:add         Add a zone record
 zone:record:remove      Remove a zone record
 zone:remove             Remove a zone
 zone:unassign           Unassign the domain zone
 zone:version:active     Active a zone version
 zone:version:add        Add a zone version
 zone:version:copy       Copy a zone version
 zone:version:remove     Remove an unactivated zone version
 zone:version:unactive   Active a zone version
```

### Example

#### Specifications

* We want to mange the domain *example.tld*
* We need 3 records:
	* *example.tld* -> 1.2.3.4
	* *www.example.tld* -> same as *example.tld*
    * *example.tld* MX is mail.foo.net

#### Add the domain

```
$ ./app/console domain:add
Name: example.tld
MASTER [null]:
Type [NATIVE]:
Domain added
```

```
$ ./app/console domain:list
DOMAIN: example.tld
ID    : 5             # This ID is important
TYPE  : NATIVE
MASTER:
```

#### Create a zone

##### Interactive (or not)

```
$ ./app/console zone:add
Name: Example zone
Description: My example zone
Zone added.
```

##### The list is updated

```
$ ./app/console zone:list
Example zone
------------
My example zone
ID: 4                 # This ID is important

No version found
```

##### New version for "Example zone" (ID=4)

```
$ ./app/console zone:version:add 4 # My zone ID
Zone version added.
```

```
$ ./app/console zone:list
Example zone
------------
My example zone
ID: 4

Version: 1 - Active: No

No record found.
```

#### Add the records

##### "SOA" record (no interactive)
```
$ ./app/console zone:record:add 4 1 --name @ --type SOA --content "localhost. postmaster@localhost 0 10800 3600 604800 3600" --ttl 3600 --prio null
```

##### "A" record (interactive)

```
$ ./app/console zone:record:add 4 1
Name: @
Content: 1.2.3.4

Available types: A AAAA CNAME MX NS TXT SPF WKS SRV LOC SOA
Type: A
TTL: 3600
Prio [null]:
Zone record added.
```

##### "CNAME" record. "--ttl" is missing

```
$ ./app/console zone:record:add 4 1 --name www --type CNAME --content example.tld. --prio null
TTL: 3600
Zone record added.
```

##### "MX" record. The validation of "--prio" failed

```
$ ./app/console zone:record:add 4 1 --name @ --type MX --content mail.foo.net. --ttl 3600 --prio badValue
Prio [null]: badValueAgain
Prio [null]: 10
Zone record added.
```

##### My version is now ready

```
$ ./app/console zone:list
Example zone
------------
My example zone
ID: 4

Version: 1 - Active: No

   ID | NAME                  | TYPE      | TTL    | PRIO    | CONTENT
----------------------------------------------------------------------
   13 | @                     | SOA       | 3600   |         | localhost postmaster@localhost 0 10800 3600 604800 3600
   14 | @                     | A         | 3600   |         | 1.2.3.4
   15 | www                   | CNAME     | 3600   |         | example.tld.
   16 | @                     | MX        | 3600   | 10      | mail.foo.net.
```

#### Active and assign the new zone

```
$ ./app/console zone:version:active 4 1
Zone version activated.
$ ./app/console zone:assign 4 5
Domain zone updated.
```

```
./app/console domain:list --zone
DOMAIN: example.tld
ID    : 5
TYPE  : NATIVE
MASTER: 


      > Example zone
      > ------------
      > My example zone
      > ID: 4
      > 
      > Version: 1 - Active: Yes
      > 
      >    ID | NAME                  | TYPE      | TTL    | PRIO    | CONTENT
      > ----------------------------------------------------------------------
      >    13 | @                     | SOA       | 3600   |         | localhost postmaster@localhost 0 10800 3600 604800 3600
      >    14 | @                     | A         | 3600   |         | 1.2.3.4
      >    15 | www                   | CNAME     | 3600   |         | example.tld.
      >    16 | @                     | MX        | 3600   | 10      | mail.foo.net.
      > 
```

#### Push modifications

```
./app/console zone:push
```

#### Test :)

```
$ dig +short -t A @localhost example.tld
1.2.3.4
$ dig +short -t CNAME @localhost www.example.tld
example.tld.
$ dig +short -t MX @localhost example.tld
10 mail.foo.net.
```

# Installation

## Requirements

* PHP >= 5.4
* PDO with module for database connection
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
  * Change *pdns* with the database name

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
