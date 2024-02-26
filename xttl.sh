#!/bin/bash
service php8.3-fpm start
cd /var/www/html/fend && npm start &
nginx
