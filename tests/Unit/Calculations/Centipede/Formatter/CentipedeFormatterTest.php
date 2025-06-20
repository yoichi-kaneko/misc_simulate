<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Centipede\Formatter;

use App\Calculations\Centipede\DTO\CentipedeChartPoint;
use App\Calculations\Centipede\DTO\CentipedeChartPointList;
use App\Calculations\Centipede\DTO\CentipedeSimulationResultInterface;
use App\Calculations\Centipede\Formatter\CentipedeFormatter;
use App\Factories\DTO\Centipede\CentipedeSimulationStepFactory;
use PHPUnit\Framework\TestCase;

class CentipedeFormatterTest extends TestCase
{
    private CentipedeSimulationStepFactory $stepFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stepFactory = new CentipedeSimulationStepFactory();
    }

    /**
     * formatメソッドが正しくフォーマットされた結果を返すことをテストします。
     * @test
     * @return void
     */
    public function testFormat()
    {
        // モックのCentipedeSimulationResultInterfaceを作成
        $simulationResult = $this->createMock(CentipedeSimulationResultInterface::class);

        // モックの振る舞いを設定
        $simulationResult->method('getCognitiveUnitLatexText')->willReturn('\dfrac{3^{\frac{1}{2}}}{2^{3}}');
        $simulationResult->method('getCognitiveUnitValue')->willReturn(0.375);
        $simulationResult->method('getAverageOfReversedCausality')->willReturn(0.5);
        $simulationResult->method('getData')->willReturn([
            $this->stepFactory->createWith(['t' => 1, 'result' => false]),
            $this->stepFactory->createWith(['t' => 2, 'result' => true]),
        ]);
        $simulationResult->method('getChartData')->willReturn(new CentipedeChartPointList([
            new CentipedeChartPoint(1, 1),
            new CentipedeChartPoint(2, 0),
        ]));

        // CentipedeFormatterクラスのインスタンスを作成
        $formatter = new CentipedeFormatter();

        // formatメソッドを実行
        $result = $formatter->format($simulationResult);

        // 結果を検証
        $this->assertIsArray($result);
        $this->assertArrayHasKey('cognitive_unit_latex_text', $result);
        $this->assertArrayHasKey('cognitive_unit_value', $result);
        $this->assertArrayHasKey('average_of_reversed_causality', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('chart_data', $result);

        $this->assertSame('\dfrac{3^{\frac{1}{2}}}{2^{3}}', $result['cognitive_unit_latex_text']);
        $this->assertSame(0.375, $result['cognitive_unit_value']);
        $this->assertSame(0.5, $result['average_of_reversed_causality']);
        $this->assertSame([
            [
                't' => 1,
                'max_nu_value' => 0,
                'left_side_value' => '',
                'right_side_value' => '',
                'result' => false,
            ],
            [
                't' => 2,
                'max_nu_value' => 0,
                'left_side_value' => '',
                'right_side_value' => '',
                'result' => true,
            ],
        ], $result['data']);
        $this->assertSame([
            ['x' => 1, 'y' => 1],
            ['x' => 2, 'y' => 0],
        ], $result['chart_data']);
    }

    /**
     * makeCognitiveUnitLatexTextメソッドのデータプロバイダー
     * @return array
     */
    public static function makeCognitiveUnitLatexTextDataProvider(): array
    {
        return [
            '基本的なケース' => [
                'baseNumerator' => 3,
                'numeratorExp1' => 1,
                'numeratorExp2' => 2,
                'denominatorExp' => 3,
                'expected' => '\dfrac{3^{\frac{1}{2}}}{2^{3}}',
            ],
            '異なる分子の底と指数' => [
                'baseNumerator' => 5,
                'numeratorExp1' => 2,
                'numeratorExp2' => 3,
                'denominatorExp' => 4,
                'expected' => '\dfrac{5^{\frac{2}{3}}}{2^{4}}',
            ],
            '大きな値のケース' => [
                'baseNumerator' => 7,
                'numeratorExp1' => 3,
                'numeratorExp2' => 4,
                'denominatorExp' => 5,
                'expected' => '\dfrac{7^{\frac{3}{4}}}{2^{5}}',
            ],
        ];
    }

    /**
     * makeCognitiveUnitLatexTextメソッドが正しくLatexテキストを生成することをテストします。
     * @test
     * @dataProvider makeCognitiveUnitLatexTextDataProvider
     * @return void
     */
    public function testMakeCognitiveUnitLatexText(
        int $baseNumerator,
        int $numeratorExp1,
        int $numeratorExp2,
        int $denominatorExp,
        string $expected
    ) {
        // CentipedeFormatterクラスのインスタンスを作成
        $formatter = new CentipedeFormatter();

        $result = $formatter->makeCognitiveUnitLatexText(
            $baseNumerator,
            $numeratorExp1,
            $numeratorExp2,
            $denominatorExp
        );

        // 結果を検証
        $this->assertSame($expected, $result);
    }

    /**
     * makeChartDataメソッドのデータプロバイダー
     * @return array
     */
    public static function makeChartDataDataProvider(): array
    {
        return [
            'resultがすべてfalseの場合' => [
                'inputData' => [
                    ['t' => 1, 'result' => false],
                    ['t' => 2, 'result' => false],
                    ['t' => 3, 'result' => false],
                ],
                'expectedPoints' => [
                    ['x' => 1, 'y' => 1],
                    ['x' => 2, 'y' => 2],
                    ['x' => 3, 'y' => 3],
                ],
            ],
            'resultにtrueが含まれる場合' => [
                'inputData' => [
                    ['t' => 1, 'result' => false],
                    ['t' => 2, 'result' => true],
                    ['t' => 3, 'result' => false],
                ],
                'expectedPoints' => [
                    ['x' => 1, 'y' => 0],
                    ['x' => 2, 'y' => 0],
                    ['x' => 3, 'y' => 1],
                ],
            ],
            '複数のtrueが含まれる場合' => [
                'inputData' => [
                    ['t' => 1, 'result' => false],
                    ['t' => 2, 'result' => true],
                    ['t' => 3, 'result' => false],
                    ['t' => 4, 'result' => true],
                    ['t' => 5, 'result' => false],
                ],
                'expectedPoints' => [
                    ['x' => 1, 'y' => 0],
                    ['x' => 2, 'y' => 0],
                    ['x' => 3, 'y' => 1],
                    ['x' => 4, 'y' => 0],
                    ['x' => 5, 'y' => 1],
                ],
            ],
        ];
    }

    /**
     * makeChartDataメソッドが正しくチャートデータを生成することをテストします。
     * @test
     * @dataProvider makeChartDataDataProvider
     * @return void
     */
    public function testMakeChartData(array $inputData, array $expectedPoints)
    {
        // CentipedeFormatterクラスのインスタンスを作成
        $formatter = new CentipedeFormatter();

        // 入力データを作成
        $data = $this->stepFactory->createManyFromArray($inputData);

        // 期待される結果を作成
        $expected = new CentipedeChartPointList(
            array_map(
                fn ($point) => new CentipedeChartPoint($point['x'], $point['y']),
                $expectedPoints
            )
        );

        // テスト対象メソッドを実行
        $result = $formatter->makeChartData($data);

        // 結果を検証
        $this->assertSame($expected->toArray(), $result->toArray());
    }
}
