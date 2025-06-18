<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * Centipedeシミュレーション結果を保持するDTOのインターフェース
 */
interface CentipedeSimulationResultInterface
{
    /**
     * Cognitive Unitの値を取得する
     * @return float
     */
    public function getCognitiveUnitValue(): float;

    /**
     * Cognitive UnitのLatex形式のテキストを取得する
     * @return string
     */
    public function getCognitiveUnitLatexText(): string;

    /**
     * 逆因果性の平均値を取得する
     * @return float
     */
    public function getAverageOfReversedCausality(): float;

    /**
     * シミュレーション結果データを取得する
     * @return array<CentipedeSimulationStepInterface>
     */
    public function getData(): array;

    /**
     * チャート用データを取得する
     * @return array
     */
    public function getChartData(): array;
}
