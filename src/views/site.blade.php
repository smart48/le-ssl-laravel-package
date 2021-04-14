# fastcgi path needs to be created
fastcgi_cache_path /etc/nginx//{{ $domain }}/cache levels=1:2 keys_zone=smart48:100m inactive=60m;
fastcgi_cache_key "$scheme$request_method$host$request_uri";

server {
    listen 80;
    listen [::]:80;
    server_name {{ $domain }};

    location /.well-known/acme-challenge {
        default_type "text/plain";
        alias {{ $challengeDirectory }}/{{ $domain }};
    }

@if ($certificateInfo)
    # Redirect to HTTPS version
    location / {
        return 301 https://$host$request_uri;
    }
@else
    # Reset connection
    location / {
        return 444;
    }
@endif
}

@if ($certificateInfo)
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name {{ $domain }};
    root {{ config("ssl-manager.root_site") }};

    ssl_certificate     {{ $certificateInfo['certificateFullChained'] }};
    ssl_certificate_key {{ $certificateInfo['privateKey'] }};

    # Improve HTTPS performance with session resumption
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 5m;

    # Enable server-side protection against BEAST attacks
    ssl_prefer_server_ciphers on;
    ssl_ciphers EECDH+ECDSA+AESGCM:EECDH+aRSA+AESGCM:EECDH+ECDSA+SHA384:EECDH+ECDSA+SHA256:EECDH+aRSA+SHA384:EECDH+aRSA+SHA256:EECDH:EDH+aRSA:HIGH:!aNULL:!eNULL:!LOW:!RC4:!3DES:!MD5:!EXP:!PSK:!SRP:!SEED:!DSS:!CAMELLIA;

    # Disable SSLv3
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;

    # Diffie-Hellman parameter for DHE ciphersuites
    # $ sudo openssl dhparam -out /etc/ssl/certs/dhparam.pem 4096
    ssl_dhparam /etc/ssl/certs/dhparam.pem;

    # Enable HSTS (https://developer.mozilla.org/en-US/docs/Security/HTTP_Strict_Transport_Security)
    add_header Strict-Transport-Security "max-age=63072000; includeSubdomains";
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    # Enable X-Cache 
    # https://lyte.id.au/2014/08/28/x-cache-and-x-cache-lookupheaders/
    add_header X-Cache $upstream_cache_status;

    index index.html index.htm index.php;

    # Enable OCSP stapling (http://blog.mozilla.org/security/2013/07/29/ocsp-stapling-in-firefox)
    ssl_stapling on;
    ssl_stapling_verify on;
    ssl_trusted_certificate {{ $certificateInfo['certificateFullChained'] }};

    resolver 8.8.8.8 8.8.4.4 valid=300s;
    resolver_timeout 5s;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log off;
    error_log  /var/log/nginx/{{ $domain }}-error.log error;

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_cache smart48;
        fastcgi_cache_valid 200 1m;
        fastcgi_cache_bypass $no_cache;
        fastcgi_no_cache $no_cache;
        fastcgi_cache_use_stale updating error timeout invalid_header http_500;
        fastcgi_cache_lock on;
        fastcgi_ignore_headers Cache-Control Expires Set-Cookie;
        include fastcgi_params;
    }

    # cache Exceptions 
    # https://medium.com/@nathobson/setting-up-fastcgi-caching-on-laravel-forge-cccecbd49ce
    # Cache everything by default
    set $no_cache 0;
    
    # Don't cache POST requests
    if ($request_method = POST)
    {
        set $no_cache 1;
    }
    
    # Don't cache if the URL contains a query string
    if ($query_string != "")
    {
        set $no_cache 1;
    }
    
    # Don't cache the following URLs
    if ($request_uri ~* "/(cp/)")
    {
        set $no_cache 1;
    }
    
    # Don't cache if there is a cookie called PHPSESSID
    if ($http_cookie = "PHPSESSID")
    {
        set $no_cache 1;
    }
}
@endif
