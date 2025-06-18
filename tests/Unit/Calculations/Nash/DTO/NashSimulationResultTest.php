<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations\Nash\DTO;

use App\Calculations\Nash\DTO\NashSimulationResult;
use Phospr\Fraction;
use PHPUnit\Framework\TestCase;

class NashSimulationResultTest extends TestCase
{
    private NashSimulationResult $result;
    private Fraction $alpha_x;
    private Fraction $alpha_y;
    private Fraction $beta_x;
    private Fraction $beta_y;
    private Fraction $rho_beta_x;
    private Fraction $rho_beta_y;
    private Fraction $gamma1_x;
    private Fraction $gamma2_y;
    private array $midpoint;
    private Fraction $a_rho;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用の値を設定
        $this->alpha_x = new Fraction(1, 2);
        $this->alpha_y = new Fraction(2, 3);
        $this->beta_x = new Fraction(3, 4);
        $this->beta_y = new Fraction(4, 5);
        $this->rho_beta_x = new Fraction(5, 6);
        $this->rho_beta_y = new Fraction(6, 7);
        $this->gamma1_x = new Fraction(7, 8);
        $this->gamma2_y = new Fraction(8, 9);
        $this->midpoint = [
            'x' => new Fraction(9, 10),
            'y' => new Fraction(10, 11),
        ];
        $this->a_rho = new Fraction(11, 12);

        // NashSimulationResultのインスタンスを作成
        $this->result = new NashSimulationResult(
            $this->alpha_x,
            $this->alpha_y,
            $this->beta_x,
            $this->beta_y,
            $this->rho_beta_x,
            $this->rho_beta_y,
            $this->gamma1_x,
            $this->gamma2_y,
            $this->midpoint,
            $this->a_rho
        );
    }

    /**
     * getAlphaX()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetAlphaX()
    {
        $this->assertSame($this->alpha_x, $this->result->getAlphaX());
        $this->assertEquals($this->alpha_x->getNumerator(), $this->result->getAlphaX()->getNumerator());
        $this->assertEquals($this->alpha_x->getDenominator(), $this->result->getAlphaX()->getDenominator());
    }

    /**
     * getAlphaY()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetAlphaY()
    {
        $this->assertSame($this->alpha_y, $this->result->getAlphaY());
        $this->assertEquals($this->alpha_y->getNumerator(), $this->result->getAlphaY()->getNumerator());
        $this->assertEquals($this->alpha_y->getDenominator(), $this->result->getAlphaY()->getDenominator());
    }

    /**
     * getBetaX()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetBetaX()
    {
        $this->assertSame($this->beta_x, $this->result->getBetaX());
        $this->assertEquals($this->beta_x->getNumerator(), $this->result->getBetaX()->getNumerator());
        $this->assertEquals($this->beta_x->getDenominator(), $this->result->getBetaX()->getDenominator());
    }

    /**
     * getBetaY()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetBetaY()
    {
        $this->assertSame($this->beta_y, $this->result->getBetaY());
        $this->assertEquals($this->beta_y->getNumerator(), $this->result->getBetaY()->getNumerator());
        $this->assertEquals($this->beta_y->getDenominator(), $this->result->getBetaY()->getDenominator());
    }

    /**
     * getRhoBetaX()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetRhoBetaX()
    {
        $this->assertSame($this->rho_beta_x, $this->result->getRhoBetaX());
        $this->assertEquals($this->rho_beta_x->getNumerator(), $this->result->getRhoBetaX()->getNumerator());
        $this->assertEquals($this->rho_beta_x->getDenominator(), $this->result->getRhoBetaX()->getDenominator());
    }

    /**
     * getRhoBetaY()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetRhoBetaY()
    {
        $this->assertSame($this->rho_beta_y, $this->result->getRhoBetaY());
        $this->assertEquals($this->rho_beta_y->getNumerator(), $this->result->getRhoBetaY()->getNumerator());
        $this->assertEquals($this->rho_beta_y->getDenominator(), $this->result->getRhoBetaY()->getDenominator());
    }

    /**
     * getGamma1X()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetGamma1X()
    {
        $this->assertSame($this->gamma1_x, $this->result->getGamma1X());
        $this->assertEquals($this->gamma1_x->getNumerator(), $this->result->getGamma1X()->getNumerator());
        $this->assertEquals($this->gamma1_x->getDenominator(), $this->result->getGamma1X()->getDenominator());
    }

    /**
     * getGamma2Y()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetGamma2Y()
    {
        $this->assertSame($this->gamma2_y, $this->result->getGamma2Y());
        $this->assertEquals($this->gamma2_y->getNumerator(), $this->result->getGamma2Y()->getNumerator());
        $this->assertEquals($this->gamma2_y->getDenominator(), $this->result->getGamma2Y()->getDenominator());
    }

    /**
     * getMidpoint()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetMidpoint()
    {
        $this->assertSame($this->midpoint, $this->result->getMidpoint());
        $this->assertEquals($this->midpoint['x']->getNumerator(), $this->result->getMidpoint()['x']->getNumerator());
        $this->assertEquals($this->midpoint['x']->getDenominator(), $this->result->getMidpoint()['x']->getDenominator());
        $this->assertEquals($this->midpoint['y']->getNumerator(), $this->result->getMidpoint()['y']->getNumerator());
        $this->assertEquals($this->midpoint['y']->getDenominator(), $this->result->getMidpoint()['y']->getDenominator());
    }

    /**
     * getARho()メソッドが正しく値を返すことをテストします。
     * @test
     * @return void
     */
    public function testGetARho()
    {
        $this->assertSame($this->a_rho, $this->result->getARho());
        $this->assertEquals($this->a_rho->getNumerator(), $this->result->getARho()->getNumerator());
        $this->assertEquals($this->a_rho->getDenominator(), $this->result->getARho()->getDenominator());
    }
}
