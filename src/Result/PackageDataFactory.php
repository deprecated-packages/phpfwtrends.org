<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Result;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\PhpFwTrends\Exception\ShouldNotHappenException;
use TomasVotruba\PhpFwTrends\Packagist\PackageMonthlyDownloadsProvider;
use TomasVotruba\PhpFwTrends\Sorter;
use TomasVotruba\PhpFwTrends\Statistics;
use TomasVotruba\PhpFwTrends\ValueObject\Option;
use TomasVotruba\PhpFwTrends\ValueObject\PackageData;

final class PackageDataFactory
{
    public function __construct(
        private PackageMonthlyDownloadsProvider $packageMonthlyDownloadsProvider,
        private Statistics $statistics,
        private Sorter $sorter,
        private SymfonyStyle $symfonyStyle,
        private ParameterProvider $parameterProvider
    ) {
    }

    /**
     * @param string[] $packageNames
     * @return PackageData[]
     */
    public function createPackagesData(array $packageNames): array
    {
        $packagesData = [];

        $minimalMonthAge = $this->parameterProvider->provideIntParameter(Option::MINIMAL_MONTH_AGE);
        $chunkSize = $minimalMonthAge / 2;

        foreach ($packageNames as $packageName) {
            $monthlyDownloads = $this->packageMonthlyDownloadsProvider->provideForPackage($packageName);

            if ($this->shouldSkipPackageForOutlier($packageName, $monthlyDownloads)) {
                continue;
            }

            // total downloads for 1st half
            $youngerChunk = $this->getChunkAndExpandDailyAverageToMonthAndSum($monthlyDownloads, $chunkSize, 0);

            // total downloads for 2nd half
            $olderChunk = $this->getChunkAndExpandDailyAverageToMonthAndSum($monthlyDownloads, $chunkSize, 1);

            if ($olderChunk === 0) {
                // to prevent fatal errors
                continue;
            }

            $lastYearTrend = 100 * ($youngerChunk / $olderChunk) - 100;

            $packagesData[] = new PackageData(
                $packageName,
                // numbers
                $lastYearTrend,
                $youngerChunk,
                $olderChunk
            );
        }

        return $this->sorter->sortArrayByLastYearTrend($packagesData);
    }

    /**
     * @param int[] $monthlyDownloads
     */
    private function shouldSkipPackageForOutlier(string $packageName, array $monthlyDownloads): bool
    {
        $minimalMonthAge = $this->parameterProvider->provideIntParameter(Option::MINIMAL_MONTH_AGE);

        // not enough data, package younger than 24 months → skip it
        if (count($monthlyDownloads) < $minimalMonthAge - 1) {
            $skippingReasonMessage = sprintf(
                'Skipping "%s" package for not enough data. %d months provided, %d needed',
                $packageName,
                count($monthlyDownloads),
                $minimalMonthAge
            );
            $this->symfonyStyle->note($skippingReasonMessage);

            return true;
        }

        $firstKey = array_key_first($monthlyDownloads);
        $lastMonthDailyDownloads = $monthlyDownloads[$firstKey];

        if ($lastMonthDailyDownloads < 0) {
            // monthly downloads are in minus
            throw new ShouldNotHappenException(sprintf(
                'Last month daily downloads for "%s" package and "%s" month is in minus: %d',
                $packageName,
                $firstKey,
                $lastMonthDailyDownloads
            ));
        }

        $minDownloadsLimit = $this->parameterProvider->provideIntParameter(Option::MIN_DOWNLOADS_LIMIT);

        // too small package → skip it
        if ($lastMonthDailyDownloads <= $minDownloadsLimit) {
            $skippingReasonMessage = sprintf(
                'Skipping "%s" package for not enough downloads last month. %d provided, %d needed',
                $packageName,
                $lastMonthDailyDownloads,
                $minDownloadsLimit
            );
            $this->symfonyStyle->note($skippingReasonMessage);

            return true;
        }

        return false;
    }

    /**
     * @param int[] $dataByYearMonth
     */
    private function getChunkAndExpandDailyAverageToMonthAndSum(
        array $dataByYearMonth,
        int $chunkSize,
        int $chunkPosition
    ): int {
        $chunks = array_chunk($dataByYearMonth, $chunkSize, true);

        $chunk = $chunks[$chunkPosition];
        $expandedChunk = $this->statistics->expandDailyAverageToMonthTotal($chunk);

        return (int) array_sum($expandedChunk);
    }
}
