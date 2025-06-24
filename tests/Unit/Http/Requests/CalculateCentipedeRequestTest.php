<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\CalculateCentipedeRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CalculateCentipedeRequestTest extends TestCase
{
    /**
     * authorizeメソッドがtrueを返すことをテストします。
     * @test
     * @return void
     */
    public function testAuthorizeReturnsTrue()
    {
        $request = new CalculateCentipedeRequest();
        $this->assertTrue($request->authorize());
    }

    /**
     * rulesメソッドが期待される検証ルールを返すことをテストします。
     * @test
     * @return void
     */
    public function testRulesReturnsExpectedRules()
    {
        // モック入力データでリクエストを作成
        $request = new CalculateCentipedeRequest();
        $request->merge([
            'patterns' => [
                [
                    'base_numerator' => 1,
                    'numerator_exp_1' => 2,
                    'numerator_exp_2' => 3,
                    'denominator_exp' => 4,
                ],
            ],
            'max_step' => 10,
            'max_rc' => 20,
            'combination_player_1' => [
                'a' => '1',
                'b' => '2',
            ],
        ]);

        $rules = $request->rules();

        // すべての期待されるルールが存在することを確認
        $this->assertArrayHasKey('patterns.*.base_numerator', $rules);
        $this->assertArrayHasKey('patterns.*.numerator_exp_1', $rules);
        $this->assertArrayHasKey('patterns.*.numerator_exp_2', $rules);
        $this->assertArrayHasKey('patterns.*.denominator_exp', $rules);
        $this->assertArrayHasKey('max_step', $rules);
        $this->assertArrayHasKey('max_rc', $rules);
        $this->assertArrayHasKey('combination_player_1', $rules);
        $this->assertArrayHasKey('combination_player_1.a', $rules);
        $this->assertArrayHasKey('combination_player_1.b', $rules);

        // ルールが正しいことを確認
        $this->assertSame('required|integer|min:1|max:2000', $rules['patterns.*.base_numerator']);
        $this->assertSame('required|integer|min:1|max:5', $rules['patterns.*.numerator_exp_1']);
        $this->assertSame('required|integer|min:1|max:5', $rules['patterns.*.numerator_exp_2']);
        $this->assertSame('required|integer|min:0|max:12', $rules['patterns.*.denominator_exp']);
        $this->assertSame('required|integer|min:1|max:200', $rules['max_step']);
        $this->assertSame('nullable|integer|min:1|max:200', $rules['max_rc']);
        $this->assertSame('nullable|array', $rules['combination_player_1']);
        $this->assertSame('nullable|regex:/^[12]$/', $rules['combination_player_1.a']);
        $this->assertSame('nullable|regex:/^[12]$/', $rules['combination_player_1.b']);
    }

    /**
     * バリデーションをテストします。
     * @test
     * @dataProvider validationDataProvider
     * @param array $data
     * @param string $field
     * @param bool $expected
     * @return void
     */
    public function testValidation(array $data, string $field, bool $expected)
    {
        $request = new CalculateCentipedeRequest();
        $request->merge($data);

        $rules = array_filter($request->rules(), function ($key) use ($field) {
            return $key === $field;
        }, ARRAY_FILTER_USE_KEY);

        $validator = Validator::make($data, $rules);
        $this->assertSame($expected, $validator->passes());
    }

    /**
     * バリデーションのテストデータを提供するデータプロバイダです。
     * @return array
     */
    public static function validationDataProvider(): array
    {
        return [
            // 有効なデータのバリデーションケース
            '有効なデータ' => [
                'data' => [
                    'patterns' => [
                        [
                            'base_numerator' => 1,
                        ],
                    ],
                ],
                'field' => 'patterns.*.base_numerator',
                'expected' => true,
            ],

            // patterns.*.base_numerator のバリデーションケース
            'patterns.*.base_numeratorが空欄' => [
                'data' => [
                    'patterns' => [
                        [
                            'base_numerator' => null,
                        ],
                    ],
                ],
                'field' => 'patterns.*.base_numerator',
                'expected' => false,
            ],
            'patterns.*.base_numeratorが整数でない' => [
                'data' => [
                    'patterns' => [
                        [
                            'base_numerator' => 'String',
                        ],
                    ],
                ],
                'field' => 'patterns.*.base_numerator',
                'expected' => false,
            ],
            'patterns.*.base_numeratorが0以下' => [
                'data' => [
                    'patterns' => [
                        [
                            'base_numerator' => 0,
                        ],
                    ],
                ],
                'field' => 'patterns.*.base_numerator',
                'expected' => false,
            ],
            'patterns.*.base_numeratorが2001以上' => [
                'data' => [
                    'patterns' => [
                        [
                            'base_numerator' => 2001,
                        ],
                    ],
                ],
                'field' => 'patterns.*.base_numerator',
                'expected' => false,
            ],

            // patterns.*.numerator_exp_1 のバリデーションケース
            'patterns.*.numerator_exp_1が空欄' => [
                'data' => [
                    'patterns' => [
                        [
                            'numerator_exp_1' => null,
                        ],
                    ],
                ],
                'field' => 'patterns.*.numerator_exp_1',
                'expected' => false,
            ],
            'patterns.*.numerator_exp_1が整数でない' => [
                'data' => [
                    'patterns' => [
                        [
                            'numerator_exp_1' => 'String',
                        ],
                    ],
                ],
                'field' => 'patterns.*.numerator_exp_1',
                'expected' => false,
            ],
            'patterns.*.numerator_exp_1が0以下' => [
                'data' => [
                    'patterns' => [
                        [
                            'numerator_exp_1' => 0,
                        ],
                    ],
                ],
                'field' => 'patterns.*.numerator_exp_1',
                'expected' => false,
            ],
            'patterns.*.numerator_exp_1が6以上' => [
                'data' => [
                    'patterns' => [
                        [
                            'numerator_exp_1' => 6,
                        ],
                    ],
                ],
                'field' => 'patterns.*.numerator_exp_1',
                'expected' => false,
            ],

            // patterns.*.numerator_exp_2 のバリデーションケース
            'patterns.*.numerator_exp_2が空欄' => [
                'data' => [
                    'patterns' => [
                        [
                            'numerator_exp_2' => null,
                        ],
                    ],
                ],
                'field' => 'patterns.*.numerator_exp_2',
                'expected' => false,
            ],
            'patterns.*.numerator_exp_2が整数でない' => [
                'data' => [
                    'patterns' => [
                        [
                            'numerator_exp_2' => 'String',
                        ],
                    ],
                ],
                'field' => 'patterns.*.numerator_exp_2',
                'expected' => false,
            ],
            'patterns.*.numerator_exp_2が0以下' => [
                'data' => [
                    'patterns' => [
                        [
                            'numerator_exp_2' => 0,
                        ],
                    ],
                ],
                'field' => 'patterns.*.numerator_exp_2',
                'expected' => false,
            ],
            'patterns.*.numerator_exp_2が6以上' => [
                'data' => [
                    'patterns' => [
                        [
                            'numerator_exp_2' => 6,
                        ],
                    ],
                ],
                'field' => 'patterns.*.numerator_exp_2',
                'expected' => false,
            ],

            // patterns.*.denominator_exp のバリデーションケース
            'patterns.*.denominator_expが空欄' => [
                'data' => [
                    'patterns' => [
                        [
                            'denominator_exp' => null,
                        ],
                    ],
                ],
                'field' => 'patterns.*.denominator_exp',
                'expected' => false,
            ],
            'patterns.*.denominator_expが整数でない' => [
                'data' => [
                    'patterns' => [
                        [
                            'denominator_exp' => 'String',
                        ],
                    ],
                ],
                'field' => 'patterns.*.denominator_exp',
                'expected' => false,
            ],
            'patterns.*.denominator_expが0未満' => [
                'data' => [
                    'patterns' => [
                        [
                            'denominator_exp' => -1,
                        ],
                    ],
                ],
                'field' => 'patterns.*.denominator_exp',
                'expected' => false,
            ],
            'patterns.*.denominator_expが13以上' => [
                'data' => [
                    'patterns' => [
                        [
                            'denominator_exp' => 13,
                        ],
                    ],
                ],
                'field' => 'patterns.*.denominator_exp',
                'expected' => false,
            ],

            // max_step のバリデーションケース
            'max_stepが空欄' => [
                'data' => [
                    'max_step' => null,
                ],
                'field' => 'max_step',
                'expected' => false,
            ],
            'max_stepが整数でない' => [
                'data' => [
                    'max_step' => 'String',
                ],
                'field' => 'max_step',
                'expected' => false,
            ],
            'max_stepが0以下' => [
                'data' => [
                    'max_step' => 0,
                ],
                'field' => 'max_step',
                'expected' => false,
            ],
            'max_stepが201以上' => [
                'data' => [
                    'max_step' => 201,
                ],
                'field' => 'max_step',
                'expected' => false,
            ],

            // max_rc のバリデーションケース
            'max_rcが整数でない' => [
                'data' => [
                    'max_rc' => 'String',
                ],
                'field' => 'max_rc',
                'expected' => false,
            ],
            'max_rcが0以下' => [
                'data' => [
                    'max_rc' => 0,
                ],
                'field' => 'max_rc',
                'expected' => false,
            ],
            'max_rcが201以上' => [
                'data' => [
                    'max_rc' => 201,
                ],
                'field' => 'max_rc',
                'expected' => false,
            ],

            // combination_player_1.a のバリデーションケース
            'combination_player_1.aが不正な値' => [
                'data' => [
                    'combination_player_1' => [
                        'a' => '3',
                    ],
                ],
                'field' => 'combination_player_1.a',
                'expected' => false,
            ],

            // combination_player_1.b のバリデーションケース
            'combination_player_1.bが不正な値' => [
                'data' => [
                    'combination_player_1' => [
                        'b' => '3',
                    ],
                ],
                'field' => 'combination_player_1.b',
                'expected' => false,
            ],
        ];
    }

    /**
     * attributesメソッドが期待される属性の翻訳を返すことをテストします。
     * @test
     * @return void
     */
    public function testAttributesReturnsExpectedTranslations()
    {
        $request = new CalculateCentipedeRequest();
        $attributes = $request->attributes();

        // すべての期待される属性が存在することを確認
        $this->assertArrayHasKey('patterns.*.base_numerator', $attributes);
        $this->assertArrayHasKey('patterns.*.numerator_exp_1', $attributes);
        $this->assertArrayHasKey('patterns.*.numerator_exp_2', $attributes);
        $this->assertArrayHasKey('patterns.*.denominator_exp', $attributes);

        // 翻訳が正しく定義されていることを確認
        $this->assertSame(trans('validation.attributes.base_numerator'), $attributes['patterns.*.base_numerator']);
        $this->assertSame(trans('validation.attributes.numerator_exp_1'), $attributes['patterns.*.numerator_exp_1']);
        $this->assertSame(trans('validation.attributes.numerator_exp_2'), $attributes['patterns.*.numerator_exp_2']);
        $this->assertSame(trans('validation.attributes.denominator_exp'), $attributes['patterns.*.denominator_exp']);
    }
}
