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
     * バリデーションをテストします。（単体のケース）
     * @test
     * @dataProvider singleFieldValidationDataProvider
     * @param array $data
     * @param string $field
     * @param bool $expected
     * @return void
     */
    public function testSingleFieldValidation(array $data, string $field, bool $expected): void
    {
        $request = new CalculateNashRequest();
        $request->merge($data);

        $rules = array_filter($request->rules(), function ($key) use ($field) {
            return $key === $field;
        }, ARRAY_FILTER_USE_KEY);

        $validator = Validator::make($data, $rules);
        $this->assertSame($expected, $validator->passes());
    }

    /**
     * 単一フィールドのバリデーションをテストするためのデータプロバイダです。
     * @return array
     */
    public static function singleFieldValidationDataProvider(): array
    {
        return [
            // alpha_1.numerator のバリデーションケース
            'alpha_1.numeratorが空欄' => [
                'data' => ['alpha_1' => ['numerator' => null, 'denominator' => 2]],
                'field' => 'alpha_1.numerator',
                'expected' => false,
            ],
            'alpha_1.numeratorが整数でない' => [
                'data' => ['alpha_1' => ['numerator' => 'String', 'denominator' => 2]],
                'field' => 'alpha_1.numerator',
                'expected' => false,
            ],
            'alpha_1.numeratorが0以下' => [
                'data' => ['alpha_1' => ['numerator' => 0, 'denominator' => 2]],
                'field' => 'alpha_1.numerator',
                'expected' => false,
            ],
            'alpha_1.numeratorが1001以上' => [
                'data' => ['alpha_1' => ['numerator' => 1001, 'denominator' => 2]],
                'field' => 'alpha_1.numerator',
                'expected' => false,
            ],
            // alpha_1.numerator の有効なケース
            'alpha_1.numeratorが最小値(1)' => [
                'data' => ['alpha_1' => ['numerator' => 1, 'denominator' => 2]],
                'field' => 'alpha_1.numerator',
                'expected' => true,
            ],
            'alpha_1.numeratorが最大値(1000)' => [
                'data' => ['alpha_1' => ['numerator' => 1000, 'denominator' => 2]],
                'field' => 'alpha_1.numerator',
                'expected' => true,
            ],

            // alpha_1.denominator のバリデーションケース
            'alpha_1.denominatorが空欄' => [
                'data' => ['alpha_1' => ['numerator' => 1, 'denominator' => null]],
                'field' => 'alpha_1.denominator',
                'expected' => false,
            ],
            'alpha_1.denominatorが整数でない' => [
                'data' => ['alpha_1' => ['numerator' => 1, 'denominator' => 'String']],
                'field' => 'alpha_1.denominator',
                'expected' => false,
            ],
            'alpha_1.denominatorが0以下' => [
                'data' => ['alpha_1' => ['numerator' => 1, 'denominator' => 0]],
                'field' => 'alpha_1.denominator',
                'expected' => false,
            ],
            'alpha_1.denominatorが1001以上' => [
                'data' => ['alpha_1' => ['numerator' => 1, 'denominator' => 1001]],
                'field' => 'alpha_1.denominator',
                'expected' => false,
            ],
            // alpha_1.denominator の有効なケース
            'alpha_1.denominatorが最小値(1)' => [
                'data' => ['alpha_1' => ['numerator' => 1, 'denominator' => 1]],
                'field' => 'alpha_1.denominator',
                'expected' => true,
            ],
            'alpha_1.denominatorが最大値(1000)' => [
                'data' => ['alpha_1' => ['numerator' => 1, 'denominator' => 1000]],
                'field' => 'alpha_1.denominator',
                'expected' => true,
            ],

            // alpha_2.numerator のバリデーションケース
            'alpha_2.numeratorが空欄' => [
                'data' => ['alpha_2' => ['numerator' => null, 'denominator' => 4]],
                'field' => 'alpha_2.numerator',
                'expected' => false,
            ],
            'alpha_2.numeratorが整数でない' => [
                'data' => ['alpha_2' => ['numerator' => 'String', 'denominator' => 4]],
                'field' => 'alpha_2.numerator',
                'expected' => false,
            ],
            'alpha_2.numeratorが0以下' => [
                'data' => ['alpha_2' => ['numerator' => 0, 'denominator' => 4]],
                'field' => 'alpha_2.numerator',
                'expected' => false,
            ],
            'alpha_2.numeratorが1001以上' => [
                'data' => ['alpha_2' => ['numerator' => 1001, 'denominator' => 4]],
                'field' => 'alpha_2.numerator',
                'expected' => false,
            ],
            // alpha_2.numerator の有効なケース
            'alpha_2.numeratorが最小値(1)' => [
                'data' => ['alpha_2' => ['numerator' => 1, 'denominator' => 4]],
                'field' => 'alpha_2.numerator',
                'expected' => true,
            ],
            'alpha_2.numeratorが最大値(1000)' => [
                'data' => ['alpha_2' => ['numerator' => 1000, 'denominator' => 4]],
                'field' => 'alpha_2.numerator',
                'expected' => true,
            ],

            // alpha_2.denominator のバリデーションケース
            'alpha_2.denominatorが空欄' => [
                'data' => ['alpha_2' => ['numerator' => 3, 'denominator' => null]],
                'field' => 'alpha_2.denominator',
                'expected' => false,
            ],
            'alpha_2.denominatorが整数でない' => [
                'data' => ['alpha_2' => ['numerator' => 3, 'denominator' => 'String']],
                'field' => 'alpha_2.denominator',
                'expected' => false,
            ],
            'alpha_2.denominatorが0以下' => [
                'data' => ['alpha_2' => ['numerator' => 3, 'denominator' => 0]],
                'field' => 'alpha_2.denominator',
                'expected' => false,
            ],
            'alpha_2.denominatorが1001以上' => [
                'data' => ['alpha_2' => ['numerator' => 3, 'denominator' => 1001]],
                'field' => 'alpha_2.denominator',
                'expected' => false,
            ],
            // alpha_2.denominator の有効なケース
            'alpha_2.denominatorが最小値(1)' => [
                'data' => ['alpha_2' => ['numerator' => 3, 'denominator' => 1]],
                'field' => 'alpha_2.denominator',
                'expected' => true,
            ],
            'alpha_2.denominatorが最大値(1000)' => [
                'data' => ['alpha_2' => ['numerator' => 3, 'denominator' => 1000]],
                'field' => 'alpha_2.denominator',
                'expected' => true,
            ],

            // beta_1.numerator のバリデーションケース
            'beta_1.numeratorが空欄' => [
                'data' => ['beta_1' => ['numerator' => null, 'denominator' => 4]],
                'field' => 'beta_1.numerator',
                'expected' => false,
            ],
            'beta_1.numeratorが整数でない' => [
                'data' => ['beta_1' => ['numerator' => 'String', 'denominator' => 4]],
                'field' => 'beta_1.numerator',
                'expected' => false,
            ],
            'beta_1.numeratorが0以下' => [
                'data' => ['beta_1' => ['numerator' => 0, 'denominator' => 4]],
                'field' => 'beta_1.numerator',
                'expected' => false,
            ],
            'beta_1.numeratorが1001以上' => [
                'data' => ['beta_1' => ['numerator' => 1001, 'denominator' => 4]],
                'field' => 'beta_1.numerator',
                'expected' => false,
            ],
            // beta_1.numerator の有効なケース
            'beta_1.numeratorが最小値(1)' => [
                'data' => ['beta_1' => ['numerator' => 1, 'denominator' => 4]],
                'field' => 'beta_1.numerator',
                'expected' => true,
            ],
            'beta_1.numeratorが最大値(1000)' => [
                'data' => ['beta_1' => ['numerator' => 1000, 'denominator' => 4]],
                'field' => 'beta_1.numerator',
                'expected' => true,
            ],

            // beta_1.denominator のバリデーションケース
            'beta_1.denominatorが空欄' => [
                'data' => ['beta_1' => ['numerator' => 1, 'denominator' => null]],
                'field' => 'beta_1.denominator',
                'expected' => false,
            ],
            'beta_1.denominatorが整数でない' => [
                'data' => ['beta_1' => ['numerator' => 1, 'denominator' => 'String']],
                'field' => 'beta_1.denominator',
                'expected' => false,
            ],
            'beta_1.denominatorが0以下' => [
                'data' => ['beta_1' => ['numerator' => 1, 'denominator' => 0]],
                'field' => 'beta_1.denominator',
                'expected' => false,
            ],
            'beta_1.denominatorが1001以上' => [
                'data' => ['beta_1' => ['numerator' => 1, 'denominator' => 1001]],
                'field' => 'beta_1.denominator',
                'expected' => false,
            ],
            // beta_1.denominator の有効なケース
            'beta_1.denominatorが最小値(1)' => [
                'data' => ['beta_1' => ['numerator' => 1, 'denominator' => 1]],
                'field' => 'beta_1.denominator',
                'expected' => true,
            ],
            'beta_1.denominatorが最大値(1000)' => [
                'data' => ['beta_1' => ['numerator' => 1, 'denominator' => 1000]],
                'field' => 'beta_1.denominator',
                'expected' => true,
            ],

            // beta_2.numerator のバリデーションケース
            'beta_2.numeratorが空欄' => [
                'data' => ['beta_2' => ['numerator' => null, 'denominator' => 8]],
                'field' => 'beta_2.numerator',
                'expected' => false,
            ],
            'beta_2.numeratorが整数でない' => [
                'data' => ['beta_2' => ['numerator' => 'String', 'denominator' => 8]],
                'field' => 'beta_2.numerator',
                'expected' => false,
            ],
            'beta_2.numeratorが0以下' => [
                'data' => ['beta_2' => ['numerator' => 0, 'denominator' => 8]],
                'field' => 'beta_2.numerator',
                'expected' => false,
            ],
            'beta_2.numeratorが1001以上' => [
                'data' => ['beta_2' => ['numerator' => 1001, 'denominator' => 8]],
                'field' => 'beta_2.numerator',
                'expected' => false,
            ],
            // beta_2.numerator の有効なケース
            'beta_2.numeratorが最小値(1)' => [
                'data' => ['beta_2' => ['numerator' => 1, 'denominator' => 8]],
                'field' => 'beta_2.numerator',
                'expected' => true,
            ],
            'beta_2.numeratorが最大値(1000)' => [
                'data' => ['beta_2' => ['numerator' => 1000, 'denominator' => 8]],
                'field' => 'beta_2.numerator',
                'expected' => true,
            ],

            // beta_2.denominator のバリデーションケース
            'beta_2.denominatorが空欄' => [
                'data' => ['beta_2' => ['numerator' => 7, 'denominator' => null]],
                'field' => 'beta_2.denominator',
                'expected' => false,
            ],
            'beta_2.denominatorが整数でない' => [
                'data' => ['beta_2' => ['numerator' => 7, 'denominator' => 'String']],
                'field' => 'beta_2.denominator',
                'expected' => false,
            ],
            'beta_2.denominatorが0以下' => [
                'data' => ['beta_2' => ['numerator' => 7, 'denominator' => 0]],
                'field' => 'beta_2.denominator',
                'expected' => false,
            ],
            'beta_2.denominatorが1001以上' => [
                'data' => ['beta_2' => ['numerator' => 7, 'denominator' => 1001]],
                'field' => 'beta_2.denominator',
                'expected' => false,
            ],
            // beta_2.denominator の有効なケース
            'beta_2.denominatorが最小値(1)' => [
                'data' => ['beta_2' => ['numerator' => 7, 'denominator' => 1]],
                'field' => 'beta_2.denominator',
                'expected' => true,
            ],
            'beta_2.denominatorが最大値(1000)' => [
                'data' => ['beta_2' => ['numerator' => 7, 'denominator' => 1000]],
                'field' => 'beta_2.denominator',
                'expected' => true,
            ],

            // rho.numerator のバリデーションケース
            'rho.numeratorが空欄' => [
                'data' => ['rho' => ['numerator' => null, 'denominator' => 2]],
                'field' => 'rho.numerator',
                'expected' => false,
            ],
            'rho.numeratorが整数でない' => [
                'data' => ['rho' => ['numerator' => 'String', 'denominator' => 2]],
                'field' => 'rho.numerator',
                'expected' => false,
            ],
            'rho.numeratorが0以下' => [
                'data' => ['rho' => ['numerator' => 0, 'denominator' => 2]],
                'field' => 'rho.numerator',
                'expected' => false,
            ],
            'rho.numeratorが1001以上' => [
                'data' => ['rho' => ['numerator' => 1001, 'denominator' => 2]],
                'field' => 'rho.numerator',
                'expected' => false,
            ],
            // rho.numerator の有効なケース
            'rho.numeratorが最小値(1)' => [
                'data' => ['rho' => ['numerator' => 1, 'denominator' => 2]],
                'field' => 'rho.numerator',
                'expected' => true,
            ],
            'rho.numeratorが最大値(1000)' => [
                'data' => ['rho' => ['numerator' => 1000, 'denominator' => 2]],
                'field' => 'rho.numerator',
                'expected' => true,
            ],

            // rho.denominator のバリデーションケース
            'rho.denominatorが空欄' => [
                'data' => ['rho' => ['numerator' => 1, 'denominator' => null]],
                'field' => 'rho.denominator',
                'expected' => false,
            ],
            'rho.denominatorが整数でない' => [
                'data' => ['rho' => ['numerator' => 1, 'denominator' => 'String']],
                'field' => 'rho.denominator',
                'expected' => false,
            ],
            'rho.denominatorが0以下' => [
                'data' => ['rho' => ['numerator' => 1, 'denominator' => 0]],
                'field' => 'rho.denominator',
                'expected' => false,
            ],
            'rho.denominatorが1001以上' => [
                'data' => ['rho' => ['numerator' => 1, 'denominator' => 1001]],
                'field' => 'rho.denominator',
                'expected' => false,
            ],
            // rho.denominator の有効なケース
            'rho.denominatorが最小値(1)' => [
                'data' => ['rho' => ['numerator' => 1, 'denominator' => 1]],
                'field' => 'rho.denominator',
                'expected' => true,
            ],
            'rho.denominatorが最大値(1000)' => [
                'data' => ['rho' => ['numerator' => 1, 'denominator' => 1000]],
                'field' => 'rho.denominator',
                'expected' => true,
            ],

            // alpha_1 のバリデーションケース
            'alpha_1が空欄' => [
                'data' => ['alpha_1' => null],
                'field' => 'alpha_1',
                'expected' => false,
            ],
            'alpha_1が配列でない' => [
                'data' => ['alpha_1' => 'String'],
                'field' => 'alpha_1',
                'expected' => false,
            ],
            'alpha_1が1より大きい' => [
                'data' => ['alpha_1' => ['numerator' => 15, 'denominator' => 10]],
                'field' => 'alpha_1',
                'expected' => false,
            ],
            // alpha_1 の有効なケース
            'alpha_1が有効な値' => [
                'data' => ['alpha_1' => ['numerator' => 1, 'denominator' => 2]],
                'field' => 'alpha_1',
                'expected' => true,
            ],

            // alpha_2 のバリデーションケース
            'alpha_2が空欄' => [
                'data' => ['alpha_2' => null],
                'field' => 'alpha_2',
                'expected' => false,
            ],
            'alpha_2が配列でない' => [
                'data' => ['alpha_2' => 'String'],
                'field' => 'alpha_2',
                'expected' => false,
            ],
            'alpha_2が1より大きい' => [
                'data' => ['alpha_2' => ['numerator' => 5, 'denominator' => 4]],
                'field' => 'alpha_2',
                'expected' => false,
            ],
            // alpha_2 の有効なケース
            'alpha_2が有効な値' => [
                'data' => ['alpha_2' => ['numerator' => 3, 'denominator' => 4]],
                'field' => 'alpha_2',
                'expected' => true,
            ],

            // beta_1 のバリデーションケース
            'beta_1が空欄' => [
                'data' => ['beta_1' => null],
                'field' => 'beta_1',
                'expected' => false,
            ],
            'beta_1が配列でない' => [
                'data' => ['beta_1' => 'String'],
                'field' => 'beta_1',
                'expected' => false,
            ],
            'beta_1が1より大きい' => [
                'data' => ['beta_1' => ['numerator' => 5, 'denominator' => 4]],
                'field' => 'beta_1',
                'expected' => false,
            ],
            // beta_1 の有効なケース
            'beta_1が有効な値' => [
                'data' => ['beta_1' => ['numerator' => 1, 'denominator' => 4]],
                'field' => 'beta_1',
                'expected' => true,
            ],

            // beta_2 のバリデーションケース
            'beta_2が空欄' => [
                'data' => ['beta_2' => null],
                'field' => 'beta_2',
                'expected' => false,
            ],
            'beta_2が配列でない' => [
                'data' => ['beta_2' => 'String'],
                'field' => 'beta_2',
                'expected' => false,
            ],
            'beta_2が1より大きい' => [
                'data' => ['beta_2' => ['numerator' => 9, 'denominator' => 8]],
                'field' => 'beta_2',
                'expected' => false,
            ],
            // beta_2 の有効なケース
            'beta_2が有効な値' => [
                'data' => ['beta_2' => ['numerator' => 7, 'denominator' => 8]],
                'field' => 'beta_2',
                'expected' => true,
            ],

            // rho のバリデーションケース
            'rhoが空欄' => [
                'data' => ['rho' => null],
                'field' => 'rho',
                'expected' => false,
            ],
            'rhoが配列でない' => [
                'data' => ['rho' => 'String'],
                'field' => 'rho',
                'expected' => false,
            ],
            'rhoが1より大きい' => [
                'data' => ['rho' => ['numerator' => 3, 'denominator' => 2]],
                'field' => 'rho',
                'expected' => false,
            ],
            // rho の有効なケース
            'rhoが有効な値' => [
                'data' => ['rho' => ['numerator' => 1, 'denominator' => 2]],
                'field' => 'rho',
                'expected' => true,
            ],
        ];
    }

    /**
     * 座標バリデーションのテストデータプロバイダー
     * @return array
     */
    public static function coordinateValidationDataProvider(): array
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
     * 座標データのバリデーションをテストします。
     * @test
     * @dataProvider coordinateValidationDataProvider
     * @return void
     */
    public function testCoordinateValidation(array $data)
    {
        $request = new CalculateNashRequest();
        $request->merge($data);

        $validator = Validator::make($data, $request->rules());

        // withValidatorメソッドを手動で呼び出す
        $request->withValidator($validator);

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
