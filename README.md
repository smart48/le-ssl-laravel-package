# Laravel Let's Encrypt Laravel Package

Laravel Let's Encrypt Laravel Package to install Let's Encrypt SSL Certificates for customers using CNAMES.

## Usage

There are multiple ways to install Laravel Packages, but we recommend two here based on the current setup:

* including private repo
* local installation

### Private Repo Installation

One option is installing it from the private Git repository. For that composer.json should have something like

```
{
    "name": "laravel/laravel",
    "description": "The Laravel Framework.",
    "keywords": ["framework", "laravel"],
    "license": "MIT",
    "type": "project",
    "repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:jasperf/le-ssl-laravel-package.git"
    }
  ],
    "require": {
        "php": ">=5.6.4",
        "appstract/laravel-opcache": "^1.1",
        "imagewize/ssl-manager": "dev-master",
        "stonemax/acme2": "^1.0"
    },
    "require-dev": {
        "fzaninotto/faker": "~1.4",
        "mockery/mockery": "0.9.*",
        "phpunit/phpunit": "~5.7"
    },
    "autoload": {
        "classmap": [
            "database"
        ],
        "psr-4": {
            "App\\": "app/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        .......
        ]
    },
    "config": {
        "preferred-install": "dist",
        "sort-packages": true
    }
}
```


See also this [url](https://likegeeks.com/install-and-use-non-composer-laravel-packages/) on setting up composer packages using private git repos. 

Once that is done you install it with composer. 

### Local Installation

Other option is local installing:

* Copy the zip file over to the vendors folder 
* unzip it and rename it as need be
* Copy over package requirements into app composer.json (autoload class and required package) 

## Step 1 Install package

Stonemax package will be installed automatically when you run:
```
composer install
```

## Step 2. Add service provider to your app:

```php
# config/app

'providers' => [
    // ...
    
    Imagewize\SslManager\SslManagerProvider::class,
],

```

## Step 3 Publish configs and views:

```bash
php artisan vendor:publish
```

## Step 4 

Configure `config/ssl-manager.php` and create specified there directories.

## Step 5 

Add to your NGINX dynamically generated site configs directory:
 
```
# /etc/nginx/nginx.conf

...

http {
  ...
  
  include /path-to-app/storage/sites.d/*.conf;
}
```

## Step 6

Change views at `resources/views/imagewize/ssl-manager` as you need.

## Step 7

Run SSL controller with required privileges:

```
sudo php artisan queue:work --queue=ssl-manager -- redis
``` 

*Note 1: You can change the queue at `config/ssl-manager.php`.*

*Note 2: Queue mechanism is supposed to be configured.*
