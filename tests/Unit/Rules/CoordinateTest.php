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

        $this->assertSame($alpha_1, $alpha1Property->getValue($rule));
        $this->assertSame($alpha_2, $alpha2Property->getValue($rule));
        $this->assertSame($beta_1, $beta1Property->getValue($rule));
        $this->assertSame($beta_2, $beta2Property->getValue($rule));
    }

    /**
     * 座標の位置関係をテストするためのデータプロバイダー
     * @return array
     */
    public static function coordinatesPositionDataProvider(): array
    {
        return [
            'アルファが基本的な右下の位置' => [
                'alpha_1' => ['numerator' => 3, 'denominator' => 1],
                'alpha_2' => ['numerator' => 2, 'denominator' => 1],
                'beta_1' => ['numerator' => 1, 'denominator' => 1],
                'beta_2' => ['numerator' => 4, 'denominator' => 1],
                'expected' => true,
            ],
            'アルファが分数を使った右下の位置' => [
                'alpha_1' => ['numerator' => 5, 'denominator' => 2],
                'alpha_2' => ['numerator' => 1, 'denominator' => 2],
                'beta_1' => ['numerator' => 1, 'denominator' => 1],
                'beta_2' => ['numerator' => 3, 'denominator' => 2],
                'expected' => true,
            ],
            'アルファが左下の位置' => [
                'alpha_1' => ['numerator' => 1, 'denominator' => 1],
                'alpha_2' => ['numerator' => 2, 'denominator' => 1],
                'beta_1' => ['numerator' => 3, 'denominator' => 1],
                'beta_2' => ['numerator' => 4, 'denominator' => 1],
                'expected' => false,
            ],
            'アルファが右上の位置' => [
                'alpha_1' => ['numerator' => 3, 'denominator' => 1],
                'alpha_2' => ['numerator' => 4, 'denominator' => 1],
                'beta_1' => ['numerator' => 1, 'denominator' => 1],
                'beta_2' => ['numerator' => 2, 'denominator' => 1],
                'expected' => false,
            ],
            'アルファが左上の位置' => [
                'alpha_1' => ['numerator' => 1, 'denominator' => 1],
                'alpha_2' => ['numerator' => 4, 'denominator' => 1],
                'beta_1' => ['numerator' => 3, 'denominator' => 1],
                'beta_2' => ['numerator' => 2, 'denominator' => 1],
                'expected' => false,
            ],
            'X座標が同じ' => [
                'alpha_1' => ['numerator' => 3, 'denominator' => 1],
                'alpha_2' => ['numerator' => 2, 'denominator' => 1],
                'beta_1' => ['numerator' => 3, 'denominator' => 1],
                'beta_2' => ['numerator' => 4, 'denominator' => 1],
                'expected' => false,
            ],
            'Y座標が同じ' => [
                'alpha_1' => ['numerator' => 3, 'denominator' => 1],
                'alpha_2' => ['numerator' => 4, 'denominator' => 1],
                'beta_1' => ['numerator' => 1, 'denominator' => 1],
                'beta_2' => ['numerator' => 4, 'denominator' => 1],
                'expected' => false,
            ],
            '両方の座標が同じ' => [
                'alpha_1' => ['numerator' => 3, 'denominator' => 1],
                'alpha_2' => ['numerator' => 4, 'denominator' => 1],
                'beta_1' => ['numerator' => 3, 'denominator' => 1],
                'beta_2' => ['numerator' => 4, 'denominator' => 1],
                'expected' => false,
            ],
        ];
    }

    /**
     * 座標の位置関係に基づいて正しい結果を返すことをテスト
     * @test
     * @dataProvider coordinatesPositionDataProvider
     */
    public function validateCallsFailClosureBasedOnCoordinatesPosition(
        array $alpha_1,
        array $alpha_2,
        array $beta_1,
        array $beta_2,
        bool $expected
    ) {
        $rule = new Coordinate($alpha_1, $alpha_2, $beta_1, $beta_2);

        $failCalled = false;
        $fail = function ($message) use (&$failCalled) {
            $failCalled = true;

            return $message;
        };

        $rule->validate('attribute', null, $fail);

        // $expected が true の場合は $fail が呼ばれないはず（バリデーション成功）
        // $expected が false の場合は $fail が呼ばれるはず（バリデーション失敗）
        $this->assertSame(! $expected, $failCalled);
    }

    /**
     * バリデーション失敗時に正しいエラーメッセージが設定されることをテスト
     * @test
     */
    public function validateSetsCorrectErrorMessage()
    {
        $alpha_1 = ['numerator' => 1, 'denominator' => 1]; // 左下の位置（バリデーション失敗するケース）
        $alpha_2 = ['numerator' => 2, 'denominator' => 1];
        $beta_1 = ['numerator' => 3, 'denominator' => 1];
        $beta_2 = ['numerator' => 4, 'denominator' => 1];

        $rule = new Coordinate($alpha_1, $alpha_2, $beta_1, $beta_2);

        // trans関数をモック
        $this->app->instance('translator', $translator = \Mockery::mock(\Illuminate\Contracts\Translation\Translator::class));
        $translator->shouldReceive('get')
            ->with('validation.invalid_coordinate', [], null)
            ->once()
            ->andReturn('座標の位置関係が不正です。');

        $message = null;
        $fail = function ($msg) use (&$message) {
            $message = $msg;

            return $msg;
        };

        $rule->validate('attribute', null, $fail);

        $this->assertSame('座標の位置関係が不正です。', $message);
    }

    /**
     * 分数の境界値テスト用データプロバイダー
     * @return array
     */
    public static function fractionBoundaryDataProvider(): array
    {
        return [
            'X座標の境界（アルファが右側ぎりぎり）- 有効' => [
                'alpha_1' => ['numerator' => 1001, 'denominator' => 1000],
                'alpha_2' => ['numerator' => 500, 'denominator' => 1000],
                'beta_1' => ['numerator' => 1000, 'denominator' => 1000],
                'beta_2' => ['numerator' => 2000, 'denominator' => 1000],
                'expected' => true,
            ],
            'X座標の境界（アルファが右側ぎりぎり）- 無効' => [
                'alpha_1' => ['numerator' => 1000, 'denominator' => 1000],
                'alpha_2' => ['numerator' => 500, 'denominator' => 1000],
                'beta_1' => ['numerator' => 1000, 'denominator' => 1000],
                'beta_2' => ['numerator' => 2000, 'denominator' => 1000],
                'expected' => false,
            ],
            'Y座標の境界（アルファが下側ぎりぎり）- 有効' => [
                'alpha_1' => ['numerator' => 2000, 'denominator' => 1000],
                'alpha_2' => ['numerator' => 999, 'denominator' => 1000],
                'beta_1' => ['numerator' => 1000, 'denominator' => 1000],
                'beta_2' => ['numerator' => 1000, 'denominator' => 1000],
                'expected' => true,
            ],
            'Y座標の境界（アルファが下側ぎりぎり）- 無効' => [
                'alpha_1' => ['numerator' => 2000, 'denominator' => 1000],
                'alpha_2' => ['numerator' => 1000, 'denominator' => 1000],
                'beta_1' => ['numerator' => 1000, 'denominator' => 1000],
                'beta_2' => ['numerator' => 1000, 'denominator' => 1000],
                'expected' => false,
            ],
        ];
    }

    /**
     * 分数の境界値テスト
     * 分母を1000に固定し、分子を変動させることで境界をテスト
     * @test
     * @dataProvider fractionBoundaryDataProvider
     */
    public function validateHandlesFractionBoundaryCorrectly(
        array $alpha_1,
        array $alpha_2,
        array $beta_1,
        array $beta_2,
        bool $expected
    ) {
        $rule = new Coordinate($alpha_1, $alpha_2, $beta_1, $beta_2);

        $failCalled = false;
        $fail = function ($message) use (&$failCalled) {
            $failCalled = true;

            return $message;
        };

        $rule->validate('attribute', null, $fail);

        // $expected が true の場合は $fail が呼ばれないはず（バリデーション成功）
        // $expected が false の場合は $fail が呼ばれるはず（バリデーション失敗）
        $this->assertSame(! $expected, $failCalled);
    }
}
