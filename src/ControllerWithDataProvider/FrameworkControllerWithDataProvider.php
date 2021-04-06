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
        $frameworksVendorToName = $this->removeMergeableVendorsFromFrameworkVendorNames(
            $this->parameterProvider->provideArrayParameter(Option::FRAMEWORKS_VENDOR_TO_NAME)
        );

        return array_keys($frameworksVendorToName);
    }

    /**
     * Remove vendors marked as mergeable from the list of vendors to report.
     *
     * @param array<string, string> $frameworksVendorToName
     * @return array<string,string>
     */
    private function removeMergeableVendorsFromFrameworkVendorNames(array $frameworksVendorToName): array
    {
        foreach ($this->prepareListOfMergeableVendors() as $mergeableVendor) {
            if (isset($frameworksVendorToName[$mergeableVendor])) {
                unset($frameworksVendorToName[$mergeableVendor]);
            }
        }

        return $frameworksVendorToName;
    }

    /**
     * @return string[]
     */
    private function prepareListOfMergeableVendors(): array
    {
        // This reduction operation iterates over each data set in the
        // Option::FRAMEWORKS_MERGEABLE_VENDORS list, merging the set with prior
        // sets, and ensuring only unique entries are reported.
        return array_reduce(
            $this->parameterProvider->provideArrayParameter(Option::FRAMEWORKS_MERGEABLE_VENDORS),
            function (array $mergeableVendorList, array $mergeableVendorsForOneVendorInList): array {
                $mergeableVendorList = array_merge($mergeableVendorList, $mergeableVendorsForOneVendorInList);
                return array_unique($mergeableVendorList);
            },
            []
        );
    }
}
