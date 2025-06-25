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

        $this->assertSame(5, $property->getValue($rule));
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
     * 分数が最大値以下の場合にfailが呼ばれないことをテスト
     * @test
     * @dataProvider fractionLessThanOrEqualToMaxDataProvider
     */
    public function validateDoesNotCallFailWhenFractionIsLessThanOrEqualToMax(int $numerator, int $denominator)
    {
        $rule = new FractionMax(2);

        $failCalled = false;
        $fail = function ($message) use (&$failCalled) {
            $failCalled = true;

            return $message;
        };

        $rule->validate('fraction', [
            'numerator' => $numerator,
            'denominator' => $denominator,
        ], $fail);

        $this->assertFalse($failCalled, 'Fail closure should not be called when fraction is less than or equal to max');
    }

    /**
     * 分数が最大値より大きい場合にfailが呼ばれることをテスト
     * @test
     */
    public function validateCallsFailWhenFractionIsGreaterThanMax()
    {
        $rule = new FractionMax(2);

        $failCalled = false;
        $fail = function ($message) use (&$failCalled) {
            $failCalled = true;

            return $message;
        };

        $rule->validate('fraction', [
            'numerator' => 5,
            'denominator' => 2,
        ], $fail);

        $this->assertTrue($failCalled, 'Fail closure should be called when fraction is greater than max');
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
     * 分子または分母が数値でない場合にfailが呼ばれることをテスト
     * @test
     * @dataProvider nonNumericNumeratorOrDenominatorDataProvider
     */
    public function validateCallsFailWhenNumeratorOrDenominatorIsNotNumeric($numerator, $denominator)
    {
        $rule = new FractionMax(2);

        $failCalled = false;
        $fail = function ($message) use (&$failCalled) {
            $failCalled = true;

            return $message;
        };

        $rule->validate('fraction', [
            'numerator' => $numerator,
            'denominator' => $denominator,
        ], $fail);

        $this->assertTrue($failCalled, 'Fail closure should be called when numerator or denominator is not numeric');
    }

    /**
     * バリデーション失敗時に正しいエラーメッセージが設定されることをテスト
     * @test
     */
    public function validateSetsCorrectErrorMessage()
    {
        $rule = new FractionMax(5);

        // trans関数をモック
        $this->app->instance('translator', $translator = \Mockery::mock(\Illuminate\Contracts\Translation\Translator::class));
        $translator->shouldReceive('get')
            ->with('validation.fraction_max', ['max' => 5], null)
            ->once()
            ->andReturn('分数は5以下である必要があります。');

        $message = null;
        $fail = function ($msg) use (&$message) {
            $message = $msg;

            return $msg;
        };

        // バリデーションが失敗するケース
        $rule->validate('fraction', [
            'numerator' => 6,
            'denominator' => 1,
        ], $fail);

        $this->assertSame('分数は5以下である必要があります。', $message);
    }

    /**
     * 分数の境界値テスト用データプロバイダー
     * @return array
     */
    public static function fractionBoundaryDataProvider(): array
    {
        return [
            '最大値5の境界（分子5000、分母1000）- 有効' => [
                'max' => 5,
                'numerator' => 5000,
                'denominator' => 1000,
                'shouldFail' => false,
            ],
            '最大値5の境界（分子5001、分母1000）- 無効' => [
                'max' => 5,
                'numerator' => 5001,
                'denominator' => 1000,
                'shouldFail' => true,
            ],
            '最大値2の境界（分子2000、分母1000）- 有効' => [
                'max' => 2,
                'numerator' => 2000,
                'denominator' => 1000,
                'shouldFail' => false,
            ],
            '最大値2の境界（分子2001、分母1000）- 無効' => [
                'max' => 2,
                'numerator' => 2001,
                'denominator' => 1000,
                'shouldFail' => true,
            ],
        ];
    }

    /**
     * 分数の境界値テスト
     * 分母を1000に固定し、分子を変動させることで境界をテスト
     * @test
     * @dataProvider fractionBoundaryDataProvider
     */
    public function validateHandlesFractionBoundaryCorrectly(int $max, int $numerator, int $denominator, bool $shouldFail)
    {
        $rule = new FractionMax($max);

        $failCalled = false;
        $fail = function ($message) use (&$failCalled) {
            $failCalled = true;

            return $message;
        };

        $rule->validate('fraction', [
            'numerator' => $numerator,
            'denominator' => $denominator,
        ], $fail);

        if ($shouldFail) {
            $this->assertTrue($failCalled, "分子が{$numerator}、分母が{$denominator}の場合、バリデーションは失敗するべきです");
        } else {
            $this->assertFalse($failCalled, "分子が{$numerator}、分母が{$denominator}の場合、バリデーションは成功するべきです");
        }
    }
}
