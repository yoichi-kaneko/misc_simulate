<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations;

use App\Calculations\Nash;
use App\Calculations\Nash\Simulator\NashSimulator;
use App\Calculations\Nash\Formatter\NashFormatter;
use App\Calculations\Nash\DTO\NashSimulationResult;
use Illuminate\Contracts\Container\BindingResolutionException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class NashTest extends TestCase
{
    /** @var NashSimulator&MockObject $simulator */
    private NashSimulator $simulator;

    /** @var NashFormatter&MockObject $formatter */
    private NashFormatter $formatter;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        // シミュレーターとフォーマッターのインスタンスを作成
        $this->simulator = $this->createMock(NashSimulator::class);
        $this->formatter = $this->createMock(NashFormatter::class);
    }

    /**
     * テストの前にNashクラスのインスタンスを作成します。
     * @return Nash
     * @throws BindingResolutionException
     */
    private function makeInstance(): Nash
    {
        // テスト対象のNashクラスのインスタンスを作成
        $this->instance(NashSimulator::class, $this->simulator);
        $this->instance(NashFormatter::class, $this->formatter);

        /** @var Nash */
        return app()->make(Nash::class);
    }

    /**
     * Nashクラスがシミュレーターとフォーマッターを正しく使用することをテストします。
     * @test
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function testRunDelegation()
    {
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
        $this->simulator->expects($this->once())
            ->method('run')
            ->with($alpha_1, $alpha_2, $beta_1, $beta_2, $rho)
            ->willReturn($simulationResult);

        // フォーマッターのモックの振る舞いを設定
        $this->formatter->expects($this->once())
            ->method('format')
            ->with($simulationResult)
            ->willReturn($expectedOutput);

        // Nashクラスのインスタンスを作成
        $nash = $this->makeInstance();

        // runメソッドを実行
        $result = $nash->run($alpha_1, $alpha_2, $beta_1, $beta_2, $rho);

        // 結果を検証
        $this->assertEquals($expectedOutput, $result);
    }


    /**
     * シミュレーターが例外をスローした場合のテスト
     * @test
     * @return void
     * @throws BindingResolutionException
     */
    public function testRunWithSimulatorException()
    {
        // テスト用の入力データ
        $alpha_1 = ['numerator' => '1', 'denominator' => '1'];
        $alpha_2 = ['numerator' => '2', 'denominator' => '1'];
        $beta_1 = ['numerator' => '3', 'denominator' => '1'];
        $beta_2 = ['numerator' => '4', 'denominator' => '1'];
        $rho = ['numerator' => '1', 'denominator' => '2'];

        // シミュレーターが例外をスローするように設定
        $this->simulator->expects($this->once())
            ->method('run')
            ->with($alpha_1, $alpha_2, $beta_1, $beta_2, $rho)
            ->willThrowException(new \Exception('Simulation error'));

        // Nashクラスのインスタンスを作成
        $nash = $this->makeInstance();

        // 例外が発生することを期待
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Simulation error');

        // runメソッドを実行
        $nash->run($alpha_1, $alpha_2, $beta_1, $beta_2, $rho);
    }
}
