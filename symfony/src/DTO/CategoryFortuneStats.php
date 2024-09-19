<?php

declare(strict_types=1);

namespace App\DTO;

class CategoryFortuneStats
{
    public function __construct(
        private int $fortunesPrinted,
        private float $fortunesAverage,
        private string $categoryName
    ) {}

    public function getFortunesPrinted(): int
    {
        return $this->fortunesPrinted;
    }

    public function getFortunesAverage(): float
    {
        return $this->fortunesAverage;
    }

    public function getCategoryName(): string
    {
        return $this->categoryName;
    }
}