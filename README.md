# SSL Manager

A Laravel package that issues and renews free [Let's Encrypt](https://letsencrypt.org/) certificates for your customers' own domains, and generates the Nginx config for each one.

It is built for apps where customers point their domain (`www.customer.com`) at your server with an A record and expect it to be served over HTTPS, such as website builders and white-label tools. Certificates are ordered with a bundled ACME v2 client (`src/Acme`, derived from [stonemax/acme2](https://github.com/stonemax/acme2), MIT).

## How it works

1. The customer points an A record for their domain at your server (`target_aname`).
2. `ssl-controller:update-certificate <domain>` queues a job (or runs it right away with `now`).
3. The job places the HTTP-01 challenge file and orders the certificate from Let's Encrypt (`SslService`). For a bare domain like `customer.com` the certificate also covers `www.customer.com`. The Nginx server config that uses the certificate is written by `HttpService`.
4. Nginx is reloaded. A renewal is the same command, run again later.
5. If the job fails, an email is sent to `notification_failed_email`.

The package contains a DNS check (`DnsService::hasProperRecord`) that verifies the A record points at `target_aname`, but the job does not call it at the moment (it is commented out in `UpdateCertificate`). If the A record is wrong, the Let's Encrypt challenge fails and you get the failure email.

Only the HTTP-01 challenge and Let's Encrypt are supported today. The CA is fixed by the ACME client library, which knows Let's Encrypt only (production, or staging with `SSL_STAGING=true`). Mind [Let's Encrypt's rate limits](https://letsencrypt.org/docs/rate-limits/) when testing.

## Requirements

- Laravel 10, 11 or 12 (see `composer.json`)
- Nginx, with permission to reload it (see *Nginx*)
- A queue worker, Redis recommended (see *Usage*)
- Public port 80 on your server, for the HTTP-01 challenge

## Installation

```
composer require imagewize/ssl-manager
```

If the package is not on Packagist yet, add the repository to your `composer.json` first:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/imagewize/ssl-manager.git"
    }
]
```

The service provider is discovered automatically. Publish the config and the Nginx view:

```
php artisan vendor:publish --provider="Imagewize\SslManager\SslManagerProvider"
```

This creates `config/ssl-manager.php` and `resources/views/ssl-manager/`. Change the view if your sites need a different Nginx server block.

## Configuration

All options are in `config/ssl-manager.php`:

| Option | Env | Meaning |
|--------|-----|---------|
| `account_email` | - | Let's Encrypt account email |
| `target_aname` | - | The IP address customers must point their A record at |
| `controller_queue` | - | Queue name for the jobs (default `ssl-manager`) |
| `root_site` | `SSL_ROOT_SITE` | The `public` directory of your app, used in the generated Nginx config |
| `sites_directory` | `SSL_SITES_DIRECTORY` | Where the Nginx site configs are generated |
| `challenge_directory` | `SSL_CHALLENGE_DIRECTORY` | Where HTTP-01 challenge files are placed temporarily |
| `storage_directory` | `SSL_STORAGE_DIRECTORY` | Where account keys and certificates are stored |
| `http_config_reload` | - | Command that reloads Nginx (default `/usr/sbin/nginx -s reload`) |
| `notification_failed_email` | `SSL_NOTIFICATION_FAILED_EMAIL` | Who gets an email when an order fails |

Create the three directories and make them writable by the user that runs the queue worker.

### Nginx

Include the generated site configs in your main config:

```
# /etc/nginx/nginx.conf
http {
    # ...
    include /path-to-app/storage/sites.d/*.conf;
}
```

The reload needs root. Allow the user that runs the worker to do it without a password (`sudo visudo`):

```
# SSL Manager: reload Nginx
deploy ALL = NOPASSWD: /etc/init.d/nginx
```

Replace `deploy` with your own user (`forge`, `ploi`, ...), and the path with the reload command you configured.

## Usage

Run a queue worker for the package's queue, with the privileges it needs to write the configs and reload Nginx:

```
sudo php artisan queue:work redis --queue=ssl-manager
```

Request a certificate for a domain:

```
# queue the job: a new certificate for the domain
php artisan ssl-controller:update-certificate customer.com

# run it now, without the queue (a new order is only made when the third argument is true)
php artisan ssl-controller:update-certificate customer.com now true
```

The arguments are `{domain} {now=false} {renew=false}`: `now` runs the job in this process instead of queueing it, `renew` places a new order instead of reusing the existing certificate. Renewals are the same command; schedule it for your domains before their certificates expire (Let's Encrypt certificates last 90 days).

## Credits

Originally written by Karabutin Alex and maintained by [Imagewize](https://github.com/imagewize). The ACME client is derived from [stonemax/acme2](https://github.com/stonemax/acme2) by Zhang Jinlong (MIT, `LICENSE-acme2.txt`).

## License

MIT, see [LICENSE.md](LICENSE.md).
