<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\ValueObject;

use Nette\Utils\Strings;
use TomasVotruba\PhpFwTrends\Contract\LastYearTrendAwareInterface;

final class VendorData implements LastYearTrendAwareInterface
{
    /**
     * @param PackageData[] $packagesData
     */
    public function __construct(
        private string $vendorName,
        private int $vendorTotalLastYear,
        private int $vendorTotalPreviousYear,
        private float $lastYearTrend,
        private array $packagesData
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function __toArray(): array
    {
        $packagesDataAsArray = [];
        foreach ($this->packagesData as $packageData) {
            $packagesDataAsArray[] = $packageData->__toArray();
        }

        return [
            'vendor_name' => $this->vendorName,
            'vendor_total_last_year' => $this->vendorTotalLastYear,
            'vendor_total_previous_year' => $this->vendorTotalPreviousYear,
            'last_year_trend' => $this->lastYearTrend,
            'packages_data' => $packagesDataAsArray,
        ];
    }

    public function getVendorKey(): string
    {
        return Strings::webalize($this->vendorName);
    }

    public function getVendorNameWebalized(): string
    {
        return Strings::webalize($this->vendorName);
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
