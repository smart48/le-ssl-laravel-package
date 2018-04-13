# Usage

**Step 1**. Install package

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
