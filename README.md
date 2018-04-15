# Laravel Let's Encrypt Laravel Package

Laravel Let's Encrypt Laravel Package to install Let's Encrypt SSL Certificates for customers using CNAMES.

## Usage


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
        "imagewize/ssl-manager": "dev-master"
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

Other option is copying over package requirements into composer.json (autoload class and required package) and adding the needed files to the vendor directory.

**Step 1**. Install package

```
composer install
```

**Step 2**. Add service provider to your app:

```php
# config/app

'providers' => [
    // ...
    
    Imagewize\SslManager\SslManagerProvider::class,
],

```

**Step 3**. Publish configs and views:

```bash
php artisan vendor:publish
```

**Step 4**. Configure `config/ssl-manager.php` and create specified there directories.

**Step 5**. Add to your NGINX dynamically generated site configs directory:
 
```
# /etc/nginx/nginx.conf

...

http {
  ...
  
  include /path-to-app/storage/sites.d/*.conf;
}
```

**Step 6**. Change views at `resources/views/imagewize/ssl-manager` as you need.

**Step 7**. Run SSL controller with required privileges:

*Note 1: You can change the queue at `config/ssl-manager.php`.*

*Note 2: Queue mechanism is supposed to be configured.*

```
sudo php artisan queue:work --queue=ssl-manager -- redis
``` 
