<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TomasVotruba\PhpFwTrends\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    // minimal age of package in moths
    $parameters->set(Option::MINIMAL_MONTH_AGE, 12);

    // minimal monthly downloads to avoid micro packages in stats
    $parameters->set(Option::MIN_DOWNLOADS_LIMIT, 500);

    $parameters->set(Option::FRAMEWORKS_VENDOR_TO_NAME, [
        'nette' => 'Nette',
        'symfony' => 'Symfony',
        'illuminate' => 'Laravel',
        'cakephp' => 'CakePHP',
        'zendframework' => 'Zend',
        'zfcampus' => 'Zend',
        'laminas' => 'Laminas',
        'laminas-api-tools' => 'Laminas',
        'mezzio' => 'Laminas',
        'yiisoft' => 'Yii',
        'doctrine' => 'Doctrine',
    ]);

    $parameters->set(Option::EXCLUDED_FRAMEWORK_PACKAGES, [
        'symfony/security-guard',
        'symfony/security-http',
        'symfony/security-csrf',
        'symfony/lts',
        'symfony/thanks',
        'symfony/polyfill',
        'symfony/polyfill-*',
        'symfony/*-pack',
        'symfony/*-bundle',
        'symfony/class-loader',
        'symfony/assetic-bundle',
        'symfony/locale',
        'symfony/icu',
        'symfony/swiftmailer-bridge',
        'illuminate/html',
        'doctrine/*-module',
        'doctrine/*-bundle',
        'doctrine/static-website-generator',
        'doctrine/doctrine1',
        'doctrine/coding-standard',
        'cakephp/elastic-search',
        'cakephp/acl',
        'yiisoft/yii2-apidoc',
        'zendframework/skeleton-application',
        'zendframework/zend-config-*',
        'zendframework/zend-developer-tools',
        'zfcampus/zf-apigility-skeleton',
        '*/contracts',
        'symfony/*-contracts',
        'symfony/symfony1',
        'symfony/symfony-demo',
        'symfony/skeleton',
        'symfony/requirements-checker',
        'symfony/framework-standard-edition',
        'symfony/force-lowest',
        'symfony/image-fixtures',
        'symfony/*-bridge',
        'symfony/symfony-installer',
        'nette/sandbox',
        'nette/nette-minified',
        'nette/deprecated',
        'nette/extras',
        'nette/coding-standard',
        'nette/code-checker',
        'nette/addon-installer',
        'nette/type-fixer',
        'nette/web-project',
        // laminas
        'laminas/laminas-zendframework-bridge',
        'laminas/laminas-mvc-skeleton',
        'laminas/laminas-config-*',
        'laminas/laminas-developer-tools',
        'laminas-api-tools/api-tools-skeleton',
        'mezzio/mezzio-skeleton',
    ]);

    $parameters->set(Option::FRAMEWORKS_MERGEABLE_VENDORS, [
        'laminas' => ['laminas-api-tools', 'mezzio'],
        'zendframework' => ['zfcampus'],
    ]);
};
