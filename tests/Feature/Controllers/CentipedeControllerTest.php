<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use Tests\TestCase;

class CentipedeControllerTest extends TestCase
{
    /**
     * indexで200ステータスを返すことを確認するテスト
     * @test
     * @return void
     */
    public function index_200()
    {
        $response = $this->get('/centipede');

        $response->assertStatus(200);
    }
}
