<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends;

use TomasVotruba\PhpFwTrends\Contract\LastYearTrendAwareInterface;

final class Sorter
{
    /**
     * @param LastYearTrendAwareInterface[] $data
     * @return LastYearTrendAwareInterface[]
     */
    public function sortArrayByLastYearTrend(array $data): array
    {
        usort(
            $data,
            fn (LastYearTrendAwareInterface $firstItem, LastYearTrendAwareInterface $secondItem): int => $secondItem->getLastYearTrend() <=> $firstItem->getLastYearTrend()
        );

        return $data;
    }
}
