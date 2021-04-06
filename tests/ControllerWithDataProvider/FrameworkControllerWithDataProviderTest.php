<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Tests\ControllerWithDataProvider;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\PhpFwTrends\ControllerWithDataProvider\FrameworkControllerWithDataProvider;
use TomasVotruba\PhpFwTrends\ValueObject\Option;

class FrameworkControllerWithDataProviderTest extends TestCase
{
    public function testGetArgumentsReturnsListOfFrameworkVendorNamesThatOmitsVendorsMarkedToMerge(): void
    {
        $parameterBag = new ParameterBag();
        $parameterBag->set(Option::FRAMEWORKS_VENDOR_TO_NAME, [
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
        $parameterBag->set(Option::FRAMEWORKS_MERGEABLE_VENDORS, [
            'laminas' => ['laminas-api-tools', 'mezzio'],
            'zendframework' => ['zfcampus'],
        ]);

        $container         = new Container($parameterBag);
        $parameterProvider = new ParameterProvider($container);
        $frameworkControllerWithDataProvider        = new FrameworkControllerWithDataProvider($parameterProvider);

        $this->assertSame(
            ['nette', 'symfony', 'illuminate', 'cakephp', 'zendframework', 'laminas', 'yiisoft', 'doctrine'],
            $frameworkControllerWithDataProvider->getArguments()
        );
    }
}
