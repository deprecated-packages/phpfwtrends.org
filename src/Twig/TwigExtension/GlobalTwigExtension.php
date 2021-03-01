<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Twig\TwigExtension;

use Symplify\PackageBuilder\Parameter\ParameterProvider;
use TomasVotruba\PhpFwTrends\ValueObject\Option;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class GlobalTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private ParameterProvider $parameterProvider
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getGlobals(): array
    {
        $minimalMonthAge = $this->parameterProvider->provideIntParameter(Option::MINIMAL_MONTH_AGE);

        return [
            'site_title' => 'PHP Framework Trends',
            'chunk_size_in_months' => $minimalMonthAge / 2,
        ];
    }
}
