<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\PhpFwTrends\Exception\ShouldNotHappenException;
use TomasVotruba\PhpFwTrends\MergeableVendors;
use TomasVotruba\PhpFwTrends\ValueObject\Option;

final class MergeableVendorsTest extends TestCase
{
    private MergeableVendors $mergeableVendors;

    protected function setUp(): void
    {
        $parameterBag = new ParameterBag();
        $parameterBag->set(Option::FRAMEWORKS_MERGEABLE_VENDORS, [
            'laminas' => ['laminas-api-tools', 'mezzio'],
            'zendframework' => ['zfcampus'],
        ]);

        $container = new Container($parameterBag);
        $parameterProvider = new ParameterProvider($container);
        $this->mergeableVendors = new MergeableVendors($parameterProvider);
    }

    /**
     * @return mixed[][]
     */
    public function vendorMergeabilityProvider(): iterable
    {
        yield 'laminas' => ['laminas', false];
        yield 'mezzio' => ['mezzio', true];
        yield 'laminas-api-tools' => ['laminas-api-tools', true];
        yield 'symfony' => ['symfony', false];
        yield 'zfcampus' => ['zfcampus', true];
        yield 'zendframework' => ['zendframework', false];
    }

    /**
     * @dataProvider vendorMergeabilityProvider
     */
    public function testCorrectlyReportsVendorMergeabilityStatus(string $vendorToTest, bool $expectedResult): void
    {
        $isMergeable = $this->mergeableVendors->isVendorMergable($vendorToTest);
        $this->assertSame($expectedResult, $isMergeable);
    }

    public function testMergeVendorToUnknownVendorShouldRaiseException(): void
    {
        $this->expectException(ShouldNotHappenException::class);
        $this->expectExceptionMessage('vendorIsMergeable MUST be called');
        $this->mergeableVendors->mergeVendorTo('unknown-vendor-name');
    }

    /**
     * @return string[][]
     */
    public function vendorMergeToProvider(): iterable
    {
        yield 'mezzio' => ['mezzio', 'laminas'];
        yield 'laminas-api-tools' => ['laminas-api-tools', 'laminas'];
        yield 'zfcampus' => ['zfcampus', 'zendframework'];
    }

    /**
     * @dataProvider vendorMergeToProvider
     */
    public function testMergeVendorToReturnsParentVendor(string $vendorToMerge, string $expectedTargetVendor): void
    {
        $targetVendor = $this->mergeableVendors->mergeVendorTo($vendorToMerge);
        $this->assertSame($expectedTargetVendor, $targetVendor);
    }
}
