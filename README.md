#Laravel Let's Encrypt Laravel Package

## Usage

**Step 1**. Install package

```
composer install
```

Other option is installing it form the Git repository. For that composer.json should have something like

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
        "post-root-package-install": [
            "php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "php artisan key:generate"
        ],
        "post-install-cmd": [
            "Illuminate\\Foundation\\ComposerScripts::postInstall",
            "php artisan optimize"
        ],
        "post-update-cmd": [
            "Illuminate\\Foundation\\ComposerScripts::postUpdate",
            "php artisan optimize"
        ]
    },
    "config": {
        "preferred-install": "dist",
        "sort-packages": true
    }
}
```

You may get
```
[RuntimeException]                                                                     
  Could not scan for classes inside "database" which does not appear to be a file nor a  
   folder  
```
installing locally, but that is because it is not being installed from within a Laravel app.

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
