#!/bin/bash
service php8.3-fpm start
cd /var/www/html/frontend && npm start &
nginx
