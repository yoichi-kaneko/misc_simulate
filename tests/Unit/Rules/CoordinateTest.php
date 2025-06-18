<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\Coordinate;
use Tests\TestCase;

class CoordinateTest extends TestCase
{
    /**
     * コンストラクタが正しくプロパティを設定することをテスト
     * @test
     */
    public function constructorSetsPropertiesCorrectly()
    {
        $alpha_1 = ['numerator' => 3, 'denominator' => 1];
        $alpha_2 = ['numerator' => 2, 'denominator' => 1];
        $beta_1 = ['numerator' => 1, 'denominator' => 1];
        $beta_2 = ['numerator' => 4, 'denominator' => 1];

        $rule = new Coordinate($alpha_1, $alpha_2, $beta_1, $beta_2);

        // privateプロパティをテストするためにReflectionを使用
        $reflector = new \ReflectionClass(Coordinate::class);

        $alpha1Property = $reflector->getProperty('alpha_1');
        $alpha2Property = $reflector->getProperty('alpha_2');
        $beta1Property = $reflector->getProperty('beta_1');
        $beta2Property = $reflector->getProperty('beta_2');

        $this->assertEquals($alpha_1, $alpha1Property->getValue($rule));
        $this->assertEquals($alpha_2, $alpha2Property->getValue($rule));
        $this->assertEquals($beta_1, $beta1Property->getValue($rule));
        $this->assertEquals($beta_2, $beta2Property->getValue($rule));
    }

    /**
     * 座標が正しい位置関係（右下）にある場合のデータプロバイダー
     * @return array
     */
    public static function coordinatesInCorrectPositionDataProvider(): array
    {
        return [
            '基本的な右下の位置' => [
                'alpha_1' => ['numerator' => 3, 'denominator' => 1],
                'alpha_2' => ['numerator' => 2, 'denominator' => 1],
                'beta_1' => ['numerator' => 1, 'denominator' => 1],
                'beta_2' => ['numerator' => 4, 'denominator' => 1],
            ],
            '分数を使った右下の位置' => [
                'alpha_1' => ['numerator' => 5, 'denominator' => 2],
                'alpha_2' => ['numerator' => 1, 'denominator' => 2],
                'beta_1' => ['numerator' => 1, 'denominator' => 1],
                'beta_2' => ['numerator' => 3, 'denominator' => 2],
            ],
        ];
    }

    /**
     * 座標が正しい位置関係（右下）にある場合にtrueを返すことをテスト
     * @test
     * @dataProvider coordinatesInCorrectPositionDataProvider
     */
    public function passesReturnsTrueWhenCoordinatesAreInCorrectPosition(
        array $alpha_1,
        array $alpha_2,
        array $beta_1,
        array $beta_2
    ) {
        $rule = new Coordinate($alpha_1, $alpha_2, $beta_1, $beta_2);
        $this->assertTrue($rule->passes('attribute', null));
    }

    /**
     * 座標が正しくない位置関係にある場合のデータプロバイダー
     * @return array
     */
    public static function coordinatesInIncorrectPositionDataProvider(): array
    {
        return [
            '左下の位置' => [
                'alpha_1' => ['numerator' => 1, 'denominator' => 1],
                'alpha_2' => ['numerator' => 2, 'denominator' => 1],
                'beta_1' => ['numerator' => 3, 'denominator' => 1],
                'beta_2' => ['numerator' => 4, 'denominator' => 1],
            ],
            '右上の位置' => [
                'alpha_1' => ['numerator' => 3, 'denominator' => 1],
                'alpha_2' => ['numerator' => 4, 'denominator' => 1],
                'beta_1' => ['numerator' => 1, 'denominator' => 1],
                'beta_2' => ['numerator' => 2, 'denominator' => 1],
            ],
            '左上の位置' => [
                'alpha_1' => ['numerator' => 1, 'denominator' => 1],
                'alpha_2' => ['numerator' => 4, 'denominator' => 1],
                'beta_1' => ['numerator' => 3, 'denominator' => 1],
                'beta_2' => ['numerator' => 2, 'denominator' => 1],
            ],
            'X座標が同じ' => [
                'alpha_1' => ['numerator' => 3, 'denominator' => 1],
                'alpha_2' => ['numerator' => 2, 'denominator' => 1],
                'beta_1' => ['numerator' => 3, 'denominator' => 1],
                'beta_2' => ['numerator' => 4, 'denominator' => 1],
            ],
            'Y座標が同じ' => [
                'alpha_1' => ['numerator' => 3, 'denominator' => 1],
                'alpha_2' => ['numerator' => 4, 'denominator' => 1],
                'beta_1' => ['numerator' => 1, 'denominator' => 1],
                'beta_2' => ['numerator' => 4, 'denominator' => 1],
            ],
            '両方の座標が同じ' => [
                'alpha_1' => ['numerator' => 3, 'denominator' => 1],
                'alpha_2' => ['numerator' => 4, 'denominator' => 1],
                'beta_1' => ['numerator' => 3, 'denominator' => 1],
                'beta_2' => ['numerator' => 4, 'denominator' => 1],
            ],
        ];
    }

    /**
     * 座標が正しくない位置関係にある場合にfalseを返すことをテスト
     * @test
     * @dataProvider coordinatesInIncorrectPositionDataProvider
     */
    public function passesReturnsFalseWhenCoordinatesAreInIncorrectPosition(
        array $alpha_1,
        array $alpha_2,
        array $beta_1,
        array $beta_2
    ) {
        $rule = new Coordinate($alpha_1, $alpha_2, $beta_1, $beta_2);
        $this->assertFalse($rule->passes('attribute', null));
    }

    /**
     * メッセージメソッドが正しい翻訳を返すことをテスト
     * @test
     */
    public function messageReturnsCorrectTranslation()
    {
        $alpha_1 = ['numerator' => 3, 'denominator' => 1];
        $alpha_2 = ['numerator' => 2, 'denominator' => 1];
        $beta_1 = ['numerator' => 1, 'denominator' => 1];
        $beta_2 = ['numerator' => 4, 'denominator' => 1];

        $rule = new Coordinate($alpha_1, $alpha_2, $beta_1, $beta_2);

        // trans関数をモック
        $this->app->instance('translator', $translator = \Mockery::mock(\Illuminate\Contracts\Translation\Translator::class));
        $translator->shouldReceive('get')
            ->with('validation.invalid_coordinate', [], null)
            ->once()
            ->andReturn('座標の位置関係が不正です。');

        $this->assertEquals('座標の位置関係が不正です。', $rule->message());
    }
}
