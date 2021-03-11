<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Twig\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class NumberTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        $twigFilters = [];
        $twigFilters[] = new TwigFilter('thousands', fn (int $number): string => $this->formatNumber(
            $number / (10 ** 3)
        ) . ' K');

        return $twigFilters;
    }

    private function formatNumber(float|int $number): string
    {
        return number_format($number, 0, '.', ' ');
    }
}
