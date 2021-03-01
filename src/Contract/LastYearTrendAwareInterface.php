<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Contract;

interface LastYearTrendAwareInterface
{
    public function getLastYearTrend(): float;
}
