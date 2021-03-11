<?php

declare(strict_types=1);

namespace TomasVotruba\PhpFwTrends\ValueObject;

use Nette\Utils\Strings;
use TomasVotruba\PhpFwTrends\Contract\LastYearTrendAwareInterface;

final class PackageData implements LastYearTrendAwareInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/PWqxoE/1
     */
    private const DASH_OR_SLASH_REGEX = '#(\/|-)#';

    private string $packageShortName;

    private string $packageKey;

    public function __construct(
        private string $packageName,
        private float $lastYearTrend,
        private int $youngerChunk,
        private int $olderChunk
    ) {
        $this->packageShortName = (string) Strings::after($packageName, '/');

        $this->packageKey = Strings::replace($packageName, self::DASH_OR_SLASH_REGEX, '_');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'package_name' => $this->packageName,
            'package_short_name' => $this->packageShortName,
            'younger_chunk' => $this->youngerChunk,
            'older_chunk' => $this->olderChunk,
            'last_year_trend' => $this->lastYearTrend,
        ];
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    public function getLastYearTrend(): float
    {
        return $this->lastYearTrend;
    }

    public function getYoungerChunk(): int
    {
        return $this->youngerChunk;
    }

    public function getOlderChunk(): int
    {
        return $this->olderChunk;
    }

    public function getPackageShortName(): string
    {
        return $this->packageShortName;
    }
}
