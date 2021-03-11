<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\ControllerWithDataProvider;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;
use TomasVotruba\PhpFwTrends\Controller\FrameworkController;
use TomasVotruba\PhpFwTrends\ValueObject\Option;

final class FrameworkControllerWithDataProvider implements ControllerWithDataProviderInterface
{
    private ParameterProvider $parameterProvider;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
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
     * @return string[]
     */
    public function getArguments(): array
    {
        $frameworksVendorToName = $this->parameterProvider->provideArrayParameter(Option::FRAMEWORKS_VENDOR_TO_NAME);
        return array_keys($frameworksVendorToName);
    }
}
