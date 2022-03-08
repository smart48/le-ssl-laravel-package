<?php

return [

    /**
     * Let's Encrypt's account email.
     */
    'account_email' => 'contact@your-app.com',


    /**
     * Required domains A NAME.
     */
    'target_aname' => 'your-ip.address',


    /**
     * Queue name where will be put jobs.
     */
    'controller_queue' => 'ssl-manager',

    /**
     * Root project
     */
    'root_site' => env('SSL_ROOT_SITE', '/home/forge/smart48.com/current/public'),


    /**
     * Directory, where NGINX sites configs will be generated.
     *
     * Note: Make sure that you've added this directory to your NGINX config:
     *
     * include /var/www/your-app/storage/sites.d/*.conf;
     */
    'sites_directory' => env('SSL_SITES_DIRECTORY'),


    /**
     * Directory, where Let's Encrypt's challenges will be stored temporarily.
     */
    'challenge_directory' => env('SSL_CHALLENGE_DIRECTORY'),


    /**
     * Directory, where Let's Encrypt's data will be stored.
     */
    'storage_directory' => env('SSL_STORAGE_DIRECTORY'),


    /**
     * Command for reloading HTTP server's config.
     */
    'http_config_reload' => '/usr/sbin/nginx -s reload',

    'notification_failed_email' => env('SSL_NOTIFICATION_FAILED_EMAIL'),
];
