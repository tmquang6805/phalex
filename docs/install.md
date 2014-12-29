# Installation

## Via Composer (Recommend)

The recommended way to start a new **Phalex** project is to clone the skeleton application and use `composer` to 
install dependencies using the `create-project` command:

```
curl -s https://getcomposer.org/installer | php --
php composer.phar create-project -sdev tmquang6805/phalex-skeleton path/to/install
```

Alternately, clone the repository and manually invoke `composer`:

```
cd my/project/dir
git clone https://github.com/tmquang6805/phalex-skeleton.git
cd phalex-skeleton
curl -s https://getcomposer.org/installer | php --
php composer.phar install
```

## Web Server setup

### PHP Cli Server

The simplist way to get started 

```
php -S 127.0.0.1:8080 -t public/ public/index.php
```

### Apache Setup

To use Apache, setup a virtual host to point to the public/ directory of the project. It should look something like below:

```
<VirtualHost *:80>
    ServerName phalex.local
    DocumentRoot /path/to/phalex/project/public

    <Directory /path/to/phalex/project/public>
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
```

Or, if you are using Apache 2.4 or above:

```
<VirtualHost *:80>
    ServerName phalex.local
    DocumentRoot /path/to/phalex/project/public

    <Directory /path/to/phalex/project/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
