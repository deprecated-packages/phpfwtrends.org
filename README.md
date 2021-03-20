# PHP FW Trends

## Install

```bash
git clone https://github.com/TomasVotruba/phpfwtrends.org
composer install
```

## Generate stats

```bash
bin/console generate-stats
```

## Run website locally

```bash
cd path/to/clone
php -S 0.0.0.0:8080 -t public
```

Open [0.0.0.0:8001](http://0.0.0.0:8001) in your browser to see the website

## Create static website to deploy

```bash
bin/console dump-static-site 
```
