# vzw-lach

Symfony 5.4 LTS project with Encore webpack as frontend manager wrapping Bootstrap 5.

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

## .htaccess

```
# www to non-www
RewriteEngine On
RewriteBase /
RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
RewriteRule ^(.*)$ https://%1/$1 [R=301,L]

# http to https
RewriteEngine On
RewriteCond %{SERVER_PORT} 80
RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]

<IfModule mod_rewrite.c>
    Options -MultiViews
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

<IfModule !mod_rewrite.c>
    <IfModule mod_alias.c>
        RedirectMatch 302 ^/$ /index.php/
    </IfModule>
</IfModule>
```
