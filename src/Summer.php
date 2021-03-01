<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends;

use TomasVotruba\PhpFwTrends\ValueObject\PackageData;

final class Summer
{
    /**
     * @param PackageData[] $packagesData
     */
    public function getLastYearTotalArraySum(array $packagesData): int
    {
        $total = 0;
        foreach ($packagesData as $packageData) {
            $total += $packageData->getLast12Months();
        }

        return $total;
    }

    /**
     * @param PackageData[] $packagesData
     */
    public function getPreviousYearTotalArraySum(array $packagesData): int
    {
        $total = 0;
        foreach ($packagesData as $packageData) {
            $total += $packageData->getPrevious12Months();
        }

        return $total;
    }
}
