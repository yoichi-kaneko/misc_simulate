<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * Centipedeシミュレーションの各ステップの結果を表すDTO
 */
final class CentipedeSimulationStep implements CentipedeSimulationStepInterface
{
    private readonly int $t;
    private readonly int $maxNuValue;
    private readonly string $leftSideValue;
    private readonly string $rightSideValue;
    private readonly bool $result;

    /**
     * @param int $t ステップ数
     * @param int $maxNuValue 最大nu値
     * @param string $leftSideValue 左辺値
     * @param string $rightSideValue 右辺値
     * @param bool $result 結果
     */
    public function __construct(
        int $t,
        int $maxNuValue,
        string $leftSideValue,
        string $rightSideValue,
        bool $result
    ) {
        $this->t = $t;
        $this->maxNuValue = $maxNuValue;
        $this->leftSideValue = $leftSideValue;
        $this->rightSideValue = $rightSideValue;
        $this->result = $result;
    }

    /**
     * ステップ数を取得する
     * @return int
     */
    public function getT(): int
    {
        return $this->t;
    }

    /**
     * 最大nu値を取得する
     * @return int
     */
    public function getMaxNuValue(): int
    {
        return $this->maxNuValue;
    }

    /**
     * 左辺値を取得する
     * @return string
     */
    public function getLeftSideValue(): string
    {
        return $this->leftSideValue;
    }

    /**
     * 右辺値を取得する
     * @return string
     */
    public function getRightSideValue(): string
    {
        return $this->rightSideValue;
    }

    /**
     * 結果を取得する
     * @return bool
     */
    public function getResult(): bool
    {
        return $this->result;
    }

    /**
     * 配列に変換する
     * @return array{t: int, max_nu_value: int, left_side_value: string, right_side_value: string, result: bool}
     */
    public function toArray(): array
    {
        return [
            't' => $this->t,
            'max_nu_value' => $this->maxNuValue,
            'left_side_value' => $this->leftSideValue,
            'right_side_value' => $this->rightSideValue,
            'result' => $this->result,
        ];
    }
}