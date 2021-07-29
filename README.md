# PHP FW Trends


## [2021-07-29] Project is deprecated because it's based on [2 mutually exclusive metrics](https://tomasvotruba.com/blog/how-i-made-huge-mistake-with-interpretation-of-laravel-downloads/)

---

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
