<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\Simulator;

use App\Calculations\Centipede\DTO\CentipedeChartPoint;
use App\Calculations\Centipede\DTO\CentipedeSimulationResult;
use App\Calculations\Centipede\DTO\CentipedeSimulationStep;
use App\Calculations\Centipede\Formatter\CentipedeFormatter;

/**
 * Centipedeシミュレーションを実行するクラス
 */
class CentipedeSimulator
{
    // NUの値は「所定の計算式の値より小さい整数」のため、計算式が整数ちょうどになった場合を考慮してceil() - 1という式としている
    private const ODD_NU = 'ceil(pow(2 * %1$d - 1, %3$d / %4$d) / %2$.15f) - 1';
    private const ODD_LEFT_SIDE = 'number_format(pow(2 * %1$d, %2$d / %3$d), 8)';
    private const ODD_RIGHT_SIDE = 'number_format((%2$d + 1) * %1$.15f, 8)';
    private const EVEN_NU = 'ceil(pow(2 * %1$d + 2, %3$d / %4$d) / %2$.15f) - 1';
    private const EVEN_LEFT_SIDE = 'number_format(pow(2 * %1$d + 3, %2$d / %3$d), 8)';
    private const EVEN_RIGHT_SIDE = 'number_format((%2$d + 1) * %1$.15f, 8)';

    private CentipedeFormatter $formatter;

    /**
     * コンストラクタ
     * @param CentipedeFormatter $formatter
     */
    public function __construct(CentipedeFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * パターンの計算を実行する
     * @param int $baseNumerator
     * @param int $numeratorExp1
     * @param int $numeratorExp2
     * @param int $denominatorExp
     * @param int $maxStep
     * @return CentipedeSimulationResult
     * @throws \Exception
     */
    public function calculatePattern(
        int $baseNumerator,
        int $numeratorExp1,
        int $numeratorExp2,
        int $denominatorExp,
        int $maxStep
    ): CentipedeSimulationResult {
        $data = [];
        $cognitiveUnitValue = $this->calcCognitiveUnitValue(
            $baseNumerator,
            $numeratorExp1,
            $numeratorExp2,
            $denominatorExp
        );

        for ($i = 1; $i <= $maxStep; $i++) {
            /*
             * 次の手順で計算を行う。
             * - 計算式1で、nuの最大値を求める
             * - 計算式2の左辺と右辺を求める
             * - 右辺の方が大きい場合、結果はtrue
             */
            if ($i % 2 > 0) {
                $maxNuFormula = self::ODD_NU;
                $leftSideFormula = self::ODD_LEFT_SIDE;
                $rightSideFormula = self::ODD_RIGHT_SIDE;
            } else {
                $maxNuFormula = self::EVEN_NU;
                $leftSideFormula = self::EVEN_LEFT_SIDE;
                $rightSideFormula = self::EVEN_RIGHT_SIDE;
            }

            $maxNuValue = $this->evalFormula(
                sprintf(
                    $maxNuFormula,
                    $i,
                    $cognitiveUnitValue,
                    $numeratorExp1,
                    $numeratorExp2
                )
            );
            $leftSideValue = $this->evalFormula(
                sprintf(
                    $leftSideFormula,
                    $i,
                    $numeratorExp1,
                    $numeratorExp2
                )
            );
            $rightSideValue = $this->evalFormula(
                sprintf(
                    $rightSideFormula,
                    $cognitiveUnitValue,
                    $maxNuValue
                )
            );
            $data[] = new CentipedeSimulationStep(
                $i,
                (int)$maxNuValue,
                $leftSideValue,
                $rightSideValue,
                ($leftSideValue < $rightSideValue)
            );
        }
        $chartData = $this->formatter->makeChartData($data);
        $cognitiveUnitLatexText = $this->formatter->makeCognitiveUnitLatexText(
            $baseNumerator,
            $numeratorExp1,
            $numeratorExp2,
            $denominatorExp
        );
        $yValues = array_map(fn (CentipedeChartPoint $point) => $point->getY(), $chartData);
        $averageOfReversedCausality = (array_sum($yValues) / count($chartData));

        return new CentipedeSimulationResult(
            $cognitiveUnitValue,
            $cognitiveUnitLatexText,
            $averageOfReversedCausality,
            $data,
            $chartData
        );
    }

    /**
     * 計算式をevalで実行する
     * @param string $str
     * @return mixed
     */
    private function evalFormula(string $str)
    {
        return eval('return ' . $str . ';');
    }

    /**
     * Cognitive Unitの値を計算して返す
     * @param int $baseNumerator
     * @param int $numeratorExp1
     * @param int $numeratorExp2
     * @param int $denominatorExp
     * @return float
     * @throws \Exception
     */
    private function calcCognitiveUnitValue(
        int $baseNumerator,
        int $numeratorExp1,
        int $numeratorExp2,
        int $denominatorExp
    ): float {
        $numerator = pow($baseNumerator, ($numeratorExp1 / $numeratorExp2));

        if (is_nan($numerator) || is_infinite($numerator)) {
            throw new \Exception(trans('validation.invalid_cognitive_unit'));
        }

        $denominator = pow(2, $denominatorExp);

        return $numerator / $denominator;
    }
}
