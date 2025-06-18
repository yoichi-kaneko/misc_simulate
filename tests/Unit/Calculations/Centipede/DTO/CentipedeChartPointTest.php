<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Centipede\DTO;

use App\Calculations\Centipede\DTO\CentipedeChartPoint;
use PHPUnit\Framework\TestCase;

class CentipedeChartPointTest extends TestCase
{
    private CentipedeChartPoint $chartPoint;
    private int $x;
    private int $y;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用の値を設定
        $this->x = 10;
        $this->y = 20;

        // CentipedeChartPointのインスタンスを作成
        $this->chartPoint = new CentipedeChartPoint(
            $this->x,
            $this->y
        );
    }

    /**
     * getX()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetX()
    {
        $this->assertSame($this->x, $this->chartPoint->getX());
    }

    /**
     * getY()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetY()
    {
        $this->assertSame($this->y, $this->chartPoint->getY());
    }

    /**
     * toArray()メソッドが正しく配列を返すことをテストします。
     * @test
     * @return void
     */
    public function testToArray()
    {
        $expected = [
            'x' => $this->x,
            'y' => $this->y,
        ];
        $this->assertSame($expected, $this->chartPoint->toArray());
    }
}