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
            ])
            ->assertJson([
                'report_params' => [
                    'a_rho' => '1.400',
                ],
            ]);

        // Check the structure of the first element in render_params.line
        $this->assertArrayHasKey('title', $response->json('render_params.line.0'));
        $this->assertArrayHasKey('display_text', $response->json('render_params.line.0'));
        $this->assertArrayHasKey('x', $response->json('render_params.line.0'));
        $this->assertArrayHasKey('y', $response->json('render_params.line.0'));

        // Check the number of elements in render_params.line
        $this->assertCount(5, $response->json('render_params.line'));

        // Check the structure of the first element in render_params.dot
        $this->assertArrayHasKey('title', $response->json('render_params.dot.0'));
        $this->assertArrayHasKey('display_text', $response->json('render_params.dot.0'));
        $this->assertArrayHasKey('x', $response->json('render_params.dot.0'));
        $this->assertArrayHasKey('y', $response->json('render_params.dot.0'));

        // Check the number of elements in render_params.dot
        $this->assertCount(1, $response->json('render_params.dot'));
    }
}
