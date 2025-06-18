<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * Centipedeシミュレーションのチャートポイントを表すインターフェース
 */
interface CentipedeChartPointInterface
{
    /**
     * X座標を取得する
     * @return int
     */
    public function getX(): int;

    /**
     * Y座標を取得する
     * @return int
     */
    public function getY(): int;

    /**
     * 配列に変換する
     * @return array{x: int, y: int}
     */
    public function toArray(): array;
}
