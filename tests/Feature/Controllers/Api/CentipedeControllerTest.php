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
                [
                    'base_numerator' => 3,
                    'numerator_exp_1' => 1,
                    'numerator_exp_2' => 2,
                    'denominator_exp' => 1,
                ]
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
}
