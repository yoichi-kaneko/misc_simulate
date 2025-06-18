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
                'pattern_data',
                'combination_data',
            ])
            ->assertJson([
                'result' => 'ok',
                'render_params' => [
                    'max_step' => 10,
                    'max_rc' => 100,
                ],
            ]);
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
                'pattern_data',
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
    }
}
