<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations;

use App\Calculations\Nash;
use App\Calculations\Nash\Simulator\NashSimulator;
use App\Calculations\Nash\Formatter\NashFormatter;
use App\Calculations\Nash\DTO\NashSimulationResult;
use PHPUnit\Framework\TestCase;
use Phospr\Fraction;

class NashTest extends TestCase
{
    /**
     * Nashクラスがシミュレーターとフォーマッターを正しく使用することをテストします。
     * @test
     * @return void
     */
    public function testRunDelegation()
    {
        // モックのシミュレーターとフォーマッターを作成
        $simulator = $this->createMock(NashSimulator::class);
        $formatter = $this->createMock(NashFormatter::class);

        // テスト用の入力データ
        $alpha_1 = ['numerator' => '1', 'denominator' => '1'];
        $alpha_2 = ['numerator' => '2', 'denominator' => '1'];
        $beta_1 = ['numerator' => '3', 'denominator' => '1'];
        $beta_2 = ['numerator' => '4', 'denominator' => '1'];
        $rho = ['numerator' => '1', 'denominator' => '2'];

        // モックのシミュレーション結果
        $simulationResult = $this->createMock(NashSimulationResult::class);

        // モックの期待される出力
        $expectedOutput = [
            'report_params' => ['a_rho' => '3.500'],
            'render_params' => [
                ['title' => 'alpha', 'x' => 1.0, 'y' => 2.0, 'display_text' => '[1.000, 2.000]'],
                // 他のパラメータは省略
            ]
        ];

        // シミュレーターのモックの振る舞いを設定
        $simulator->expects($this->once())
            ->method('run')
            ->with($alpha_1, $alpha_2, $beta_1, $beta_2, $rho)
            ->willReturn($simulationResult);

        // フォーマッターのモックの振る舞いを設定
        $formatter->expects($this->once())
            ->method('format')
            ->with($simulationResult)
            ->willReturn($expectedOutput);

        // テスト対象のNashクラスのインスタンスを作成
        $nash = new Nash($simulator, $formatter);

        // runメソッドを実行
        $result = $nash->run($alpha_1, $alpha_2, $beta_1, $beta_2, $rho);

        // 結果を検証
        $this->assertEquals($expectedOutput, $result);
    }
}
