#!/usr/bin/env bash
echo "Removing all the cache and logs"
rm -rf app/cache/* app/logs/*
echo "Clearing the cache"
php app/console cache:clear
echo "Delete all the assets"
#php app/console assetic:dump --env=dev
echo "install all the new assets"
#php app/console assets:install --env=dev
chmod -R 777 app/cache/ app/logs/
