<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\CalculateNashRequest;
use App\Rules\Coordinate;
use App\Rules\FractionMax;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CalculateNashRequestTest extends TestCase
{
    /**
     * authorizeメソッドがtrueを返すことをテストします。
     * @test
     * @return void
     */
    public function testAuthorizeReturnsTrue()
    {
        $request = new CalculateNashRequest();
        $this->assertTrue($request->authorize());
    }

    /**
     * rulesメソッドが期待される検証ルールを返すことをテストします。
     * @test
     * @return void
     */
    public function testRulesReturnsExpectedRules()
    {
        // Coordinate コンストラクタでnull値を避けるためにモック入力データでリクエストを作成
        $request = new CalculateNashRequest();
        $request->merge([
            'alpha_1' => ['numerator' => 1, 'denominator' => 2],
            'alpha_2' => ['numerator' => 3, 'denominator' => 4],
            'beta_1' => ['numerator' => 1, 'denominator' => 4],
            'beta_2' => ['numerator' => 7, 'denominator' => 8],
            'rho' => ['numerator' => 1, 'denominator' => 2],
        ]);

        $rules = $request->rules();

        // すべての期待されるルールが存在することを確認
        $this->assertArrayHasKey('alpha_1.numerator', $rules);
        $this->assertArrayHasKey('alpha_1.denominator', $rules);
        $this->assertArrayHasKey('alpha_2.numerator', $rules);
        $this->assertArrayHasKey('alpha_2.denominator', $rules);
        $this->assertArrayHasKey('beta_1.numerator', $rules);
        $this->assertArrayHasKey('beta_1.denominator', $rules);
        $this->assertArrayHasKey('beta_2.numerator', $rules);
        $this->assertArrayHasKey('beta_2.denominator', $rules);
        $this->assertArrayHasKey('rho.numerator', $rules);
        $this->assertArrayHasKey('rho.denominator', $rules);
        $this->assertArrayHasKey('alpha_1', $rules);
        $this->assertArrayHasKey('alpha_2', $rules);
        $this->assertArrayHasKey('beta_1', $rules);
        $this->assertArrayHasKey('beta_2', $rules);
        $this->assertArrayHasKey('rho', $rules);

        // 分子と分母のルールが正しいことを確認
        $this->assertSame('required|integer|min:1|max:1000', $rules['alpha_1.numerator']);
        $this->assertSame('required|integer|min:1|max:1000', $rules['alpha_1.denominator']);

        // FractionMaxルールがすべての分数フィールドに適用されていることを確認
        $this->assertContains('required', $rules['alpha_1']);
        $this->assertContains('array', $rules['alpha_1']);
        $this->assertTrue(
            collect($rules['alpha_1'])->contains(fn ($r) => $r instanceof FractionMax),
            'alpha_1 に FractionMax ルールが含まれていません'
        );

        // Coordinateルールがbeta_2に適用されていることを確認
        $this->assertContains('required', $rules['beta_2']);
        $this->assertContains('array', $rules['beta_2']);
        $this->assertTrue(
            collect($rules['beta_2'])->contains(fn ($r) => $r instanceof FractionMax),
            'beta_2 に FractionMax ルールが含まれていません'
        );
        $this->assertTrue(
            collect($rules['beta_2'])->contains(fn ($r) => $r instanceof Coordinate),
            'beta_2 に Coordinate ルールが含まれていません'
        );
    }

    /**
     * 有効なデータでバリデーションが通ることをテストします。
     * @test
     * @return void
     */
    public function testValidationPassesWithValidData()
    {
        $data = [
            'alpha_1' => ['numerator' => 1, 'denominator' => 2],
            'alpha_2' => ['numerator' => 3, 'denominator' => 4],
            'beta_1' => ['numerator' => 1, 'denominator' => 4],
            'beta_2' => ['numerator' => 7, 'denominator' => 8],
            'rho' => ['numerator' => 1, 'denominator' => 2],
        ];

        $request = new CalculateNashRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $this->assertTrue($validator->passes());
    }

    /**
     * 無効なデータでバリデーションが失敗することをテストします。（単体のケース）
     * @test
     * @dataProvider validationFailsWithSingleInvalidDataProvider
     * @param array $data
     * @return void
     */
    public function testValidationFails_値単体のケース(array $data): void
    {
        $request = new CalculateNashRequest();
        $request->merge($data);

        $rules = array_filter($request->rules(), function ($attribute) {
            return str_contains($attribute, '.');
        }, ARRAY_FILTER_USE_KEY);

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->passes());
    }

    /**
     * 無効なデータでバリデーションが失敗することをテストするためのデータプロバイダです。
     * @return array
     */
    public static function validationFailsWithSingleInvalidDataProvider(): array
    {
        $base_data = [
            'alpha_1' => ['numerator' => 1, 'denominator' => 2],
            'alpha_2' => ['numerator' => 3, 'denominator' => 4],
            'beta_1' => ['numerator' => 1, 'denominator' => 4],
            'beta_2' => ['numerator' => 7, 'denominator' => 8],
            'rho' => ['numerator' => 1, 'denominator' => 2],
        ];

        return [
            // alpha_1.numerator のバリデーションケース
            'alpha_1.numeratorが空欄' => [
                'data' => array_merge($base_data, ['alpha_1' => ['numerator' => null, 'denominator' => 2]]),
            ],
            'alpha_1.numeratorが整数でない' => [
                'data' => array_merge($base_data, ['alpha_1' => ['numerator' => 'String', 'denominator' => 2]]),
            ],
            'alpha_1.numeratorが0以下' => [
                'data' => array_merge($base_data, ['alpha_1' => ['numerator' => 0, 'denominator' => 2]]),
            ],
            'alpha_1.numeratorが1001以上' => [
                'data' => array_merge($base_data, ['alpha_1' => ['numerator' => 1001, 'denominator' => 2]]),
            ],

            // alpha_1.denominator のバリデーションケース
            'alpha_1.denominatorが空欄' => [
                'data' => array_merge($base_data, ['alpha_1' => ['numerator' => 1, 'denominator' => null]]),
            ],
            'alpha_1.denominatorが整数でない' => [
                'data' => array_merge($base_data, ['alpha_1' => ['numerator' => 1, 'denominator' => 'String']]),
            ],
            'alpha_1.denominatorが0以下' => [
                'data' => array_merge($base_data, ['alpha_1' => ['numerator' => 1, 'denominator' => 0]]),
            ],
            'alpha_1.denominatorが1001以上' => [
                'data' => array_merge($base_data, ['alpha_1' => ['numerator' => 1, 'denominator' => 1001]]),
            ],

            // alpha_2.numerator のバリデーションケース
            'alpha_2.numeratorが空欄' => [
                'data' => array_merge($base_data, ['alpha_2' => ['numerator' => null, 'denominator' => 4]]),
            ],
            'alpha_2.numeratorが整数でない' => [
                'data' => array_merge($base_data, ['alpha_2' => ['numerator' => 'String', 'denominator' => 4]]),
            ],
            'alpha_2.numeratorが0以下' => [
                'data' => array_merge($base_data, ['alpha_2' => ['numerator' => 0, 'denominator' => 4]]),
            ],
            'alpha_2.numeratorが1001以上' => [
                'data' => array_merge($base_data, ['alpha_2' => ['numerator' => 1001, 'denominator' => 4]]),
            ],

            // alpha_2.denominator のバリデーションケース
            'alpha_2.denominatorが空欄' => [
                'data' => array_merge($base_data, ['alpha_2' => ['numerator' => 3, 'denominator' => null]]),
            ],
            'alpha_2.denominatorが整数でない' => [
                'data' => array_merge($base_data, ['alpha_2' => ['numerator' => 3, 'denominator' => 'String']]),
            ],
            'alpha_2.denominatorが0以下' => [
                'data' => array_merge($base_data, ['alpha_2' => ['numerator' => 3, 'denominator' => 0]]),
            ],
            'alpha_2.denominatorが1001以上' => [
                'data' => array_merge($base_data, ['alpha_2' => ['numerator' => 3, 'denominator' => 1001]]),
            ],

            // beta_1.numerator のバリデーションケース
            'beta_1.numeratorが空欄' => [
                'data' => array_merge($base_data, ['beta_1' => ['numerator' => null, 'denominator' => 4]]),
            ],
            'beta_1.numeratorが整数でない' => [
                'data' => array_merge($base_data, ['beta_1' => ['numerator' => 'String', 'denominator' => 4]]),
            ],
            'beta_1.numeratorが0以下' => [
                'data' => array_merge($base_data, ['beta_1' => ['numerator' => 0, 'denominator' => 4]]),
            ],
            'beta_1.numeratorが1001以上' => [
                'data' => array_merge($base_data, ['beta_1' => ['numerator' => 1001, 'denominator' => 4]]),
            ],

            // beta_1.denominator のバリデーションケース
            'beta_1.denominatorが空欄' => [
                'data' => array_merge($base_data, ['beta_1' => ['numerator' => 1, 'denominator' => null]]),
            ],
            'beta_1.denominatorが整数でない' => [
                'data' => array_merge($base_data, ['beta_1' => ['numerator' => 1, 'denominator' => 'String']]),
            ],
            'beta_1.denominatorが0以下' => [
                'data' => array_merge($base_data, ['beta_1' => ['numerator' => 1, 'denominator' => 0]]),
            ],
            'beta_1.denominatorが1001以上' => [
                'data' => array_merge($base_data, ['beta_1' => ['numerator' => 1, 'denominator' => 1001]]),
            ],

            // beta_2.numerator のバリデーションケース
            'beta_2.numeratorが空欄' => [
                'data' => array_merge($base_data, ['beta_2' => ['numerator' => null, 'denominator' => 8]]),
            ],
            'beta_2.numeratorが整数でない' => [
                'data' => array_merge($base_data, ['beta_2' => ['numerator' => 'String', 'denominator' => 8]]),
            ],
            'beta_2.numeratorが0以下' => [
                'data' => array_merge($base_data, ['beta_2' => ['numerator' => 0, 'denominator' => 8]]),
            ],
            'beta_2.numeratorが1001以上' => [
                'data' => array_merge($base_data, ['beta_2' => ['numerator' => 1001, 'denominator' => 8]]),
            ],

            // beta_2.denominator のバリデーションケース
            'beta_2.denominatorが空欄' => [
                'data' => array_merge($base_data, ['beta_2' => ['numerator' => 7, 'denominator' => null]]),
            ],
            'beta_2.denominatorが整数でない' => [
                'data' => array_merge($base_data, ['beta_2' => ['numerator' => 7, 'denominator' => 'String']]),
            ],
            'beta_2.denominatorが0以下' => [
                'data' => array_merge($base_data, ['beta_2' => ['numerator' => 7, 'denominator' => 0]]),
            ],
            'beta_2.denominatorが1001以上' => [
                'data' => array_merge($base_data, ['beta_2' => ['numerator' => 7, 'denominator' => 1001]]),
            ],

            // rho.numerator のバリデーションケース
            'rho.numeratorが空欄' => [
                'data' => array_merge($base_data, ['rho' => ['numerator' => null, 'denominator' => 2]]),
            ],
            'rho.numeratorが整数でない' => [
                'data' => array_merge($base_data, ['rho' => ['numerator' => 'String', 'denominator' => 2]]),
            ],
            'rho.numeratorが0以下' => [
                'data' => array_merge($base_data, ['rho' => ['numerator' => 0, 'denominator' => 2]]),
            ],
            'rho.numeratorが1001以上' => [
                'data' => array_merge($base_data, ['rho' => ['numerator' => 1001, 'denominator' => 2]]),
            ],

            // rho.denominator のバリデーションケース
            'rho.denominatorが空欄' => [
                'data' => array_merge($base_data, ['rho' => ['numerator' => 1, 'denominator' => null]]),
            ],
            'rho.denominatorが整数でない' => [
                'data' => array_merge($base_data, ['rho' => ['numerator' => 1, 'denominator' => 'String']]),
            ],
            'rho.denominatorが0以下' => [
                'data' => array_merge($base_data, ['rho' => ['numerator' => 1, 'denominator' => 0]]),
            ],
            'rho.denominatorが1001以上' => [
                'data' => array_merge($base_data, ['rho' => ['numerator' => 1, 'denominator' => 1001]]),
            ],
        ];
    }

    /**
     * 無効なデータで分数のバリデーションが失敗することをテストします。
     * @test
     * @dataProvider validationFailsWithInvalidFractionDataProvider
     * @return void
     */
    public function testValidationFailsWithInvalidFractionData(
        array $data,
        string $attribute
    ) {
        $request = new CalculateNashRequest();
        $request->merge($data);

        $rules = array_filter($request->rules(), function ($attributeKey) use ($attribute) {
            return $attributeKey === $attribute;
        }, ARRAY_FILTER_USE_KEY);

        // Coordinateルールはここでは除外する
        $rules[$attribute] = array_filter($rules[$attribute], function ($ruleValue) {
            return ! ($ruleValue instanceof Coordinate);
        });

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->passes());
    }

    public static function validationFailsWithInvalidFractionDataProvider(): array
    {
        $base_data = [
            'alpha_1' => ['numerator' => 1, 'denominator' => 2],
            'alpha_2' => ['numerator' => 3, 'denominator' => 4],
            'beta_1' => ['numerator' => 1, 'denominator' => 4],
            'beta_2' => ['numerator' => 7, 'denominator' => 8],
            'rho' => ['numerator' => 1, 'denominator' => 2],
        ];

        return [
            'alpha_1が1以上' => [
                'data' => array_merge($base_data, ['alpha_1' => ['numerator' => 15, 'denominator' => 10]]),
                'attribute' => 'alpha_1',
            ],
            'alpha_2が1以上' => [
                'data' => array_merge($base_data, ['alpha_2' => ['numerator' => 5, 'denominator' => 4]]),
                'attribute' => 'alpha_2',
            ],
            'beta_1が1以上' => [
                'data' => array_merge($base_data, ['beta_1' => ['numerator' => 5, 'denominator' => 4]]),
                'attribute' => 'beta_1',
            ],
            'beta_2が1以上' => [
                'data' => array_merge($base_data, ['beta_2' => ['numerator' => 9, 'denominator' => 8]]),
                'attribute' => 'beta_2',
            ],
            'rhoが1以上' => [
                'data' => array_merge($base_data, ['rho' => ['numerator' => 3, 'denominator' => 2]]),
                'attribute' => 'rho',
            ],
        ];
    }

    /**
     * 無効な座標データのデータプロバイダー
     * @return array
     */
    public static function validationFailsWithInvalidCoordinateDataProvider(): array
    {
        return [
            '無効な分数（最大値より大きい）' => [
                'data' => [
                    'alpha_1' => ['numerator' => 2, 'denominator' => 1], // 2 > 1
                    'alpha_2' => ['numerator' => 3, 'denominator' => 4],
                    'beta_1' => ['numerator' => 1, 'denominator' => 4],
                    'beta_2' => ['numerator' => 7, 'denominator' => 8],
                    'rho' => ['numerator' => 1, 'denominator' => 2],
                ],
            ],
            '無効な座標（beta_1 > alpha_1 かつ beta_2 > alpha_2）' => [
                'data' => [
                    'alpha_1' => ['numerator' => 1, 'denominator' => 4],
                    'alpha_2' => ['numerator' => 3, 'denominator' => 4],
                    'beta_1' => ['numerator' => 1, 'denominator' => 2],
                    'beta_2' => ['numerator' => 7, 'denominator' => 8],
                    'rho' => ['numerator' => 1, 'denominator' => 2],
                ],
            ],
        ];
    }

    /**
     * 無効なデータでCoordinateのバリデーションが失敗することをテストします。
     * @test
     * @dataProvider validationFailsWithInvalidCoordinateDataProvider
     * @return void
     */
    public function testValidationFailsWithInvalidCoordinateData(array $data)
    {
        $request = new CalculateNashRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());
        $this->assertFalse($validator->passes());
    }

    /**
     * attributesメソッドが期待される属性の翻訳を返すことをテストします。
     * @test
     * @return void
     */
    public function testAttributesReturnsExpectedTranslations()
    {
        $request = new CalculateNashRequest();
        $attributes = $request->attributes();

        // すべての期待される属性が存在することを確認
        $this->assertArrayHasKey('alpha_1.numerator', $attributes);
        $this->assertArrayHasKey('alpha_1.denominator', $attributes);
        $this->assertArrayHasKey('alpha_2.numerator', $attributes);
        $this->assertArrayHasKey('alpha_2.denominator', $attributes);
        $this->assertArrayHasKey('beta_1.numerator', $attributes);
        $this->assertArrayHasKey('beta_1.denominator', $attributes);
        $this->assertArrayHasKey('beta_2.numerator', $attributes);
        $this->assertArrayHasKey('beta_2.denominator', $attributes);
        $this->assertArrayHasKey('rho.numerator', $attributes);
        $this->assertArrayHasKey('rho.denominator', $attributes);

        // 翻訳が正しく定義されていることを確認
        $this->assertSame(trans('validation.attributes.alpha_1_numerator'), $attributes['alpha_1.numerator']);
        $this->assertSame(trans('validation.attributes.alpha_1_denominator'), $attributes['alpha_1.denominator']);
    }
}
