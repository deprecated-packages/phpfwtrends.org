<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\ValueObject;

use TomasVotruba\PhpFwTrends\Contract\LastYearTrendAwareInterface;

final class VendorData implements LastYearTrendAwareInterface
{
    private float $lastYearTrend;

    /**
     * @param PackageData[] $packagesData
     */
    public function __construct(
        private string $vendorKey,
        private string $vendorName,
        private int $vendorTotalLastYear,
        private int $vendorTotalPreviousYear,
        private array $packagesData
    ) {
        $lastYearTrend = ($vendorTotalLastYear / $vendorTotalPreviousYear * 100) - 100;
        $lastYearTrend = round($lastYearTrend, 0);
        $this->lastYearTrend = $lastYearTrend;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $packagesDataAsArray = [];
        foreach ($this->packagesData as $packageData) {
            $packagesDataAsArray[] = $packageData->toArray();
        }

        return [
            'vendor_key' => $this->vendorKey,
            'vendor_name' => $this->vendorName,
            'vendor_total_last_year' => $this->vendorTotalLastYear,
            'vendor_total_previous_year' => $this->vendorTotalPreviousYear,
            'last_year_trend' => $this->lastYearTrend,
            'packages_data' => $packagesDataAsArray,
        ];
    }

    public function getVendorKey(): string
    {
        return $this->vendorKey;
    }

    public function getVendorName(): string
    {
        return $this->vendorName;
    }

    public function getVendorTotalLastYear(): int
    {
        return $this->vendorTotalLastYear;
    }

    public function getVendorTotalPreviousYear(): int
    {
        return $this->vendorTotalPreviousYear;
    }

    public function getLastYearTrend(): float
    {
        return $this->lastYearTrend;
    }

    /**
     * @return PackageData[]
     */
    public function getPackagesData(): array
    {
        return $this->packagesData;
    }
}
