<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations;

use App\Calculations\Centipede;
use App\Calculations\Centipede\CentipedeDataCombiner;
use App\Calculations\Centipede\Formatter\CentipedeFormatter;
use App\Calculations\Centipede\Simulator\CentipedeSimulator;
use Tests\TestCase;

class CentipedeTest extends TestCase
{
    /**
     * makeCognitiveUnitLatexTextメソッドが正しいLaTeX形式のテキストを返すことをテストします。
     * @test
     * @dataProvider makeCognitiveUnitLatexTextDataProvider
     * @return void
     */
    public function testMakeCognitiveUnitLatexText(
        int $base_numerator,
        int $numerator_exp_1,
        int $numerator_exp_2,
        int $denominator_exp,
        string $expected
    ) {
        // CentipedeFormatterクラスのインスタンスを作成
        $formatter = new CentipedeFormatter();

        $result = $formatter->makeCognitiveUnitLatexText(
            $base_numerator,
            $numerator_exp_1,
            $numerator_exp_2,
            $denominator_exp
        );
        $this->assertEquals($expected, $result, "LaTeX形式のテキストが期待通りではありません。");
    }

    /**
     * makeCognitiveUnitLatexTextの検証用データセット
     * @return array<string, mixed>
     */
    public static function makeCognitiveUnitLatexTextDataProvider(): array
    {
        return [
            'ケース1' => [
                'base_numerator' => 2,
                'numerator_exp_1' => 1,
                'numerator_exp_2' => 2,
                'denominator_exp' => 3,
                'expected' => '\dfrac{2^{\frac{1}{2}}}{2^{3}}',
            ],
            'ケース2' => [
                'base_numerator' => 3,
                'numerator_exp_1' => 2,
                'numerator_exp_2' => 3,
                'denominator_exp' => 4,
                'expected' => '\dfrac{3^{\frac{2}{3}}}{2^{4}}',
            ],
            'ケース3' => [
                'base_numerator' => 5,
                'numerator_exp_1' => 3,
                'numerator_exp_2' => 4,
                'denominator_exp' => 5,
                'expected' => '\dfrac{5^{\frac{3}{4}}}{2^{5}}',
            ],
            'ケース4' => [
                'base_numerator' => 10,
                'numerator_exp_1' => 1,
                'numerator_exp_2' => 1,
                'denominator_exp' => 2,
                'expected' => '\dfrac{10^{\frac{1}{1}}}{2^{2}}',
            ],
            'ケース5' => [
                'base_numerator' => 1,
                'numerator_exp_1' => 5,
                'numerator_exp_2' => 5,
                'denominator_exp' => 0,
                'expected' => '\dfrac{1^{\frac{5}{5}}}{2^{0}}',
            ],
        ];
    }

    /**
     * runメソッドのテスト - $combination_player_1がnullの場合
     * @test
     */
    public function testRunWithNullCombinationPlayer1()
    {
        // モックの作成
        $simulatorMock = $this->createMock(CentipedeSimulator::class);
        $formatterMock = $this->createMock(CentipedeFormatter::class);
        $dataCombinerMock = $this->createMock(CentipedeDataCombiner::class);

        // テスト用のデータ
        $patterns = [
            '0' => [
                'base_numerator' => 2,
                'numerator_exp_1' => 1,
                'numerator_exp_2' => 2,
                'denominator_exp' => 3,
            ],
        ];
        $max_step = 10;
        $max_rc = 5;
        $combination_player_1 = null;

        // シミュレーション結果の作成
        $simulationStep = new \App\Calculations\Centipede\DTO\CentipedeSimulationStep(
            1, // t
            5, // maxNuValue
            '1.23456789', // leftSideValue
            '2.34567890', // rightSideValue
            true // result
        );

        $simulationResult = new \App\Calculations\Centipede\DTO\CentipedeSimulationResult(
            0.5, // cognitiveUnitValue
            '\dfrac{2^{\frac{1}{2}}}{2^{3}}', // cognitiveUnitLatexText
            0.7, // averageOfReversedCausality
            [$simulationStep], // data
            [['x' => 1, 'y' => 0]] // chartData
        );

        // モックの振る舞いを設定
        $simulatorMock->expects($this->once())
            ->method('calculatePattern')
            ->with(
                $this->equalTo(2),
                $this->equalTo(1),
                $this->equalTo(2),
                $this->equalTo(3),
                $this->equalTo($max_step)
            )
            ->willReturn($simulationResult);

        $formattedResult = [
            'cognitive_unit_latex_text' => '\dfrac{2^{\frac{1}{2}}}{2^{3}}',
            'cognitive_unit_value' => 0.5,
            'average_of_reversed_causality' => 0.7,
            'data' => [],
            'chart_data' => [],
        ];

        $formatterMock->expects($this->once())
            ->method('format')
            ->with($this->equalTo($simulationResult))
            ->willReturn($formattedResult);

        // dataCombinerのcombineメソッドは呼ばれないはず
        $dataCombinerMock->expects($this->never())
            ->method('combine');

        // テスト対象のインスタンスを作成
        $centipede = new Centipede($simulatorMock, $formatterMock, $dataCombinerMock);

        // メソッドを実行
        $result = $centipede->run($patterns, $max_step, $max_rc, $combination_player_1);

        // 結果を検証
        $this->assertEquals('ok', $result['result']);
        $this->assertEquals($max_step, $result['render_params']['max_step']);
        $this->assertEquals($max_rc, $result['render_params']['max_rc']);
        $this->assertEquals($formattedResult, $result['pattern_data']['0']);
        $this->assertNull($result['combination_data']);
    }

    /**
     * runメソッドのテスト - $combination_player_1がnullでない場合
     * @test
     */
    public function testRunWithNonNullCombinationPlayer1()
    {
        // モックの作成
        $simulatorMock = $this->createMock(CentipedeSimulator::class);
        $formatterMock = $this->createMock(CentipedeFormatter::class);
        $dataCombinerMock = $this->createMock(CentipedeDataCombiner::class);

        // テスト用のデータ
        $patterns = [
            '0_1' => [
                'base_numerator' => 2,
                'numerator_exp_1' => 1,
                'numerator_exp_2' => 2,
                'denominator_exp' => 3,
            ],
            '0_2' => [
                'base_numerator' => 3,
                'numerator_exp_1' => 2,
                'numerator_exp_2' => 3,
                'denominator_exp' => 4,
            ],
        ];
        $max_step = 10;
        $max_rc = 5;
        $combination_player_1 = ['0' => '1']; // Player 1が1を選択

        // シミュレーション結果の作成
        $simulationStep1 = new \App\Calculations\Centipede\DTO\CentipedeSimulationStep(
            1, // t
            5, // maxNuValue
            '1.23456789', // leftSideValue
            '2.34567890', // rightSideValue
            true // result
        );

        $simulationResult1 = new \App\Calculations\Centipede\DTO\CentipedeSimulationResult(
            0.5, // cognitiveUnitValue
            '\dfrac{2^{\frac{1}{2}}}{2^{3}}', // cognitiveUnitLatexText
            0.7, // averageOfReversedCausality
            [$simulationStep1], // data
            [['x' => 1, 'y' => 0]] // chartData
        );

        $simulationStep2 = new \App\Calculations\Centipede\DTO\CentipedeSimulationStep(
            1, // t
            6, // maxNuValue
            '2.34567890', // leftSideValue
            '3.45678901', // rightSideValue
            true // result
        );

        $simulationResult2 = new \App\Calculations\Centipede\DTO\CentipedeSimulationResult(
            0.6, // cognitiveUnitValue
            '\dfrac{3^{\frac{2}{3}}}{2^{4}}', // cognitiveUnitLatexText
            0.8, // averageOfReversedCausality
            [$simulationStep2], // data
            [['x' => 1, 'y' => 0]] // chartData
        );

        // モックの振る舞いを設定 - 連続した呼び出し
        $simulatorMock->expects($this->exactly(2))
            ->method('calculatePattern')
            ->willReturnCallback(function ($baseNumerator, $numeratorExp1, $numeratorExp2, $denominatorExp, $maxStep) use ($simulationResult1, $simulationResult2, $max_step) {
                static $callCount = 0;

                if ($callCount === 0) {
                    $this->assertEquals(2, $baseNumerator);
                    $this->assertEquals(1, $numeratorExp1);
                    $this->assertEquals(2, $numeratorExp2);
                    $this->assertEquals(3, $denominatorExp);
                    $this->assertEquals($max_step, $maxStep);
                    $callCount++;

                    return $simulationResult1;
                } else {
                    $this->assertEquals(3, $baseNumerator);
                    $this->assertEquals(2, $numeratorExp1);
                    $this->assertEquals(3, $numeratorExp2);
                    $this->assertEquals(4, $denominatorExp);
                    $this->assertEquals($max_step, $maxStep);
                    $callCount++;

                    return $simulationResult2;
                }
            });

        $formattedResult1 = [
            'cognitive_unit_latex_text' => '\dfrac{2^{\frac{1}{2}}}{2^{3}}',
            'cognitive_unit_value' => 0.5,
            'average_of_reversed_causality' => 0.7,
            'data' => [],
            'chart_data' => [],
        ];

        $formattedResult2 = [
            'cognitive_unit_latex_text' => '\dfrac{3^{\frac{2}{3}}}{2^{4}}',
            'cognitive_unit_value' => 0.6,
            'average_of_reversed_causality' => 0.8,
            'data' => [],
            'chart_data' => [],
        ];

        // フォーマッターのモック設定 - 連続した呼び出し
        $formatterMock->expects($this->exactly(2))
            ->method('format')
            ->willReturnCallback(function ($result) use ($simulationResult1, $simulationResult2, $formattedResult1, $formattedResult2) {
                static $callCount = 0;

                if ($callCount === 0) {
                    $this->assertEquals($simulationResult1, $result);
                    $callCount++;

                    return $formattedResult1;
                } else {
                    $this->assertEquals($simulationResult2, $result);
                    $callCount++;

                    return $formattedResult2;
                }
            });

        $patternData = [
            '0_1' => $formattedResult1,
            '0_2' => $formattedResult2,
        ];

        $combinedData = [
            '0' => [
                'data' => [],
                'chart_data' => [],
                'cognitive_unit_latex_text_1' => '\dfrac{2^{\frac{1}{2}}}{2^{3}}',
                'cognitive_unit_latex_text_2' => '\dfrac{3^{\frac{2}{3}}}{2^{4}}',
                'cognitive_unit_value_1' => 0.5,
                'cognitive_unit_value_2' => 0.6,
                'average_of_reversed_causality' => 0.75,
            ],
        ];

        $dataCombinerMock->expects($this->once())
            ->method('combine')
            ->with(
                $this->equalTo($combination_player_1),
                $this->equalTo($patternData)
            )
            ->willReturn($combinedData);

        // テスト対象のインスタンスを作成
        $centipede = new Centipede($simulatorMock, $formatterMock, $dataCombinerMock);

        // メソッドを実行
        $result = $centipede->run($patterns, $max_step, $max_rc, $combination_player_1);

        // 結果を検証
        $this->assertEquals('ok', $result['result']);
        $this->assertEquals($max_step, $result['render_params']['max_step']);
        $this->assertEquals($max_rc, $result['render_params']['max_rc']);
        $this->assertEquals($patternData, $result['pattern_data']);
        $this->assertEquals($combinedData, $result['combination_data']);
    }
}
