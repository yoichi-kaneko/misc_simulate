<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * Centipedeシミュレーションのチャートポイントを表すDTO
 */
final class CentipedeChartPoint implements CentipedeChartPointInterface
{
    private readonly int $x;
    private readonly int $y;

    /**
     * @param int $x X座標
     * @param int $y Y座標
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * X座標を取得する
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Y座標を取得する
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * 配列に変換する
     * @return array{x: int, y: int}
     */
    public function toArray(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
        ];
    }
}