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
<<<<<<< HEAD
     * @return int[]|string[]
=======
     * @return string[]
>>>>>>> ec9b904... misc
     */
    public function getArguments(): array
    {
        $frameworksVendorToName = $this->parameterProvider->provideArrayParameter(Option::FRAMEWORKS_VENDOR_TO_NAME);
        return array_keys($frameworksVendorToName);
    }
}
