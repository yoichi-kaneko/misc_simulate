<?php

namespace Feature\Controllers;

use Tests\TestCase;

class NashControllerTest extends TestCase
{
    /**
     * indexで200ステータスを返すことを確認するテスト
     * @test
     * @return void
     */
    public function index_200()
    {
        $response = $this->get('/nash');

        $response->assertStatus(200);
    }
}
