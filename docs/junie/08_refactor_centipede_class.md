# Centipede クラスリファクタリング計画書

## 1. 現状の問題点

現在の `Centipede` クラスには以下の問題点があります：

1. **単一責任の原則違反**：クラスが複数の責任を持っています
    - シミュレーション実行
    - パターン計算
    - 数式評価
    - データ結合
    - 値計算
    - テキスト整形
    - チャートデータ生成

2. **セキュリティリスク**：`eval()` 関数の使用
    - 文字列として渡された式を実行するため、潜在的なセキュリティリスクがあります

3. **テスト容易性の低さ**：
    - 責任が混在しているため、単体テストが困難です
    - 特に数学的計算の正確性を検証するテストが不足しています

## 2. リファクタリング目標

1. **クラス構成の分離**：単一責任の原則に従ったクラス設計
2. **ユニットテストの作成**：各クラスの機能を検証するテスト
3. **eval() の安全な代替手段への置き換え**：セキュリティリスクの排除

## 3. 新しいクラス構成

Nashシミュレータの構造を参考に、以下のクラス構成に分離します：

### 3.1 コアクラス

```
App\Calculations\Centipede\
├── DTO/
│   └── CentipedeSimulationResult.php  # シミュレーション結果を保持するDTO
├── Simulator/
│   └── CentipedeSimulator.php         # コア計算ロジック
├── Formatter/
│   └── CentipedeFormatter.php         # 表示用フォーマッタ
└── Calculator/
    └── FormulaCalculator.php          # eval()の代替となる数式計算機
```

### 3.2 ファクトリクラス

```
App\Factories\DTO\Centipede\
└── CentipedeSimulationResultFactory.php  # DTOを作成するファクトリ
```

## 4. 各クラスの責任と実装詳細

### 4.1 CentipedeSimulationResult (DTO)

**責任**：
- シミュレーション結果データの保持
- イミュータブルなデータ構造の提供

**実装詳細**：
- 現在の `calculatePattern` メソッドの戻り値に相当するデータを保持
- 必要なゲッターメソッドを提供
- イミュータブルな設計（すべてのプロパティをprivateにし、コンストラクタで初期化）

```php
class CentipedeSimulationResult
{
    private float $cognitiveUnitValue;
    private string $cognitiveUnitLatexText;
    private float $averageOfReversedCausality;
    private array $data;
    private array $chartData;
    
    // コンストラクタとゲッターメソッド
}
```

### 4.2 CentipedeSimulator

**責任**：
- シミュレーション実行のコア処理
- 数学的計算の実行

**実装詳細**：
- 現在の `run` メソッドと `calculatePattern` メソッドの機能を提供
- `FormulaCalculator` を使用して数式を評価
- 計算結果を `CentipedeSimulationResult` として返す

```php
class CentipedeSimulator
{
    private FormulaCalculator $calculator;
    
    // 定数定義（現在のODD_NU, EVEN_NUなど）
    
    public function __construct(FormulaCalculator $calculator)
    {
        $this->calculator = $calculator;
    }
    
    public function run(array $patterns, int $maxStep, ?int $maxRc, ?array $combinationPlayer1): array
    {
        // 現在のrunメソッドの実装
    }
    
    private function calculatePattern(int $baseNumerator, int $numeratorExp1, int $numeratorExp2, int $denominatorExp, int $maxStep): CentipedeSimulationResult
    {
        // 現在のcalculatePatternメソッドの実装
    }
    
    private function calcCognitiveUnitValue(int $baseNumerator, int $numeratorExp1, int $numeratorExp2, int $denominatorExp): float
    {
        // 現在のcalcCognitiveUnitValueメソッドの実装
    }
}
```

### 4.3 CentipedeFormatter

**責任**：
- シミュレーション結果の表示用フォーマット

**実装詳細**：
- 現在の `makeCognitiveUnitLatexText` メソッドの機能を提供
- チャートデータの生成（現在の `makeChartData` メソッド）
- フロントエンド用のデータ構造への変換

```php
class CentipedeFormatter
{
    public function format(CentipedeSimulationResult $result): array
    {
        // フロントエンド用のデータ構造に変換
    }
    
    public function makeCognitiveUnitLatexText(int $baseNumerator, int $numeratorExp1, int $numeratorExp2, int $denominatorExp): string
    {
        // 現在のmakeCognitiveUnitLatexTextメソッドの実装
    }
    
    public function makeChartData(array $data): array
    {
        // 現在のmakeChartDataメソッドの実装
    }
}
```

### 4.4 FormulaCalculator

**責任**：
- 数式の安全な評価
- `eval()` の代替機能の提供

**実装詳細**：
- 数式パーサーライブラリを使用して数式を評価
- 特定の関数（pow, ceil, number_formatなど）のサポート
- 安全な評価のための入力検証

```php
class FormulaCalculator
{
    /**
     * 数式を安全に評価する
     * @param string $formula 評価する数式
     * @return mixed 計算結果
     */
    public function calculate(string $formula): mixed
    {
        // 数式パーサーを使用して安全に評価
    }
    
    /**
     * 特定のフォーマットの数式を評価する
     * @param string $format フォーマット文字列
     * @param array $args フォーマットに適用する引数
     * @return mixed 計算結果
     */
    public function calculateFormatted(string $format, ...$args): mixed
    {
        $formula = sprintf($format, ...$args);
        return $this->calculate($formula);
    }
}
```

### 4.5 CentipedeSimulationResultFactory

**責任**：
- `CentipedeSimulationResult` インスタンスの作成

**実装詳細**：
- デフォルト値での作成メソッド
- カスタム値での作成メソッド
- 値の検証

```php
class CentipedeSimulationResultFactory extends AbstractDTOFactory
{
    public function create(): CentipedeSimulationResult
    {
        // デフォルト値でDTOを作成
    }
    
    public function createWith(array $attributes): CentipedeSimulationResult
    {
        // カスタム値でDTOを作成
    }
}
```

## 5. データ結合処理の分離

現在の `unionCalculateData` メソッドは、複数のシミュレーション結果を結合する責任を持っています。これを別のクラスに分離します：

```php
class CentipedeDataCombiner
{
    private CentipedeFormatter $formatter;
    
    public function __construct(CentipedeFormatter $formatter)
    {
        $this->formatter = $formatter;
    }
    
    public function combine(array $combinationPlayer1, array $patternData): array
    {
        // 現在のunionCalculateDataメソッドの実装
    }
}
```

## 6. eval() の置き換え計画

`eval()` 関数の置き換えは、以下の手順で行います：

1. **数式パーサーライブラリの選定**：
    - [symfony/expression-language](https://github.com/symfony/expression-language)
    - [mossadal/math-parser](https://github.com/mossadal/math-parser)
    - [NXP/math-parser](https://github.com/nxp/math-parser)

2. **FormulaCalculator クラスの実装**：
    - 選定したライブラリを使用して数式を評価
    - 現在使用している関数（pow, ceil, number_formatなど）のサポート

3. **段階的な置き換え**：
    - 単体テストで両方の実装の結果を比較
    - 差異がある場合は原因を特定し修正

## 7. ユニットテスト計画

### 7.1 CentipedeSimulationResult のテスト

- ゲッターメソッドが正しい値を返すことを検証
- イミュータブル性の検証

### 7.2 CentipedeSimulator のテスト

- 基本的な計算が正しく行われることを検証
- エッジケース（大きな数値、小さな数値など）の処理
- 異常系（不正な入力など）の処理

### 7.3 CentipedeFormatter のテスト

- フォーマット結果の検証
- チャートデータ生成の検証

### 7.4 FormulaCalculator のテスト

- 基本的な数式の評価
- 複雑な数式の評価
- 現在の `eval()` との結果比較

### 7.5 CentipedeDataCombiner のテスト

- データ結合の正確性検証
- エッジケースの処理

## 8. リファクタリング実施手順

### フェーズ1: クラス構成の分離

1. DTOクラス（`CentipedeSimulationResult`）の作成
2. ファクトリクラス（`CentipedeSimulationResultFactory`）の作成
3. フォーマッタクラス（`CentipedeFormatter`）の作成
4. シミュレータクラス（`CentipedeSimulator`）の作成（この段階では`eval()`を継続使用）
5. データ結合クラス（`CentipedeDataCombiner`）の作成
6. 元の`Centipede`クラスを新しいクラス構成を使用するように修正

### フェーズ2: ユニットテストの作成

1. 各クラスの基本機能のテスト作成
2. エッジケースのテスト作成
3. 異常系のテスト作成
4. 現在の実装と新しい実装の結果比較テスト

### フェーズ3: eval() の置き換え

1. `FormulaCalculator`クラスの実装
2. 単体テストでの検証
3. `CentipedeSimulator`クラスを`FormulaCalculator`を使用するように修正
4. 統合テストでの検証

## 9. リスク管理

### 9.1 計算結果の変化リスク

- **対策**: 詳細な単体テストと結果比較テストの作成
- **検証方法**: 既存の実装と新しい実装の結果を比較するテストケースの作成

### 9.2 パフォーマンスリスク

- **対策**: パフォーマンステストの実施
- **検証方法**: 処理時間の計測と比較

### 9.3 互換性リスク

- **対策**: 段階的なリファクタリングと継続的な検証
- **検証方法**: 既存の機能テストの実行

## 10. 補足事項と改善提案

### 10.1 型安全性の向上

- PHPの型宣言機能を積極的に活用
- 入力パラメータ用の専用の値オブジェクトの作成

### 10.2 エラー処理の改善

- より具体的な例外クラスの作成
- 入力値の検証強化

### 10.3 ドキュメント化

- PHPDocの充実
- クラス間の関係を示す図の作成

### 10.4 将来の拡張性

- インターフェースの導入
- 依存性注入の活用

## 11. まとめ

このリファクタリング計画は、現在の`Centipede`クラスの問題点を解決し、より保守性の高いコードベースを実現することを目指しています。単一責任の原則に従ったクラス設計、十分なテストカバレッジ、そして安全な実装への移行を段階的に進めることで、リスクを最小限に抑えながらコードの品質を向上させることができます。