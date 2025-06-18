<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * CentipedeChartPointのコレクションを表すインターフェース
 */
interface CentipedeChartPointListInterface extends \Countable, \IteratorAggregate
{
    /**
     * @return array<CentipedeChartPoint>
     */
    public function getPoints(): array;

    /**
     * 配列に変換する
     * @return array<array{x: int, y: int}>
     */
    public function toArray(): array;
}
