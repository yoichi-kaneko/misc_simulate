<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

use App\Traits\ArrayTypeCheckTrait;

/**
 * Centipedeシミュレーション結果を保持するDTO
 */
final class CentipedeSimulationResult implements CentipedeSimulationResultInterface
{
    use ArrayTypeCheckTrait;
    private readonly float $cognitiveUnitValue;
    private readonly string $cognitiveUnitLatexText;
    private readonly float $averageOfReversedCausality;
    /** @var array<CentipedeSimulationStepInterface> */
    private readonly array $data;
    /** @var array<CentipedeChartPoint> */
    private readonly array $chartData;

    /**
     * @param float $cognitiveUnitValue Cognitive Unitの値
     * @param string $cognitiveUnitLatexText Cognitive UnitのLatex形式のテキスト
     * @param float $averageOfReversedCausality 逆因果性の平均値
     * @param array<CentipedeSimulationStepInterface> $data シミュレーション結果データ
     * @param array<CentipedeChartPoint> $chartData チャート用データ
     */
    public function __construct(
        float $cognitiveUnitValue,
        string $cognitiveUnitLatexText,
        float $averageOfReversedCausality,
        array $data,
        array $chartData
    ) {
        $this->assertArrayOfType($data, CentipedeSimulationStepInterface::class, 'data');
        $this->assertArrayOfType($chartData, CentipedeChartPoint::class, 'chartData');

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
     * @return array<CentipedeSimulationStepInterface>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * チャート用データを取得する
     * @return array<CentipedeChartPoint>
     */
    public function getChartData(): array
    {
        return $this->chartData;
    }
}
