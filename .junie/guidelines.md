# ゲーム理論シミュレーションプロジェクト開発ガイドライン

## 1. プロジェクト概要

このプロジェクトは、ゲーム理論の概念（ナッシュの社会厚生、ムカデゲームなど）をシミュレーションするためのWebアプリケーションです。Laravel（PHP）をバックエンドに、JavaScript/TypeScriptをフロントエンドに使用しています。

## 2. サーバーサイド開発ガイドライン

### 2.1 App\Calculations クラス

App\Calculations ディレクトリには、ゲーム理論の計算を行うクラスが含まれています。現在、以下のクラスが実装されています：

#### 2.1.1 Nash 関連クラス

Nash 関連クラスは、ナッシュの社会厚生の計算を行います。計算処理とフォーマット処理を分離するために、以下のクラス構成になっています：

##### 2.1.1.1 Nash クラス

Nash クラスは、計算とフォーマットの橋渡しを行うファサードクラスです。主な特徴：

- `App\Calculations\Nash\Simulator\NashSimulator` と `App\Calculations\Nash\Formatter\NashFormatter` を内部で使用
- 主要メソッド：
  - `run()`: 計算のエントリーポイント（シミュレーションの実行とフォーマットを委譲）

##### 2.1.1.2 NashSimulator クラス

NashSimulator クラス (`App\Calculations\Nash\Simulator\NashSimulator`) は、ナッシュの社会厚生の計算ロジックを担当します。主な特徴：

- 分数計算に `Phospr\Fraction` ライブラリを使用
- 主要メソッド：
  - `run()`: シミュレーションを実行し、`App\Calculations\Nash\DTO\NashSimulationResult` を返す
  - `calcGamma1X()`: ガンマ1のX座標を計算
  - `calcGamma2Y()`: ガンマ2のY座標を計算
  - `calcMidpoint()`: 中点を計算
  - `calcARho()`: a_rho値を計算

##### 2.1.1.3 NashFormatter クラス

NashFormatter クラス (`App\Calculations\Nash\Formatter\NashFormatter`) は、計算結果をフロントエンド用に整形します。主な特徴：

- 主要メソッド：
  - `format()`: `App\Calculations\Nash\DTO\NashSimulationResult` をフロントエンド用の配列に変換
  - `getDisplayText()`: 表示用テキストを生成

##### 2.1.1.4 NashSimulationResult クラス

NashSimulationResult クラス (`App\Calculations\Nash\DTO\NashSimulationResult`) は、シミュレーション結果を保持するDTOです。主な特徴：

- シミュレーション結果のすべての値を保持
- ゲッターメソッドを提供
- 各ゲッターメソッドには適切なPHPDocを記載（プロパティの説明と戻り値の型）

#### 2.1.2 Centipede クラス

Centipede クラスは、ムカデゲームのシミュレーションを行います。主な特徴：

- 複雑な数学的計算を `eval()` を使用して実行
- 主要メソッド：
  - `run()`: 複数パターンの計算を実行
  - `calculatePattern()`: 特定パターンの計算を実行
  - `unionCalculateData()`: 2つのプレイヤーのシミュレーション結果を合算
  - `calcCognitiveUnitValue()`: 認知単位の値を計算
  - `makeCognitiveUnitLatexText()`: LaTeX形式のテキストを生成
  - `makeChartData()`: チャート表示用データを生成

**注意**: 現在、Centipedeクラスは計算ロジックと表示ロジックが混在しています。将来的には、Nashクラスと同様に、計算処理（CentipedeSimulator）とフォーマット処理（CentipedeFormatter）に分離し、DTOを使用したリファクタリングを行う予定です。これにより、コードの保守性と拡張性が向上します。

### 2.2 分数計算の実装

計算クラスでは、精度を保つために分数計算を多用しています：

- `Phospr\Fraction` ライブラリを使用して分数を表現
- 分数の加減乗除は、対応するメソッド（`add()`, `subtract()`, `multiply()`, `divide()`）を使用
- 分数から浮動小数点数への変換は `toFloat()` メソッドを使用
- 分子と分母の取得には `getNumerator()` と `getDenominator()` メソッドを使用

#### 2.2.1 Fractionクラスの仕様と実装方針について

- `Phospr\Fraction` ライブラリでは$denominatorに0や負の値を入れると例外をスローします
- シミュレーションの実装上発生する事があるのは以下のケースです
  - new Fraction でインスタンスを生成するとき、第2引数に0や負の整数をセットする
  - Fractionのdivideメソッドを呼んだ時、引数に負の値を持つFractionインスタンスをセットする
- Requestクラスのバリデーションによりこの例外が発生しないパラメータである事を保証しているため、通常の処理では例外は発生しません
- そのため、Simulatorクラスで例外のコントロールは実装していません
- ただしユニットテスト側では例外が発生する事に対するテストケースを実装しています

### 2.3 PHPユニットテスト

PHPのユニットテストは、以下の方針で実装しています：

- `tests/Unit` ディレクトリにテストクラスを配置
- 計算クラスのテスト（例：`NashTest`, `CentipedeTest`）では：
  - privateメソッドのテストには `ReflectionClass` を使用
  - 複数のテストケースをデータプロバイダーで提供
  - 分数の計算結果は分子と分母の両方を検証
- リクエストバリデーションのテスト（例：`CalculateNashRequestTest`, `CalculateCentipedeRequestTest`）では：
  - `authorize()` メソッドのテスト
  - バリデーションルールの存在と内容の検証
  - 有効なデータと無効なデータの両方でのバリデーション検証
  - カスタムバリデーションルール（`FractionMax`, `Coordinate`など）のテスト
- ユニットテストを作成する時は、テスト対象クラスの1メソッドに対してユニットテストもメソッドを1件以上作成すること。1つのユニットテストメソッド内で複数のメソッドを検証しない。
- PHPユニットテストを書くときは、正常系の確認と異常系の確認は別のケースとして記述すること。これにより、テストの目的が明確になり、障害発生時の原因特定が容易になります。

#### 2.3.1 依存性注入とモックを使用したテスト設計パターン

外部依存を持つクラスのテストでは、以下の設計パターンを採用しています：

1. **テストクラスの基本構造**
   - Laravelの `Tests\TestCase` クラスを継承する
   - これにより、Laravelのテスト用メソッドやアサーションが使用可能になる

2. **外部依存の注入方法**
   - 外部から注入するクラスはメンバ変数として定義する
   - `setUp()` メソッド内で `createMock()` を使用してモックインスタンスを作成する
   - 各テストケース内でモックの振る舞いを定義する
   - テスト対象クラスを生成する直前に `instance()` メソッドを使用してモックをLaravelのサービスコンテナに登録する
   - `app()->make()` を使用してテスト対象クラスをLaravelのサービスコンテナから取得する（モックが自動的に注入される）

3. **実装例（NashTest クラス）**
   ```php
   class NashTest extends TestCase
   {
       /** @var NashSimulator&MockObject $simulator */
       private NashSimulator $simulator;

       /** @var NashFormatter&MockObject $formatter */
       private NashFormatter $formatter;

       /**
        * @return void
        * @throws Exception
        */
       public function setUp(): void
       {
           parent::setUp();

           // シミュレーターとフォーマッターのモックを作成
           $this->simulator = $this->createMock(NashSimulator::class);
           $this->formatter = $this->createMock(NashFormatter::class);
       }

       /**
        * テストの前にNashクラスのインスタンスを作成します。
        * @return Nash
        * @throws BindingResolutionException
        */
       private function makeInstance(): Nash
       {
           // モックをLaravelのサービスコンテナに登録
           $this->instance(NashSimulator::class, $this->simulator);
           $this->instance(NashFormatter::class, $this->formatter);

           // テスト対象クラスをサービスコンテナから取得
           /** @var Nash */
           return app()->make(Nash::class);
       }

       /**
        * テストメソッドの例
        * @test
        */
       public function testSomeMethod()
       {
           // モックの振る舞いを定義
           $this->simulator->expects($this->once())
               ->method('someMethod')
               ->willReturn($expectedValue);

           // テスト対象クラスのインスタンスを作成
           $instance = $this->makeInstance();

           // テスト対象メソッドを実行
           $result = $instance->methodToTest();

           // 結果を検証
           $this->assertEquals($expectedValue, $result);
       }
   }
   ```

4. **このパターンの利点**
   - テスト対象クラスを外部依存から分離できる
   - 外部依存の振る舞いを完全に制御できる
   - 特定の条件や例外ケースのテストが容易になる
   - Laravelのサービスコンテナを活用した依存性注入が可能

5. **注意点**
   - モックの作成は `setUp()` メソッド内で一度だけ行い、テストメソッド内で再作成しない
   - 各テストメソッドでは、そのテストに必要なモックの振る舞いのみを定義する
   - 例外をテストする場合は、`expectException()` と `expectExceptionMessage()` を使用する

#### 2.3.2 その他のユニットテストのベストプラクティス

1. **テストの命名規則**
   - テストメソッド名は `test` で始めるか、`@test` アノテーションを使用する
   - メソッド名は「何をテストするか」を明確に表現する（例：`testRunDelegation`, `testExceptionHandling`）

2. **テストデータの準備**
   - テストデータは各テストメソッド内で明示的に定義し、グローバル変数や共有状態に依存しない
   - 複雑なテストデータの準備には、ファクトリーやデータプロバイダーを使用する

3. **アサーションの使用**
   - 各テストでは、テスト対象の動作に関連する最小限のアサーションのみを使用する
   - 複数の関連しない動作を1つのテストで検証しない
   - 適切なアサーションメソッドを使用する（例：`assertEquals`, `assertSame`, `assertInstanceOf`）

4. **テストの独立性**
   - 各テストは他のテストに依存せず、任意の順序で実行できるようにする
   - テスト間で状態を共有しない
   - テスト実行後は、変更した状態を元に戻す

5. **モックとスタブの適切な使用**
   - テスト対象クラスの直接の依存のみをモック化し、過剰なモック化を避ける
   - モックの振る舞いは、テストの目的に必要な最小限に留める
   - 実際のオブジェクトの代わりにモックを使用する理由を明確にする

6. **テストカバレッジ**
   - 重要なビジネスロジックや複雑な条件分岐は優先的にテストする
   - エッジケースや例外ケースも考慮したテストを作成する
   - カバレッジレポートを定期的に確認し、テストが不足している領域を特定する

### 2.4 PHPUnitのカバレッジレポート

コードカバレッジは、テストによってどの程度のコードが実行されたかを測定する指標です。PHPUnitでは、以下の方針でカバレッジレポートを生成・管理しています：

- `coverage` ディレクトリにPHPUnitのカバレッジレポートを出力し、保管します
- このディレクトリはgit管理外のため、git上には存在せず、ローカル環境でのみ出力・利用します
- カバレッジレポートの設定は `phpunit.xml` ファイルの `<coverage>` セクションで定義されています
- HTMLフォーマットのレポートは `coverage` ディレクトリに、Cloverフォーマットのレポートは `coverage.xml` ファイルに出力されます

カバレッジレポートの生成方法：
```bash
# カバレッジレポートを生成するPHPUnitテストの実行
php artisan test --coverage
```

カバレッジレポートの活用方法：
- HTMLレポートをブラウザで開き、テストカバレッジの視覚的な確認が可能
- 未テストのコード部分を特定し、テスト強化の優先順位付けに活用
- コードの品質向上のための指標として利用

**注意**: カバレッジ100%を目指すことが目的ではなく、重要なビジネスロジックや複雑な条件分岐が適切にテストされていることを確認するための指標として活用してください。

### 2.5 DTOクラスのPHPDoc

DTOクラスのメソッドには、以下の方針でPHPDocを記載します：

- すべてのゲッターメソッドには適切なPHPDocを記載
- PHPDocには以下の情報を含める：
  - メソッドの説明（例：「alpha_xを取得する」）
  - 戻り値の型（例：「@return Fraction」）
- 複雑なDTOの場合は、プロパティの意味や使用方法についても説明を追加

### 2.6 配列型のPHPDoc

配列型をPHPDocに記載する際は、以下の方針に従います：

- 単に `array` と記載するのではなく、最低でも `array{key: mixed}` の形式で配列の構造を明示する
- 配列の要素の型が明確な場合は、`array{key: string, count: int}` のように具体的な型を指定する
- 連想配列でない場合は、`array<int, string>` のように記載する
- これにより、コードの可読性が向上し、型の不一致によるエラーを防止できる

**注意**: 既存コードの一部にはまだ詳細な配列型の記述が適用されていない箇所があります。例えば、`NashSimulationResult`クラスの`getMidpoint()`メソッドのPHPDocでは、戻り値の型が単に`array`と記載されています。今後のコード修正時に、ガイドラインに沿った形式に更新していく予定です。

### 2.7 DTOクラス用のファクトリー

データベースやModelと関連のないDTOクラスのインスタンス生成を簡素化するために、専用のファクトリークラスを実装しています。これらのファクトリーは、特にテストコードでの使用を想定しています。

#### 2.7.1 ファクトリークラスの構造と命名規則

- ファクトリークラスは `App\Factories\DTO` 名前空間に配置する
- 基底クラスとして `AbstractDTOFactory` を用意し、すべてのDTOファクトリーはこれを継承する
- 具体的なファクトリークラスは、対応するDTOクラスの名前に `Factory` を付けた名前とする（例：`NashSimulationResult` → `NashSimulationResultFactory`）
- ファクトリークラスは対応するDTOクラスと同じディレクトリ構造に配置する（例：`App\Calculations\Nash\DTO\NashSimulationResult` → `App\Factories\DTO\Nash\NashSimulationResultFactory`）

#### 2.7.2 AbstractDTOFactoryクラス

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

#### 2.7.3 具体的なファクトリークラスの実装

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

#### 2.7.4 ファクトリークラスの使用方法

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

#### 2.7.5 ファクトリークラスとモックの使い分け

- **ファクトリークラスを使用する場合**:
  - DTOの実際のインスタンスが必要で、その内部状態にアクセスする場合
  - テスト対象のクラスがDTOの実際の振る舞いに依存している場合
  - 複数のテストで同じDTOインスタンスを再利用する場合

- **モックを使用する場合**:
  - DTOの特定のメソッドの振る舞いを制御する必要がある場合
  - DTOのメソッド呼び出しを検証する必要がある場合
  - テスト対象のクラスがDTOの内部実装に依存せず、インターフェースのみに依存している場合

#### 2.7.6 サービスコンテナへの登録（オプション）

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

#### 2.7.7 ベストプラクティス

- ファクトリークラスは単一責任の原則に従い、DTOの生成のみを担当する
- デフォルト値は実際のユースケースに基づいた現実的な値を設定する
- `createWith()` メソッドでは、必要な属性のみを上書きできるようにする
- 複雑なDTOの作成を簡素化するためのヘルパーメソッドを提供する
- ファクトリークラス自体のテストも作成し、正しいDTOが生成されることを確認する

## 3. フロントエンド開発ガイドライン

### 3.1 TypeScriptへの移行

現在、JavaScriptからTypeScriptへの移行を進めています：

- 新規コードはTypeScriptで記述
- 既存のJavaScriptコードは段階的にTypeScriptに移行中
- `tsconfig.json` で型チェックの設定を管理

TypeScript化の進捗状況：
- Reactコンポーネントの実装にTypeScriptを使用
- 主要な計算ロジックのTypeScript化が完了
- 残りのユーティリティ関数やヘルパー関数のTypeScript化を進行中
- 今後6ヶ月以内に全てのJavaScriptコードをTypeScriptに移行する予定

#### 3.1.1 TypeScript実装例（nash.ts）

- 型定義を使用した関数宣言（例：`function reset(): void`）
- ES6モジュールインポート構文の使用
- イベントハンドラの実装

#### 3.1.2 JavaScript実装例（centipede.js）

- ES6モジュールインポート構文の使用（例：`import {beforeCalculate} from "../functions/calculate"`）
- jQueryを使用したDOM操作とイベントハンドリング
- ブラウザ検出とチャートダウンロード機能

### 3.2 jQueryからReactへの移行

- 現在、jQueryからReactへの移行は進行中です。新規コンポーネントはReactで実装
- 既存のjQueryコードは段階的にReactコンポーネントに置き換え中
- jQueryの使用は最小限に抑え、Reactの状態管理を活用
- jQueryで書かれたコードは、段階的にReactのライフサイクルメソッドに置き換え
- 例えば、nash.tsではリセットボタンの機能をjQueryからReactに移行しました

React導入の進捗状況：
- ナッシュシミュレーション画面のコンポーネント化が完了
- ムカデゲーム画面のReactコンポーネント化を進行中
- 共通UIコンポーネントのライブラリ化を検討中
- 今後1年以内に全てのフロントエンドコードをReactベースに移行する予定

### 3.3 フロントエンド共通ガイドライン

- KaTeXライブラリを使用して数式を表示
- チャート表示には適切なライブラリを使用
- フォーム入力値のリセット機能を実装
- 計算中はスピナーを表示

### 3.4 コード品質管理

- ESLintを導入して、コードの品質と一貫性を確保
- ESLintを導入したが、現在は修正が未完了で、ESLintチェックを行なってもエラーが発生します。順次原因箇所の調査、修正を行なっていきます。
- `eslint.config.mjs`ファイルでプロジェクト固有のルールを定義
- `package.json`のスクリプトセクションにESLint関連のコマンドを追加

## 4. 開発プロセス

### 4.1 新機能の追加

1. サーバーサイドの計算クラスを `App\Calculations` に実装
2. 対応するユニットテストを `tests/Unit/Calculations` に実装
3. リクエストバリデーションクラスを作成し、テストを実装
4. フロントエンドのTypeScriptコードを実装

### 4.2 既存機能の修正

1. 該当するテストを確認または追加
2. サーバーサイドコードを修正
3. フロントエンドコードを修正
4. テストを実行して変更を検証

## 5. junieからの提案書について

- junieに提案書を作成してもらった際には、 `docs/junie/` 以下に保存します
- junieにコード修正依頼を行う時、この提案書に沿って対応を依頼する場合があります

## 6. 注意事項

- 分数計算の実装は複雑なため、コメントを詳細に記述し、テストで十分に検証してください
- 数学的な計算式は、可能な限り定数として定義し、コメントで説明を追加してください
- JavaScriptからTypeScriptへの移行は慎重に行い、既存の機能を損なわないようにしてください
- `eval()` の使用は必要最小限に抑え、入力値のバリデーションを徹底してください

## 7. 不明確な点

- 分数計算の一部で、計算式が複雑になっている箇所があります。これらの計算式の数学的背景や意図については、必要に応じて専門家に確認することをお勧めします。
- Centipedeクラスの計算式（ODD_NU, EVEN_NU など）の詳細な意味や導出過程は、ゲーム理論の専門知識が必要な場合があります。
