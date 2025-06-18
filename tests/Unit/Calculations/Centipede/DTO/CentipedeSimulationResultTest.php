<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Centipede\DTO;

use App\Calculations\Centipede\DTO\CentipedeSimulationResult;
use App\Calculations\Centipede\DTO\CentipedeSimulationStep;
use PHPUnit\Framework\TestCase;

class CentipedeSimulationResultTest extends TestCase
{
    private CentipedeSimulationResult $result;
    private float $cognitiveUnitValue;
    private string $cognitiveUnitLatexText;
    private float $averageOfReversedCausality;
    private array $data;
    private array $chartData;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用の値を設定
        $this->cognitiveUnitValue = 0.75;
        $this->cognitiveUnitLatexText = 'C_u = 0.75';
        $this->averageOfReversedCausality = 0.42;
        $this->data = [
            new CentipedeSimulationStep(1, 3, 'left1', 'right1', true),
            new CentipedeSimulationStep(2, 5, 'left2', 'right2', false),
            new CentipedeSimulationStep(3, 7, 'left3', 'right3', true),
        ];
        $this->chartData = [
            'labels' => ['A', 'B', 'C'],
            'datasets' => [
                [
                    'label' => 'Dataset 1',
                    'data' => [10, 20, 30],
                ],
            ],
        ];

        // CentipedeSimulationResultのインスタンスを作成
        $this->result = new CentipedeSimulationResult(
            $this->cognitiveUnitValue,
            $this->cognitiveUnitLatexText,
            $this->averageOfReversedCausality,
            $this->data,
            $this->chartData
        );
    }

    /**
     * getCognitiveUnitValue()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetCognitiveUnitValue()
    {
        $this->assertSame($this->cognitiveUnitValue, $this->result->getCognitiveUnitValue());
    }

    /**
     * getCognitiveUnitLatexText()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetCognitiveUnitLatexText()
    {
        $this->assertSame($this->cognitiveUnitLatexText, $this->result->getCognitiveUnitLatexText());
    }

    /**
     * getAverageOfReversedCausality()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetAverageOfReversedCausality()
    {
        $this->assertSame($this->averageOfReversedCausality, $this->result->getAverageOfReversedCausality());
    }

    /**
     * getData()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetData()
    {
        $this->assertSame($this->data, $this->result->getData());
    }

    /**
     * getChartData()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetChartData()
    {
        $this->assertSame($this->chartData, $this->result->getChartData());
    }
}
