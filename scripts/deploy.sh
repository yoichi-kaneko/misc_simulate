#!/bin/bash

branch=$1

git checkout .

if [[ -z "$branch" ]]; then
  git checkout master
else
  git fetch origin
  git checkout "$branch"
fi

git pull
composer install
npm ci
npm run prod

php artisan migrate
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

sudo systemctl restart supervisord
