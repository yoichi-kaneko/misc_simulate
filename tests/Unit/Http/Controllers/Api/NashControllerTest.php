<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers\Api;

use App\Calculations\Nash;
use App\Http\Controllers\Api\NashController;
use App\Http\Requests\CalculateNashRequest;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class NashControllerTest extends TestCase
{
    /**
     * Test that the calculate method returns a responseException when an exception is thrown
     */
    public function testCalculateReturnsResponseExceptionWhenExceptionIsThrown(): void
    {
        // Mock the request
        $request = Mockery::mock(CalculateNashRequest::class);
        $request->shouldReceive('input')
            ->with('alpha_1')
            ->andReturn(['numerator' => 1, 'denominator' => 2]);
        $request->shouldReceive('input')
            ->with('alpha_2')
            ->andReturn(['numerator' => 1, 'denominator' => 2]);
        $request->shouldReceive('input')
            ->with('beta_1')
            ->andReturn(['numerator' => 1, 'denominator' => 2]);
        $request->shouldReceive('input')
            ->with('beta_2')
            ->andReturn(['numerator' => 1, 'denominator' => 2]);
        $request->shouldReceive('input')
            ->with('rho')
            ->andReturn(['numerator' => 1, 'denominator' => 2]);

        // Mock the calculator to throw an exception
        $calculator = Mockery::mock(Nash::class);
        $calculator->shouldReceive('run')
            ->with(
                ['numerator' => 1, 'denominator' => 2],
                ['numerator' => 1, 'denominator' => 2],
                ['numerator' => 1, 'denominator' => 2],
                ['numerator' => 1, 'denominator' => 2],
                ['numerator' => 1, 'denominator' => 2]
            )
            ->andThrow(new \Exception('Test exception message'));

        // Create the controller
        $controller = new NashController();

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

    /**
     * Test that the calculate method returns a successful response with the correct data
     */
    public function testCalculateReturnsSuccessfulResponseWithCorrectData(): void
    {
        // Mock the request
        $request = Mockery::mock(CalculateNashRequest::class);
        $request->shouldReceive('input')
            ->with('alpha_1')
            ->andReturn(['numerator' => 1, 'denominator' => 2]);
        $request->shouldReceive('input')
            ->with('alpha_2')
            ->andReturn(['numerator' => 1, 'denominator' => 2]);
        $request->shouldReceive('input')
            ->with('beta_1')
            ->andReturn(['numerator' => 1, 'denominator' => 2]);
        $request->shouldReceive('input')
            ->with('beta_2')
            ->andReturn(['numerator' => 1, 'denominator' => 2]);
        $request->shouldReceive('input')
            ->with('rho')
            ->andReturn(['numerator' => 1, 'denominator' => 2]);

        // Mock the calculator to return a successful result
        $expectedResult = [
            'p1' => 0.5,
            'p2' => 0.5,
            'u1' => 0.25,
            'u2' => 0.25,
        ];

        $calculator = Mockery::mock(Nash::class);
        $calculator->shouldReceive('run')
            ->with(
                ['numerator' => 1, 'denominator' => 2],
                ['numerator' => 1, 'denominator' => 2],
                ['numerator' => 1, 'denominator' => 2],
                ['numerator' => 1, 'denominator' => 2],
                ['numerator' => 1, 'denominator' => 2]
            )
            ->andReturn($expectedResult);

        // Create the controller
        $controller = new NashController();

        // Call the calculate method
        $response = $controller->calculate($request, $calculator);

        // Assert that the response is a JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Assert that the response status code is 200
        $this->assertSame(200, $response->getStatusCode());

        // Assert that the response contains the expected data
        $responseData = json_decode($response->getContent(), true);
        $this->assertSame($expectedResult, $responseData);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
