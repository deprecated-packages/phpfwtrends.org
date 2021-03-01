<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\Tests;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use TomasVotruba\PhpFwTrends\HttpKernel\PhpFwTrendsKernel;
use TomasVotruba\PhpFwTrends\Statistics;

final class StatisticsTest extends AbstractKernelTestCase
{
    /**
     * @var array<string, int>
     */
    private const AVERAGE_DAILY_VALUES_BY_MONTH = [
        '2019-12' => 300,
    ];

    private Statistics $statistics;

    protected function setUp(): void
    {
        $this->bootKernel(PhpFwTrendsKernel::class);
        $this->statistics = $this->getService(Statistics::class);
    }

    public function test(): void
    {
        $monthlyValuesByMonth = $this->statistics->expandDailyAverageToMonthTotal(self::AVERAGE_DAILY_VALUES_BY_MONTH);

        $this->assertSame([
            '2019-12' => 9_300,
        ], $monthlyValuesByMonth);
    }
}
