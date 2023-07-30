#!/bin/sh

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

composer install
composer dump-autoload

tail -f /dev/null
