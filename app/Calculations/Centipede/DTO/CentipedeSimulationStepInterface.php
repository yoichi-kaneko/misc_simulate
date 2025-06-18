<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * Centipedeシミュレーションの各ステップの結果を表すインターフェース
 */
interface CentipedeSimulationStepInterface
{
    /**
     * ステップ数を取得する
     * @return int
     */
    public function getT(): int;

    /**
     * 最大nu値を取得する
     * @return int
     */
    public function getMaxNuValue(): int;

    /**
     * 左辺値を取得する
     * @return string
     */
    public function getLeftSideValue(): string;

    /**
     * 右辺値を取得する
     * @return string
     */
    public function getRightSideValue(): string;

    /**
     * 結果を取得する
     * @return bool
     */
    public function getResult(): bool;

    /**
     * 配列に変換する
     * @return array{t: int, max_nu_value: int, left_side_value: string, right_side_value: string, result: bool}
     */
    public function toArray(): array;
}
