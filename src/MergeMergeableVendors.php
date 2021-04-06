<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends;

use TomasVotruba\PhpFwTrends\ValueObject\VendorData;
use Webmozart\Assert\Assert;

final class MergeMergeableVendors
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
            if ($this->mergeableVendors->isVendorMergable($vendorName)) {
                $mergeVendorTo = $this->mergeableVendors->mergeVendorTo($vendorName);
                if (! array_key_exists($mergeVendorTo, $mergeableVendors)) {
                    $mergeableVendors[$mergeVendorTo] = [];
                }
                $mergeableVendors[$mergeVendorTo][] = $vendorData;
                continue;
            }

            $topLevelVendors[$vendorName] = $vendorData;
        }

        Assert::allIsInstanceOf($topLevelVendors, VendorData::class);

        foreach ($mergeableVendors as $vendorName => $vendorsToMerge) {
            Assert::isNonEmptyList($vendorsToMerge);
            Assert::allIsInstanceOf($vendorsToMerge, VendorData::class);

            $fullVendorData = array_key_exists($vendorName, $topLevelVendors)
                ? $topLevelVendors[$vendorName]
                : array_shift($vendorsToMerge);

            Assert::isInstanceOf($fullVendorData, VendorData::class);

            foreach ($vendorsToMerge as $vendorToMerge) {
                /** @var VendorData $fullVendorData */
                $fullVendorData = new VendorData(
                    $fullVendorData->getVendorKey(),
                    $fullVendorData->getVendorName(),
                    $fullVendorData->getVendorTotalLastYear() + $vendorToMerge->getVendorTotalLastYear(),
                    $fullVendorData->getVendorTotalPreviousYear() + $vendorToMerge->getVendorTotalPreviousYear(),
                    array_merge($fullVendorData->getPackagesData(), $vendorToMerge->getPackagesData()),
                );
            }

            $topLevelVendors[$vendorName] = $fullVendorData;
        }

        Assert::isMap($topLevelVendors);
        Assert::allIsInstanceOf($topLevelVendors, VendorData::class);

        return $topLevelVendors;
    }
}
