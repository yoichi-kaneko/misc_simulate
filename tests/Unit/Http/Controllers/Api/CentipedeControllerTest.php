<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers\Api;

use App\Calculations\Centipede;
use App\Http\Controllers\Api\CentipedeController;
use App\Http\Requests\CalculateCentipedeRequest;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class CentipedeControllerTest extends TestCase
{
    /**
     * Test that the calculate method returns a responseException when an exception is thrown
     */
    public function testCalculateReturnsResponseExceptionWhenExceptionIsThrown(): void
    {
        // Mock the request
        $request = Mockery::mock(CalculateCentipedeRequest::class);
        $request->shouldReceive('input')
            ->with('patterns')
            ->andReturn(['pattern1', 'pattern2']);
        $request->shouldReceive('input')
            ->with('max_step')
            ->andReturn('5');
        $request->shouldReceive('input')
            ->with('max_rc')
            ->andReturn('10');
        $request->shouldReceive('input')
            ->with('combination_player_1', null)
            ->andReturn(null);

        // Mock the calculator to throw an exception
        $calculator = Mockery::mock(Centipede::class);
        $calculator->shouldReceive('run')
            ->with(['pattern1', 'pattern2'], 5, 10, null)
            ->andThrow(new \Exception('Test exception message'));

        // Create the controller
        $controller = new CentipedeController();

        // Call the calculate method
        $response = $controller->calculate($request, $calculator);

        // Assert that the response is a JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Assert that the response status code is 422
        $this->assertSame(422, $response->getStatusCode());

        // Assert that the response contains the expected error message
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertArrayHasKey('exception_message', $responseData['errors']);
        $this->assertSame(['Test exception message'], $responseData['errors']['exception_message']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
