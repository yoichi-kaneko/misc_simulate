<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api;

use Tests\TestCase;

class CentipedeControllerTest extends TestCase
{
    /**
     * calculateで正常に処理が完了するケースのテスト
     * @test
     * @return void
     */
    public function calculate_success()
    {
        $data = [
            'patterns' => [
                'a_1' => [
                    'base_numerator' => 3,
                    'numerator_exp_1' => 1,
                    'numerator_exp_2' => 2,
                    'denominator_exp' => 1,
                ],
            ],
            'max_step' => 10,
            'max_rc' => 100,
            'combination_player_1' => null,
        ];

        $response = $this->postJson('/api/centipede/calculate', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'result',
                'render_params' => [
                    'max_step',
                    'max_rc',
                ],
                'pattern_data' => [
                    'a_1' => [
                        'cognitive_unit_latex_text',
                        'cognitive_unit_value',
                        'average_of_reversed_causality',
                        'data',
                        'chart_data',
                    ],
                ],
                'combination_data',
            ])
            ->assertJson([
                'result' => 'ok',
                'render_params' => [
                    'max_step' => 10,
                    'max_rc' => 100,
                ],
            ]);

        // Check the structure of the first element in pattern_data.a_1.data
        $this->assertArrayHasKey('t', $response->json('pattern_data.a_1.data.0'));
        $this->assertArrayHasKey('max_nu_value', $response->json('pattern_data.a_1.data.0'));
        $this->assertArrayHasKey('left_side_value', $response->json('pattern_data.a_1.data.0'));
        $this->assertArrayHasKey('right_side_value', $response->json('pattern_data.a_1.data.0'));
        $this->assertArrayHasKey('result', $response->json('pattern_data.a_1.data.0'));

        // Check the number of elements in pattern_data.a_1.data
        $this->assertCount(10, $response->json('pattern_data.a_1.data')); // max_step

        // Check the structure of the first element in pattern_data.a_1.chart_data
        $this->assertArrayHasKey('x', $response->json('pattern_data.a_1.chart_data.0'));
        $this->assertArrayHasKey('y', $response->json('pattern_data.a_1.chart_data.0'));

        // Check the number of elements in pattern_data.a_1.chart_data
        $this->assertCount(10, $response->json('pattern_data.a_1.chart_data')); // max_step
    }

    /**
     * combination_player_1がnullでないケースのテスト
     * CentipedeDataCombinerのcombineメソッドが正常に動作することを確認する
     * @test
     * @return void
     */
    public function calculate_success_with_combination_player_1()
    {
        $data = [
            'patterns' => [
                // a_1のパターン
                'a_1' => [
                    'base_numerator' => 3,
                    'numerator_exp_1' => 1,
                    'numerator_exp_2' => 2,
                    'denominator_exp' => 1,
                ],
                // a_2のパターン
                'a_2' => [
                    'base_numerator' => 5,
                    'numerator_exp_1' => 2,
                    'numerator_exp_2' => 3,
                    'denominator_exp' => 4,
                ],
            ],
            'max_step' => 10,
            'max_rc' => 100,
            'combination_player_1' => [
                'a' => '1',  // Player1が1を選択
            ],
        ];

        $response = $this->postJson('/api/centipede/calculate', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'result',
                'render_params' => [
                    'max_step',
                    'max_rc',
                ],
                'pattern_data' => [
                    'a_1' => [
                        'cognitive_unit_latex_text',
                        'cognitive_unit_value',
                        'average_of_reversed_causality',
                        'data',
                        'chart_data',
                    ],
                    'a_2' => [
                        'cognitive_unit_latex_text',
                        'cognitive_unit_value',
                        'average_of_reversed_causality',
                        'data',
                        'chart_data',
                    ],
                ],
                'combination_data' => [
                    'a' => [
                        'data',
                        'chart_data',
                        'cognitive_unit_latex_text_1',
                        'cognitive_unit_latex_text_2',
                        'cognitive_unit_value_1',
                        'cognitive_unit_value_2',
                        'average_of_reversed_causality',
                    ],
                ],
            ])
            ->assertJson([
                'result' => 'ok',
                'render_params' => [
                    'max_step' => 10,
                    'max_rc' => 100,
                ],
            ]);

        // combination_dataがnullでないことを確認
        $this->assertNotNull($response->json('combination_data'));

        // aキーが存在することを確認
        $this->assertArrayHasKey('a', $response->json('combination_data'));

        // Check pattern_data.a_1.data structure and count
        $this->assertArrayHasKey('t', $response->json('pattern_data.a_1.data.0'));
        $this->assertArrayHasKey('max_nu_value', $response->json('pattern_data.a_1.data.0'));
        $this->assertArrayHasKey('left_side_value', $response->json('pattern_data.a_1.data.0'));
        $this->assertArrayHasKey('right_side_value', $response->json('pattern_data.a_1.data.0'));
        $this->assertArrayHasKey('result', $response->json('pattern_data.a_1.data.0'));
        $this->assertCount(10, $response->json('pattern_data.a_1.data'));

        // Check pattern_data.a_1.chart_data structure and count
        $this->assertArrayHasKey('x', $response->json('pattern_data.a_1.chart_data.0'));
        $this->assertArrayHasKey('y', $response->json('pattern_data.a_1.chart_data.0'));
        $this->assertCount(10, $response->json('pattern_data.a_1.chart_data'));

        // Check pattern_data.a_2.data structure and count
        $this->assertArrayHasKey('t', $response->json('pattern_data.a_2.data.0'));
        $this->assertArrayHasKey('max_nu_value', $response->json('pattern_data.a_2.data.0'));
        $this->assertArrayHasKey('left_side_value', $response->json('pattern_data.a_2.data.0'));
        $this->assertArrayHasKey('right_side_value', $response->json('pattern_data.a_2.data.0'));
        $this->assertArrayHasKey('result', $response->json('pattern_data.a_2.data.0'));
        $this->assertCount(10, $response->json('pattern_data.a_2.data'));

        // Check pattern_data.a_2.chart_data structure and count
        $this->assertArrayHasKey('x', $response->json('pattern_data.a_2.chart_data.0'));
        $this->assertArrayHasKey('y', $response->json('pattern_data.a_2.chart_data.0'));
        $this->assertCount(10, $response->json('pattern_data.a_2.chart_data'));

        // Check combination_data.a.data structure and count
        $this->assertArrayHasKey('t', $response->json('combination_data.a.data.0'));
        $this->assertArrayHasKey('max_nu_value', $response->json('combination_data.a.data.0'));
        $this->assertArrayHasKey('left_side_value', $response->json('combination_data.a.data.0'));
        $this->assertArrayHasKey('right_side_value', $response->json('combination_data.a.data.0'));
        $this->assertArrayHasKey('result', $response->json('combination_data.a.data.0'));
        $this->assertCount(10, $response->json('combination_data.a.data'));

        // Check combination_data.a.chart_data structure and count
        $this->assertArrayHasKey('x', $response->json('combination_data.a.chart_data.0'));
        $this->assertArrayHasKey('y', $response->json('combination_data.a.chart_data.0'));
        $this->assertCount(10, $response->json('combination_data.a.chart_data'));
    }
}
