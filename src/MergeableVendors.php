<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\PhpFwTrends\Exception\ShouldNotHappenException;
use TomasVotruba\PhpFwTrends\ValueObject\Option;
use Webmozart\Assert\Assert;

final class MergeableVendors
{
    /** @var array<string, string[]> */
    private array $mergeableVendors = [];

    public function __construct(ParameterProvider $parameterProvider)
    {
        $mergeableVendors = $parameterProvider->provideArrayParameter(
            Option::FRAMEWORKS_MERGEABLE_VENDORS
        );
        Assert::isMap($mergeableVendors);
        foreach ($mergeableVendors as $vendor => $aliases) {
            Assert::isArray($aliases);
            Assert::allStringNotEmpty($aliases);
        }
        $this->mergeableVendors = $mergeableVendors;
    }

    public function isVendorMergable(string $vendor): bool
    {
        if (array_key_exists($vendor, $this->mergeableVendors)) {
            return false;
        }

        foreach ($this->mergeableVendors as $vendorList) {
            if (in_array($vendor, $vendorList, true)) {
                return true;
            }
        }

        return false;
    }

    public function mergeVendorTo(string $vendor): string
    {
        foreach ($this->mergeableVendors as $mergeVendorTo => $vendorList) {
            if (in_array($vendor, $vendorList, true)) {
                return $mergeVendorTo;
            }
        }

        throw new ShouldNotHappenException(sprintf(
            'Method vendorIsMergeable MUST be called before "%s"',
            __METHOD__
        ));
    }
}
