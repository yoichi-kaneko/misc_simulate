<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api;

use Tests\TestCase;

class NashControllerTest extends TestCase
{
    /**
     * calculateで正常に処理が完了するケースのテスト
     * @test
     * @return void
     */
    public function calculate_success()
    {
        $data = [
            'alpha_1' => [
                'numerator' => 3,
                'denominator' => 4,
            ],
            'alpha_2' => [
                'numerator' => 1,
                'denominator' => 4,
            ],
            'beta_1' => [
                'numerator' => 1,
                'denominator' => 4,
            ],
            'beta_2' => [
                'numerator' => 3,
                'denominator' => 4,
            ],
            'rho' => [
                'numerator' => 1,
                'denominator' => 2,
            ],
        ];

        $response = $this->postJson('/api/nash/calculate', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'report_params' => [
                    'a_rho',
                ],
                'render_params' => [
                    'line',
                    'dot',
                ],
            ]);
    }
}
