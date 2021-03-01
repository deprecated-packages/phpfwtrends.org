<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Packagist\Purifier;

use TomasVotruba\PhpFwTrends\Packagist\PackageRawMonthlyDownloadsProvider;

final class InterveningPackagesPurifier
{
    /**
     * @var array<string, string[]>
     */
    private const INTERVENING_DEPENDENCIES = [
        // https://packagist.org/packages/laravel/framework
        'laravel/framework' => [
            'symfony/console',
            'symfony/error-handler',
            'symfony/finder',
            'symfony/mime',
            'symfony/http-foundation',
            'symfony/http-kernel',
            'symfony/process',
            'symfony/routing',
            'symfony/var-dumper',
        ],
        // particular laravel to symfony deps
        // @see https://packagist.org/packages/illuminate/queue
        'illuminate/queue' => ['symfony/process'],
        // @see https://packagist.org/packages/illuminate/http
        'illuminate/http' => ['symfony/http-foundation', 'symfony/http-kernel', 'symfony/mime'],
        // @see https://packagist.org/packages/illuminate/validation
        'illuminate/validation' => ['symfony/http-foundation', 'symfony/mime'],
        // @see https://packagist.org/packages/illuminate/session
        'illuminate/session' => ['symfony/finder', 'symfony/http-foundation'],
        // @see https://packagist.org/packages/illuminate/console
        'illuminate/console' => ['symfony/console', 'symfony/process'],
        // @see https://packagist.org/packages/illuminate/filesystem
        'illuminate/filesystem' => ['symfony/finder'],
        // @see https://packagist.org/packages/illuminate/routing
        'illuminate/routing' => ['symfony/http-foundation', 'symfony/http-kernel', 'symfony/routing'],
    ];

    /**
     * @var array<string, array<string, int>>
     */
    private array $interveningPackagesDownloads = [];

    public function __construct(
        private PackageRawMonthlyDownloadsProvider $packageRawMonthlyDownloadsProvider
    ) {
    }

    /**
     * @param array<string, int> $monthlyDownloads
     * @return array<string, int>
     */
    public function correctInterveningPackages(array $monthlyDownloads, string $packageName): array
    {
        foreach (self::INTERVENING_DEPENDENCIES as $interveningDependency => $dependingPackages) {
            if (! in_array($packageName, $dependingPackages, true)) {
                continue;
            }

            $interveningDownloads = $this->getInterveningPackageDownloads($interveningDependency);
            foreach (array_keys($monthlyDownloads) as $key) {
                // too old
                if (! isset($interveningDownloads[$key])) {
                    break;
                }

                // correction here!
                $monthlyDownloads[$key] -= $interveningDownloads[$key];
            }
        }

        return $monthlyDownloads;
    }

    /**
     * @return array<string, int>
     */
    private function getInterveningPackageDownloads(string $packageName): array
    {
        if (isset($this->interveningPackagesDownloads[$packageName])) {
            return $this->interveningPackagesDownloads[$packageName];
        }

        $interveningRawMonthlyDownloads = $this->packageRawMonthlyDownloadsProvider->provideForPackage($packageName);

        $this->interveningPackagesDownloads[$packageName] = $interveningRawMonthlyDownloads;

        return $this->interveningPackagesDownloads[$packageName];
    }
}
