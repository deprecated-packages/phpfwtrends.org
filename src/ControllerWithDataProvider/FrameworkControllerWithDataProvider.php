<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\ControllerWithDataProvider;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use TomasVotruba\PhpFwTrends\Controller\FrameworkController;
use TomasVotruba\PhpFwTrends\ValueObject\Option;

final class FrameworkControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    public function __construct(
        private ParameterProvider $parameterProvider
    ) {
    }

    public function getControllerClass(): string
    {
        return FrameworkController::class;
    }

    public function getControllerMethod(): string
    {
        return '__invoke';
    }

    /**
     * @return int[]|string[]
     */
    public function getArguments(): array
    {
        $frameworksVendorToName = $this->parameterProvider->provideArrayParameter(Option::FRAMEWORKS_VENDOR_TO_NAME);
        $mergeableVendorList    = $this->parameterProvider->provideArrayParameter(Option::FRAMEWORKS_MERGEABLE_VENDORS);
        $excludableVendors      = array_reduce($mergeableVendorList, function (array $excludableVendors, array $currentList) : array {
            return $this->reduceExcludableVendors($excludableVendors, $currentList);
        }, []);

        foreach ($excludableVendors as $vendorName) {
            if (isset($frameworksVendorToName[$vendorName])) {
                unset($frameworksVendorToName[$vendorName]);
            }
        }

        return array_keys($frameworksVendorToName);
    }

    /**
     * @param string[] $excludableVendors
     * @param string[] $currentList
     * @return string[]
     */
    private function reduceExcludableVendors(array $excludableVendors, array $currentList): array
    {
        $excludableVendors = array_merge($excludableVendors, $currentList);
        return array_unique($excludableVendors);
    }
}
