<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends;

use TomasVotruba\PhpFwTrends\ValueObject\VendorData;

final class Merger
{
    public function __construct(
        private MergeableVendors $mergeableVendors
    ) {
    }

    /**
     * @param array<string, VendorData> $vendorsData
     * @return array<string, VendorData>
     */
    public function merge(array $vendorsData): array
    {
        $topLevelVendors = [];
        $mergeableVendors = [];

        foreach ($vendorsData as $vendorName => $vendorData) {
            if ($this->mergeableVendors->vendorIsMergable($vendorName)) {
                $mergeVendorTo = $this->mergeableVendors->mergeVendorTo($vendorName);
                if (! array_key_exists($mergeVendorTo, $mergeableVendors)) {
                    $mergeableVendors[$mergeVendorTo] = [];
                }
                $mergeableVendors[$mergeVendorTo][] = $vendorData;
                continue;
            }

            $topLevelVendors[$vendorName] = $vendorData;
        }

        foreach ($mergeableVendors as $vendorName => $vendorsToMerge) {
            $fullVendorData = array_key_exists($vendorName, $topLevelVendors)
                ? $topLevelVendors[$vendorName]
                : array_shift($vendorsToMerge);

            foreach ($vendorsToMerge as $vendorData) {
                $fullVendorData = new VendorData(
                    $fullVendorData->getVendorKey(),
                    $fullVendorData->getVendorName(),
                    $fullVendorData->getVendorTotalLastYear() + $vendorData->getVendorTotalLastYear(),
                    $fullVendorData->getVendorTotalPreviousYear() + $vendorData->getVendorTotalPreviousYear(),
                    array_merge($fullVendorData->getPackagesData(), $vendorData->getPackagesData()),
                );
            }

            $topLevelVendors[$vendorName] = $fullVendorData;
        }

        return $topLevelVendors;
    }
}
