<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\FractionMax;
use Tests\TestCase;

class FractionMaxTest extends TestCase
{
    /**
     * コンストラクタが正しく最大値を設定することをテスト
     * @test
     */
    public function constructorSetsMaxValueCorrectly()
    {
        $rule = new FractionMax(5);

        // privateプロパティをテストするためにReflectionを使用
        $reflector = new \ReflectionClass(FractionMax::class);
        $property = $reflector->getProperty('max');

        $this->assertEquals(5, $property->getValue($rule));
    }

    /**
     * 分数が最大値以下の場合のデータプロバイダー
     * @return array
     */
    public static function fractionLessThanOrEqualToMaxDataProvider(): array
    {
        return [
            '最大値より小さい分数' => [
                'numerator' => 3,
                'denominator' => 2,
            ],
            '最大値と等しい分数' => [
                'numerator' => 4,
                'denominator' => 2,
            ],
        ];
    }

    /**
     * 分数が最大値以下の場合にtrueを返すことをテスト
     * @test
     * @dataProvider fractionLessThanOrEqualToMaxDataProvider
     */
    public function passesReturnsTrueWhenFractionIsLessThanOrEqualToMax(int $numerator, int $denominator)
    {
        $rule = new FractionMax(2);

        $this->assertTrue($rule->passes('fraction', [
            'numerator' => $numerator,
            'denominator' => $denominator,
        ]));
    }

    /**
     * 分数が最大値より大きい場合にfalseを返すことをテスト
     * @test
     */
    public function passesReturnsFalseWhenFractionIsGreaterThanMax()
    {
        $rule = new FractionMax(2);

        $this->assertFalse($rule->passes('fraction', [
            'numerator' => 5,
            'denominator' => 2,
        ]));
    }

    /**
     * 分子または分母が数値でない場合のデータプロバイダー
     * @return array
     */
    public static function nonNumericNumeratorOrDenominatorDataProvider(): array
    {
        return [
            '分子が数値でない場合' => [
                'numerator' => 'abc',
                'denominator' => 2,
            ],
            '分母が数値でない場合' => [
                'numerator' => 3,
                'denominator' => 'xyz',
            ],
            '両方とも数値でない場合' => [
                'numerator' => 'abc',
                'denominator' => 'xyz',
            ],
        ];
    }

    /**
     * 分子または分母が数値でない場合にfalseを返すことをテスト
     * @test
     * @dataProvider nonNumericNumeratorOrDenominatorDataProvider
     */
    public function passesReturnsFalseWhenNumeratorOrDenominatorIsNotNumeric($numerator, $denominator)
    {
        $rule = new FractionMax(2);

        $this->assertFalse($rule->passes('fraction', [
            'numerator' => $numerator,
            'denominator' => $denominator,
        ]));
    }

    /**
     * メッセージメソッドが正しい翻訳を返すことをテスト
     * @test
     */
    public function messageReturnsCorrectTranslation()
    {
        $rule = new FractionMax(5);

        // trans関数をモック
        $this->app->instance('translator', $translator = \Mockery::mock(\Illuminate\Contracts\Translation\Translator::class));
        $translator->shouldReceive('get')
            ->with('validation.fraction_max', ['max' => 5], null)
            ->once()
            ->andReturn('分数は5以下である必要があります。');

        $this->assertEquals('分数は5以下である必要があります。', $rule->message());
    }
}
