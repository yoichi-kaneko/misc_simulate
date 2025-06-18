# PHPユニットテスト

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
  - Requestクラスルールにminやmaxがある場合、その境界の両方をテストする
    - 例として、min:1が設定されているならば「0でバリデーションがfalseとなる事、1でバリデーションがtrueとなる事」の2つをテストケースとして記載する
- Ruleクラスのテストでは：
  - Ruleクラスで定義されているカスタムルール内には分数に関わるルールがある。これの分数の検証はRuleクラスのユニットテストで定義する
  - 分数の境界問題は、分母を1000に固定し、分子を変動させる事で境界をテストする
  - 例として、`FractionMax` の境界のテストを行う際、最大値が5の場合「分母を1000に固定、分子が5001でバリデーションがfalseとなる事、5000でバリデーションがtrueとなる事」の2つをテストケースとして記載する
- ユニットテストを作成する時は、テスト対象クラスの1メソッドに対してユニットテストもメソッドを1件以上作成すること。1つのユニットテストメソッド内で複数のメソッドを検証しない。
- PHPユニットテストを書くときは、正常系の確認と異常系の確認は別のケースとして記述すること。これにより、テストの目的が明確になり、障害発生時の原因特定が容易になります。

## 依存性注入とモックを使用したテスト設計パターン

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

## その他のユニットテストのベストプラクティス

1. **テストの命名規則**
   - テストメソッド名は `test` で始めるか、`@test` アノテーションを使用する
   - メソッド名は「何をテストするか」を明確に表現する（例：`testRunDelegation`, `testExceptionHandling`）

2. **テストデータの準備**
   - テストデータは各テストメソッド内で明示的に定義し、グローバル変数や共有状態に依存しない
   - 複雑なテストデータの準備には、ファクトリーやデータプロバイダーを使用する
   - 1つのメソッドに対して複数のデータパターンを用いて繰り返しテストを行う場合は、dataProviderを定義してそこにデータセットを羅列する
   - ユニットテストでdataProviderを使用するときは、public static functionを宣言する
   - dataProviderでは、配列のキーに日本語を使用することも許容する（例：'ケース1', '正常系', '異常系：値が範囲外'）
   - dataProviderの実装例：
     ```php
     /**
      * テストメソッド
      * @test
      * @dataProvider exampleDataProvider
      */
     public function testExample(int $input, string $expected)
     {
         $result = $this->someMethod($input);
         $this->assertEquals($expected, $result);
     }

     /**
      * データプロバイダー
      * @return array<string, mixed>
      */
     public static function exampleDataProvider(): array
     {
         return [
             '正常系：通常入力' => [
                 'input' => 5,
                 'expected' => '結果A',
             ],
             '正常系：境界値' => [
                 'input' => 10,
                 'expected' => '結果B',
             ],
             '異常系：特殊ケース' => [
                 'input' => 0,
                 'expected' => '結果C',
             ],
         ];
     }
     ```

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

## 関連ドキュメント

- [カバレッジ](coverage.md) - PHPUnitのカバレッジレポート
- [PHP開発ガイドライン](../php-guidelines.md) - App\Calculationsクラスと分数計算の実装
- [DTOガイドライン](../dto/dto-guidelines.md) - DTOクラスのPHPDocと配列型の記述方法
