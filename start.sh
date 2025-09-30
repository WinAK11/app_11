#!/bin/bash

# Create required directories
mkdir -p /tmp/nginx /var/log/nginx /tmp/client_body /tmp/proxy /tmp/fastcgi /tmp/uwsgi /tmp/scgi

# Replace ${PORT} in nginx.conf
envsubst '${PORT}' < /app/nginx.conf > /tmp/nginx.conf

# Start PHP-FPM in the background
php-fpm -y /app/php-fpm.conf -F &

# Start Nginx in the foreground
nginx -c /tmp/nginx.conf -g 'daemon off;'
