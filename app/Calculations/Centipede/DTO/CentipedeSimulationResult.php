<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * Centipedeシミュレーション結果を保持するDTO
 */
final class CentipedeSimulationResult implements CentipedeSimulationResultInterface
{
    private readonly float $cognitiveUnitValue;
    private readonly string $cognitiveUnitLatexText;
    private readonly float $averageOfReversedCausality;
    private readonly array $data;
    private readonly array $chartData;

    /**
     * @param float $cognitiveUnitValue Cognitive Unitの値
     * @param string $cognitiveUnitLatexText Cognitive UnitのLatex形式のテキスト
     * @param float $averageOfReversedCausality 逆因果性の平均値
     * @param array $data シミュレーション結果データ
     * @param array $chartData チャート用データ
     */
    public function __construct(
        float $cognitiveUnitValue,
        string $cognitiveUnitLatexText,
        float $averageOfReversedCausality,
        array $data,
        array $chartData
    ) {
        $this->cognitiveUnitValue = $cognitiveUnitValue;
        $this->cognitiveUnitLatexText = $cognitiveUnitLatexText;
        $this->averageOfReversedCausality = $averageOfReversedCausality;
        $this->data = $data;
        $this->chartData = $chartData;
    }

    /**
     * Cognitive Unitの値を取得する
     * @return float
     */
    public function getCognitiveUnitValue(): float
    {
        return $this->cognitiveUnitValue;
    }

    /**
     * Cognitive UnitのLatex形式のテキストを取得する
     * @return string
     */
    public function getCognitiveUnitLatexText(): string
    {
        return $this->cognitiveUnitLatexText;
    }

    /**
     * 逆因果性の平均値を取得する
     * @return float
     */
    public function getAverageOfReversedCausality(): float
    {
        return $this->averageOfReversedCausality;
    }

    /**
     * シミュレーション結果データを取得する
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * チャート用データを取得する
     * @return array
     */
    public function getChartData(): array
    {
        return $this->chartData;
    }
}
