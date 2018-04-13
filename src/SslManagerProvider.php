<?php

namespace Imagewize\SslManager;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\ServiceProvider;
use Imagewize\SslManager\Commands\SslControllerServe;
use Imagewize\SslManager\Commands\SslControllerUpdateCertificate;
use Imagewize\SslManager\Core\DnsService;
use Imagewize\SslManager\Core\HttpService;
use Imagewize\SslManager\Core\SslService;

class SslManagerProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            SslControllerServe::class,
            SslControllerUpdateCertificate::class,
        ]);

        $this->loadViewsFrom(__DIR__.'/views', 'ssl-manager');

        $this->publishes([
            __DIR__.'/config/ssl-manager.php' => config_path('ssl-manager.php'),
            __DIR__.'/views' => resource_path('views/imagewize/ssl-manager'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DnsService::class);

        $this->app->singleton(HttpService::class, function (Container $app) {
            return new HttpService(
                config("ssl-manager.challenge_directory"),
                config("ssl-manager.sites_directory"),
                config("ssl-manager.http_config_reload"),
                $app->make(ViewFactory::class)
            );
        });

        $this->app->singleton(SslService::class, function (Container $app) {
            return new SslService(
                config("ssl-manager.account_email"),
                config("ssl-manager.storage_directory"),
                config("ssl-manager.challenge_directory"),
                $app->make(HttpService::class)
            );
        });

        $this->app->bind(SslControllerUpdateCertificate::class, function (Container $app) {
            return new SslControllerUpdateCertificate(
                $app->make(DnsService::class),
                config("ssl-manager.target_cname"),
                config("ssl-manager.controller_queue")
            );
        });
    }

    public function provides()
    {
        return [
            HttpService::class,
            SslService::class,
            DnsService::class
        ];
    }
}
