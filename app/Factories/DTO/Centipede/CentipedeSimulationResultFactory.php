<?php

declare(strict_types=1);

namespace App\Factories\DTO\Centipede;

use App\Calculations\Centipede\DTO\CentipedeChartPoint;
use App\Calculations\Centipede\DTO\CentipedeChartPointList;
use App\Calculations\Centipede\DTO\CentipedeSimulationResult;
use App\Factories\DTO\AbstractDTOFactory;

/**
 * CentipedeSimulationResultを作成するファクトリ
 */
class CentipedeSimulationResultFactory extends AbstractDTOFactory
{
    /**
     * デフォルト値でCentipedeSimulationResultを作成
     * @return CentipedeSimulationResult
     */
    public function create(): CentipedeSimulationResult
    {
        return new CentipedeSimulationResult(
            0.5,  // cognitiveUnitValue
            '\dfrac{1}{2}',  // cognitiveUnitLatexText
            0.0,  // averageOfReversedCausality
            [],   // data
            new CentipedeChartPointList([
                new CentipedeChartPoint(1, 2),
            ])    // chartData (CentipedeChartPointList)
        );
    }

    /**
     * カスタム値でCentipedeSimulationResultを作成
     * @param array $attributes
     * @return CentipedeSimulationResult
     */
    public function createWith(array $attributes): CentipedeSimulationResult
    {
        $defaults = [
            'cognitiveUnitValue' => 0.5,
            'cognitiveUnitLatexText' => '\dfrac{1}{2}',
            'averageOfReversedCausality' => 0.0,
            'data' => [],
            'chartData' => new CentipedeChartPointList([]), // empty CentipedeChartPointList
        ];

        $attributes = array_merge($defaults, $attributes);

        return new CentipedeSimulationResult(
            $attributes['cognitiveUnitValue'],
            $attributes['cognitiveUnitLatexText'],
            $attributes['averageOfReversedCausality'],
            $attributes['data'],
            $attributes['chartData']
        );
    }
}
