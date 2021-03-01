<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Tests\Packagist;

use Nette\Utils\DateTime;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use TomasVotruba\PhpFwTrends\HttpKernel\PhpFwTrendsKernel;
use TomasVotruba\PhpFwTrends\Packagist\PackageRawMonthlyDownloadsProvider;

final class PackageRawMonthlyDownloadsProviderTest extends AbstractKernelTestCase
{
    private PackageRawMonthlyDownloadsProvider $packageRawMonthlyDownloadsProvider;

    protected function setUp(): void
    {
        $this->bootKernel(PhpFwTrendsKernel::class);

        $this->packageRawMonthlyDownloadsProvider = self::$container->get(PackageRawMonthlyDownloadsProvider::class);
    }

    public function test(): void
    {
        $symplifyPackageBuilderStats = $this->packageRawMonthlyDownloadsProvider->provideForPackage(
            'symplify/package-builder'
        );

        $statsCount = count($symplifyPackageBuilderStats);
        $this->assertGreaterThan(10, $statsCount);

        $previousMonth = DateTime::from('- 2 months')->format('Y-m');

        $this->assertArrayHasKey($previousMonth, $symplifyPackageBuilderStats);
    }
}
