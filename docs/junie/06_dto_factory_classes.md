
# Laravelにおける非データベース関連DTOのファクトリー実装

## 現状の理解

`NashSimulationResult`クラスは、データベースやModelと関連のないDTOクラスであり、現在のテストではモックが使用されています。しかし、このDTOはメソッドやプロパティにアクセスしていないため、モックではなく実際のオブジェクトを生成するファクトリーアプローチが適切と考えられます。

## 非データベース関連DTOのファクトリー実装方法

Laravelの標準的なファクトリーは`Database\Factories`名前空間でデータベースモデルに紐づいていますが、DTOクラスのためには別のアプローチが必要です。以下に実装方法を提案します。

### 1. 専用の名前空間とディレクトリ構造の作成

DTOファクトリー用の専用名前空間とディレクトリ構造を作成します：

```
app/
├── Factories/
│   └── DTO/
│       ├── AbstractDTOFactory.php  # 基底ファクトリークラス
│       └── Nash/
│           └── NashSimulationResultFactory.php  # 具体的なファクトリー実装
```

### 2. 基底ファクトリークラスの実装

まず、すべてのDTOファクトリーの基底となる抽象クラスを作成します：

```php
<?php

declare(strict_types=1);

namespace App\Factories\DTO;

abstract class AbstractDTOFactory
{
    /**
     * デフォルト値でDTOを作成
     * @return mixed
     */
    abstract public function create();

    /**
     * カスタム値でDTOを作成
     * @param array $attributes
     * @return mixed
     */
    abstract public function createWith(array $attributes);
}
```

### 3. NashSimulationResultFactory の実装

次に、`NashSimulationResult`用の具体的なファクトリークラスを実装します：

```php
<?php

declare(strict_types=1);

namespace App\Factories\DTO\Nash;

use App\Calculations\Nash\DTO\NashSimulationResult;
use App\Factories\DTO\AbstractDTOFactory;
use Phospr\Fraction;

class NashSimulationResultFactory extends AbstractDTOFactory
{
    /**
     * デフォルト値でNashSimulationResultを作成
     * @return NashSimulationResult
     */
    public function create(): NashSimulationResult
    {
        return new NashSimulationResult(
            new Fraction(1, 2),  // alpha_x
            new Fraction(2, 3),  // alpha_y
            new Fraction(3, 4),  // beta_x
            new Fraction(4, 5),  // beta_y
            new Fraction(5, 6),  // rho_beta_x
            new Fraction(6, 7),  // rho_beta_y
            new Fraction(7, 8),  // gamma1_x
            new Fraction(8, 9),  // gamma2_y
            [                    // midpoint
                'x' => new Fraction(9, 10),
                'y' => new Fraction(10, 11)
            ],
            new Fraction(11, 12) // a_rho
        );
    }

    /**
     * カスタム値でNashSimulationResultを作成
     * @param array $attributes
     * @return NashSimulationResult
     */
    public function createWith(array $attributes): NashSimulationResult
    {
        $defaults = [
            'alpha_x' => new Fraction(1, 2),
            'alpha_y' => new Fraction(2, 3),
            'beta_x' => new Fraction(3, 4),
            'beta_y' => new Fraction(4, 5),
            'rho_beta_x' => new Fraction(5, 6),
            'rho_beta_y' => new Fraction(6, 7),
            'gamma1_x' => new Fraction(7, 8),
            'gamma2_y' => new Fraction(8, 9),
            'midpoint' => [
                'x' => new Fraction(9, 10),
                'y' => new Fraction(10, 11)
            ],
            'a_rho' => new Fraction(11, 12)
        ];

        $attributes = array_merge($defaults, $attributes);

        return new NashSimulationResult(
            $attributes['alpha_x'],
            $attributes['alpha_y'],
            $attributes['beta_x'],
            $attributes['beta_y'],
            $attributes['rho_beta_x'],
            $attributes['rho_beta_y'],
            $attributes['gamma1_x'],
            $attributes['gamma2_y'],
            $attributes['midpoint'],
            $attributes['a_rho']
        );
    }

    /**
     * 分数値を簡単に作成するヘルパーメソッド
     * @param int $numerator
     * @param int $denominator
     * @return Fraction
     */
    public function fraction(int $numerator, int $denominator): Fraction
    {
        return new Fraction($numerator, $denominator);
    }
}
```

### 4. ファクトリーの使用方法

テストコードでのファクトリーの使用例：

```php
<?php

use App\Factories\DTO\Nash\NashSimulationResultFactory;
use Tests\TestCase;

class NashTest extends TestCase
{
    private NashSimulationResultFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new NashSimulationResultFactory();
    }

    public function testExample()
    {
        // デフォルト値でDTOを作成
        $result = $this->factory->create();
        
        // または、カスタム値でDTOを作成
        $customResult = $this->factory->createWith([
            'alpha_x' => $this->factory->fraction(2, 3),
            'alpha_y' => $this->factory->fraction(3, 4)
        ]);
        
        // テストコードでDTOを使用
        $this->assertEquals(2, $customResult->getAlphaX()->getNumerator());
        $this->assertEquals(3, $customResult->getAlphaX()->getDenominator());
    }
}
```

### 5. サービスプロバイダーへの登録（オプション）

ファクトリーをLaravelのサービスコンテナに登録することで、依存性注入を使用して簡単にアクセスできるようになります：

```php
<?php

namespace App\Providers;

use App\Factories\DTO\Nash\NashSimulationResultFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(NashSimulationResultFactory::class, function () {
            return new NashSimulationResultFactory();
        });
    }
}
```

## 将来的な拡張性

このアプローチは、将来的に他のDTOクラスにも適用できます。例えば、新しいDTOクラス `CentipedeSimulationResult` を作成する場合は、同様のパターンで `CentipedeSimulationResultFactory` を実装できます。

## まとめ

1. データベースやModelと関連のないDTOクラスには、専用の名前空間 `App\Factories\DTO` を作成する
2. 基底となる `AbstractDTOFactory` クラスを実装し、共通のインターフェースを提供する
3. 具体的なDTOクラスごとに専用のファクトリークラスを実装する
4. ファクトリークラスには、デフォルト値での作成とカスタム値での作成の両方のメソッドを提供する
5. 必要に応じて、ヘルパーメソッドを追加して使いやすくする

このアプローチにより、テストコードがよりシンプルになり、実際のオブジェクトを使用することでテストの信頼性も向上します。また、将来的にDTOの構造が変わった場合も、ファクトリークラスを更新するだけで対応できるため、メンテナンス性も高まります。
