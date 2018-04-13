server {
    listen 80;
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
    server_name {{ $domain }};

    ssl on;
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

    # Enable OCSP stapling (http://blog.mozilla.org/security/2013/07/29/ocsp-stapling-in-firefox)
    ssl_stapling on;
    ssl_stapling_verify on;
    ssl_trusted_certificate {{ $certificateInfo['certificateFullChained'] }};

    resolver 8.8.8.8 8.8.4.4 valid=300s;
    resolver_timeout 5s;

    location / {
        default_type "text/html";
        return 200 "It works!";
    }
}
@endif
