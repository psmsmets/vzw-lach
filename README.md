# vzw-lach

Symfony 5 LTS project with Encore webpack as frontend manager wrapping Bootstrap 5.

Run `git pull` to update the master branch clone to its latest version.

## Deploy steps

A summary based on https://symfony.com/doc/current/deployment.html

1. Configure your Environment variables
   * Make sure you have the .env file present and configured properly (and if a .env.local file is present check its APP_ENV!).
1. Generate composer dependencies for this (prod) environment
   * `composer require symfony/dotenv`
1. Install/update vendors
   * `export APP_ENV=prod`
   * `composer install --no-dev --optimize-autoloader`
1. Clear and warmup symfony cache
   * `APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear`
   * `APP_ENV=prod APP_DEBUG=0 php bin/console cache:warmup`
1. Fix changes in public_html bundles
   * `cp public_html/assets/js/ckeditor_config.js public_html/bundles/fosckeditor/config.js`
