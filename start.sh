#!/bin/bash

# Start PHP-FPM in the background
php-fpm -y /workspace/php-fpm.conf -F &

# Start Nginx in the foreground
nginx -c /workspace/nginx.conf -g 'daemon off;'
