<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Centipede\DTO;

use App\Calculations\Centipede\DTO\CentipedeSimulationStep;
use PHPUnit\Framework\TestCase;
use Tests\Traits\ArrayObjectCheckTrait;

class CentipedeSimulationStepTest extends TestCase
{
    use ArrayObjectCheckTrait;

    private CentipedeSimulationStep $simulationStep;
    private int $t;
    private int $maxNuValue;
    private string $leftSideValue;
    private string $rightSideValue;
    private bool $result;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用の値を設定
        $this->t = 5;
        $this->maxNuValue = 10;
        $this->leftSideValue = "left value";
        $this->rightSideValue = "right value";
        $this->result = true;

        // CentipedeSimulationStepのインスタンスを作成
        $this->simulationStep = new CentipedeSimulationStep(
            $this->t,
            $this->maxNuValue,
            $this->leftSideValue,
            $this->rightSideValue,
            $this->result
        );
    }

    /**
     * getT()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetT()
    {
        $this->assertSame($this->t, $this->simulationStep->getT());
    }

    /**
     * getMaxNuValue()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetMaxNuValue()
    {
        $this->assertSame($this->maxNuValue, $this->simulationStep->getMaxNuValue());
    }

    /**
     * getLeftSideValue()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetLeftSideValue()
    {
        $this->assertSame($this->leftSideValue, $this->simulationStep->getLeftSideValue());
    }

    /**
     * getRightSideValue()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetRightSideValue()
    {
        $this->assertSame($this->rightSideValue, $this->simulationStep->getRightSideValue());
    }

    /**
     * getResult()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetResult()
    {
        $this->assertSame($this->result, $this->simulationStep->getResult());
    }

    /**
     * toArray()メソッドが正しく配列を返すことをテストします。
     * @test
     * @return void
     */
    public function testToArray()
    {
        $expected = [
            't' => $this->t,
            'max_nu_value' => $this->maxNuValue,
            'left_side_value' => $this->leftSideValue,
            'right_side_value' => $this->rightSideValue,
            'result' => $this->result,
        ];
        $this->assertSame($expected, $this->simulationStep->toArray());
        $this->assertArrayHasNoObjects($this->simulationStep->toArray());
    }
}
