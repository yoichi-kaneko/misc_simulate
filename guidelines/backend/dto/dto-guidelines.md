# DTOクラスのガイドライン

## DTOクラスのPHPDoc

DTOクラスのメソッドには、以下の方針でPHPDocを記載します：

- すべてのゲッターメソッドには適切なPHPDocを記載
- PHPDocには以下の情報を含める：
  - メソッドの説明（例：「alpha_xを取得する」）
  - 戻り値の型（例：「@return Fraction」）
- 複雑なDTOの場合は、プロパティの意味や使用方法についても説明を追加

## 配列型のPHPDoc

配列型をPHPDocに記載する際は、以下の方針に従います：

- 単に `array` と記載するのではなく、最低でも `array{key: mixed}` の形式で配列の構造を明示する
- 配列の要素の型が明確な場合は、`array{key: string, count: int}` のように具体的な型を指定する
- 連想配列でない場合は、`array<int, string>` のように記載する
- これにより、コードの可読性が向上し、型の不一致によるエラーを防止できる

**注意**: 既存コードの一部にはまだ詳細な配列型の記述が適用されていない箇所があります。例えば、`NashSimulationResult`クラスの`getMidpoint()`メソッドのPHPDocでは、戻り値の型が単に`array`と記載されています。今後のコード修正時に、ガイドラインに沿った形式に更新していく予定です。

## DTOクラス用のファクトリー

データベースやModelと関連のないDTOクラスのインスタンス生成を簡素化するために、専用のファクトリークラスを実装しています。これらのファクトリーは、特にテストコードでの使用を想定しています。

### ファクトリークラスの構造と命名規則

- ファクトリークラスは `App\Factories\DTO` 名前空間に配置する
- 基底クラスとして `AbstractDTOFactory` を用意し、すべてのDTOファクトリーはこれを継承する
- 具体的なファクトリークラスは、対応するDTOクラスの名前に `Factory` を付けた名前とする（例：`NashSimulationResult` → `NashSimulationResultFactory`）
- ファクトリークラスは対応するDTOクラスと同じディレクトリ構造に配置する（例：`App\Calculations\Nash\DTO\NashSimulationResult` → `App\Factories\DTO\Nash\NashSimulationResultFactory`）

### AbstractDTOFactoryクラス

`AbstractDTOFactory` クラスは、すべてのDTOファクトリーの基底となる抽象クラスです：

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
    abstract public function create(): mixed;

    /**
     * カスタム値でDTOを作成
     * @param array $attributes
     * @return mixed
     */
    abstract public function createWith(array $attributes): mixed;
}
```

### 具体的なファクトリークラスの実装

具体的なファクトリークラスは、`AbstractDTOFactory` を継承し、以下のメソッドを実装します：

1. **create()**: デフォルト値でDTOを作成するメソッド
2. **createWith(array $attributes)**: カスタム値でDTOを作成するメソッド
3. **必要に応じたヘルパーメソッド**: DTOの作成を補助するメソッド

実装例（`NashSimulationResultFactory`）：

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

### ファクトリークラスの使用方法

テストコードでのファクトリークラスの使用例：

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

### ファクトリークラスとモックの使い分け

- **ファクトリークラスを使用する場合**:
  - DTOの実際のインスタンスが必要で、その内部状態にアクセスする場合
  - テスト対象のクラスがDTOの実際の振る舞いに依存している場合
  - 複数のテストで同じDTOインスタンスを再利用する場合

- **モックを使用する場合**:
  - DTOの特定のメソッドの振る舞いを制御する必要がある場合
  - DTOのメソッド呼び出しを検証する必要がある場合
  - テスト対象のクラスがDTOの内部実装に依存せず、インターフェースのみに依存している場合

### サービスコンテナへの登録（オプション）

必要に応じて、ファクトリークラスをLaravelのサービスコンテナに登録することで、依存性注入を使用して簡単にアクセスできるようになります：

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

### ベストプラクティス

- ファクトリークラスは単一責任の原則に従い、DTOの生成のみを担当する
- デフォルト値は実際のユースケースに基づいた現実的な値を設定する
- `createWith()` メソッドでは、必要な属性のみを上書きできるようにする
- 複雑なDTOの作成を簡素化するためのヘルパーメソッドを提供する
- ファクトリークラス自体のテストも作成し、正しいDTOが生成されることを確認する

## 関連ドキュメント

- [PHP開発ガイドライン](../php-guidelines.md) - App\Calculationsクラスと分数計算の実装
- [ユニットテスト](../testing/unit-tests.md) - PHPユニットテストの実装方針