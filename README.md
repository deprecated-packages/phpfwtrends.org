# PHP FW Trends

## Install

```bash
git clone ...
composer install
```

## Generate Stats

```bash
bin/console generate-stats
```

## Run Website Locally

```bash
php -S localhost:8001 -t public 
```

Open [localhost:8001](http://localhost:8001) in your browser to see the website

## Create Static Website to Deploy

```bash
bin/console dump-static-site 
```
