<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\Authenticate;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    private Authenticate $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $auth = $this->createMock(\Illuminate\Contracts\Auth\Factory::class);
        $this->middleware = new Authenticate($auth);
    }

    /**
     * JSONリクエストでない場合にloginルートにリダイレクトすることをテスト
     * @test
     */
    public function redirectToReturnsLoginRouteWhenRequestDoesNotExpectJson()
    {
        // JSONを期待しないリクエストをモック
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('expectsJson')
            ->once()
            ->andReturn(false);

        // protected methodをテストするためにReflectionを使用
        $reflector = new \ReflectionClass(Authenticate::class);
        $method = $reflector->getMethod('redirectTo');

        // メソッドを実行して結果を検証
        $result = $method->invoke($this->middleware, $request);
        $this->assertSame(route('centipede.index'), $result);
    }

    /**
     * JSONリクエストの場合にnullを返すことをテスト
     * @test
     */
    public function redirectToReturnsNullWhenRequestExpectsJson()
    {
        // JSONを期待するリクエストをモック
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('expectsJson')
            ->once()
            ->andReturn(true);

        // protected methodをテストするためにReflectionを使用
        $reflector = new \ReflectionClass(Authenticate::class);
        $method = $reflector->getMethod('redirectTo');

        // メソッドを実行して結果を検証
        $result = $method->invoke($this->middleware, $request);
        $this->assertNull($result);
    }
}
