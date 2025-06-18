<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

use App\Traits\ArrayTypeCheckTrait;

/**
 * CentipedeChartPointのコレクションを表すDTO
 */
final class CentipedeChartPointList implements CentipedeChartPointListInterface
{
    use ArrayTypeCheckTrait;

    /** @var array<CentipedeChartPoint> */
    private readonly array $points;

    /**
     * @param array<CentipedeChartPoint> $points
     */
    public function __construct(array $points)
    {
        $this->assertArrayOfType($points, CentipedeChartPoint::class, 'points');
        $this->points = $points;
    }

    /**
     * @return array<CentipedeChartPoint>
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    /**
     * @return \ArrayIterator<int, CentipedeChartPoint>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->points);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->points);
    }

    /**
     * 配列に変換する
     * @return array<array{x: int, y: int}>
     */
    public function toArray(): array
    {
        return array_map(fn (CentipedeChartPoint $point) => $point->toArray(), $this->points);
    }
}
