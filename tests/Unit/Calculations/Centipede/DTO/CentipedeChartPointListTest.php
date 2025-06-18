<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Centipede\DTO;

use App\Calculations\Centipede\DTO\CentipedeChartPoint;
use App\Calculations\Centipede\DTO\CentipedeChartPointList;
use PHPUnit\Framework\TestCase;
use Tests\Traits\ArrayObjectCheckTrait;

class CentipedeChartPointListTest extends TestCase
{
    use ArrayObjectCheckTrait;

    private CentipedeChartPointList $chartPointList;
    private array $points;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のCentipedeChartPointを作成
        $this->points = [
            new CentipedeChartPoint(1, 2),
            new CentipedeChartPoint(3, 4),
            new CentipedeChartPoint(5, 6)
        ];

        // CentipedeChartPointListのインスタンスを作成
        $this->chartPointList = new CentipedeChartPointList($this->points);
    }

    /**
     * コンストラクタが正しく動作することをテストします。
     * @test
     * @return void
     */
    public function testConstructor()
    {
        $list = new CentipedeChartPointList($this->points);
        $this->assertInstanceOf(CentipedeChartPointList::class, $list);
    }

    /**
     * 無効な要素を含む配列でコンストラクタを呼び出すと例外が発生することをテストします。
     * @test
     * @return void
     */
    public function testConstructorWithInvalidItems()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $invalidPoints = [
            new CentipedeChartPoint(1, 2),
            'not a CentipedeChartPoint', // 無効な要素
            new CentipedeChartPoint(5, 6)
        ];
        
        new CentipedeChartPointList($invalidPoints);
    }

    /**
     * getPoints()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetPoints()
    {
        $this->assertSame($this->points, $this->chartPointList->getPoints());
    }

    /**
     * count()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testCount()
    {
        $this->assertSame(count($this->points), count($this->chartPointList));
    }

    /**
     * getIterator()メソッドが正しくイテレータを返すことをテストします。
     * @test
     * @return void
     */
    public function testGetIterator()
    {
        $iterator = $this->chartPointList->getIterator();
        $this->assertInstanceOf(\ArrayIterator::class, $iterator);
        
        // イテレータから取得した要素が元の配列と一致することを確認
        $iteratedPoints = iterator_to_array($iterator);
        $this->assertSame($this->points, $iteratedPoints);
    }

    /**
     * toArray()メソッドが正しく配列を返すことをテストします。
     * @test
     * @return void
     */
    public function testToArray()
    {
        $expected = [
            ['x' => 1, 'y' => 2],
            ['x' => 3, 'y' => 4],
            ['x' => 5, 'y' => 6]
        ];
        
        $this->assertSame($expected, $this->chartPointList->toArray());
        $this->assertArrayHasNoObjects($this->chartPointList->toArray());
    }
}