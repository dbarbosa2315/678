#!/bin/sh

# Inicie o PHP-FPM
/usr/local/sbin/php-fpm &

# Inicie o Nginx
/usr/sbin/nginx -g "daemon off;"