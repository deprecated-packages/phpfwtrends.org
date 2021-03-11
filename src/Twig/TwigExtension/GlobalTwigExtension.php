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
     * @return array<string, int|string>
     */
    public function getGlobals(): array
    {
        $minimalMonthAge = $this->parameterProvider->provideIntParameter(Option::MINIMAL_MONTH_AGE);
        $minDownloadsLimit = $this->parameterProvider->provideIntParameter(Option::MIN_DOWNLOADS_LIMIT);

        return [
            'site_title' => 'PHP Framework Trends',
            'minimal_month_age' => $minimalMonthAge,
            'min_downloads_limit' => $minDownloadsLimit,
            'chunk_size_in_months' => $minimalMonthAge / 2,
        ];
    }
}
