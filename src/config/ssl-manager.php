<?php

return [

    /**
     * Let's Encrypt's account email.
     */
    'account_email' => 'contact@your-app.com',


    /**
     * Required domains CNAME.
     */
    'target_cname' => 'your-app.com',


    /**
     * Queue name where will be put jobs.
     */
    'controller_queue' => 'ssl-manager',


    /**
     * Directory, where NGINX sites configs will be generated.
     *
     * Note: Make sure that you've added this directory to your NGINX config:
     *
     * include /var/www/your-app/storage/sites.d/*.conf;
     */
    'sites_directory' => storage_path('sites.d'),


    /**
     * Directory, where Let's Encrypt's challenges will be stored temporarily.
     */
    'challenge_directory' => storage_path('challenges'),


    /**
     * Directory, where Let's Encrypt's data will be stored.
     */
    'storage_directory' => storage_path('le-storage'),


    /**
     * Command for reloading HTTP server's config.
     */
    'http_config_reload' => '/usr/sbin/nginx -s reload',
];
