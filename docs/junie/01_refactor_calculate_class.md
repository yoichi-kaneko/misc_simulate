# Centipedeクラスの責任分離アプローチ

現在のCentipedeクラスは、学術的シミュレーション（計算処理）とフロントエンド用のフォーマット整形（表示処理）の両方を担当しており、単一責任の原則に反しています。以下に、このクラスを分解するためのアプローチを提案します。

## 分解アプローチ

### 1. ドメイン層とプレゼンテーション層の分離

現在のCentipedeクラスを以下の2つの主要なクラスに分割します：

1. **CentipedeSimulator** - 計算処理（ドメイン層）
2. **CentipedeFormatter** - 表示処理（プレゼンテーション層）

### 2. データ転送オブジェクト（DTO）の導入

計算結果を表示層に渡すための専用のDTOクラスを作成します：
- **CentipedeSimulationResult** - シミュレーション結果を保持するDTO

## 具体的なクラス構造

### 名前空間の提案

```
App\
  ├── Calculations\
  │   ├── Centipede\
  │   │   ├── Simulator\
  │   │   │   └── CentipedeSimulator.php  // 計算処理のみを担当
  │   │   ├── Formatter\
  │   │   │   └── CentipedeFormatter.php  // 表示処理のみを担当
  │   │   └── DTO\
  │   │       ├── CentipedeSimulationResult.php  // シミュレーション結果DTO
  │   │       └── CentipedePatternResult.php     // 各パターンの結果DTO
  │   └── ...
  └── ...
```

### 各クラスの責任

#### 1. CentipedeSimulator

```php
namespace App\Calculations\Centipede\Simulator;

use App\Calculations\Centipede\DTO\CentipedeSimulationResult;
use App\Calculations\Centipede\DTO\CentipedePatternResult;

class CentipedeSimulator
{
    // 定数定義（計算式）
    private const ODD_NU = '...';
    private const EVEN_NU = '...';
    // ...

    /**
     * シミュレーションを実行する
     * @return CentipedeSimulationResult
     */
    public function run(array $patterns, int $max_step, ?array $combination_player_1): CentipedeSimulationResult
    {
        // 計算処理のみを行い、結果をDTOに格納して返す
    }

    /**
     * パターン計算を実行する
     */
    private function calculatePattern(...): CentipedePatternResult
    {
        // ...
    }

    /**
     * 計算式を評価する
     */
    private function evalFormula(string $str)
    {
        // ...
    }

    /**
     * 認知単位の値を計算する
     */
    private function calcCognitiveUnitValue(...): float
    {
        // ...
    }

    /**
     * 2つのシミュレーション結果を合算する
     */
    private function unionCalculateData(...): array
    {
        // ...
    }
}
```

#### 2. CentipedeFormatter

```php
namespace App\Calculations\Centipede\Formatter;

use App\Calculations\Centipede\DTO\CentipedeSimulationResult;

class CentipedeFormatter
{
    /**
     * シミュレーション結果をフロントエンド用に整形する
     */
    public function format(CentipedeSimulationResult $result, ?int $max_rc): array
    {
        // フロントエンド用のレスポンス形式に整形
        return [
            'result' => 'ok',
            'render_params' => [
                'max_step' => $result->getMaxStep(),
                'max_rc' => $max_rc,
            ],
            'pattern_data' => $this->formatPatternData($result->getPatternData()),
            'combination_data' => $this->formatCombinationData($result->getCombinationData()),
        ];
    }

    /**
     * 認知単位のLaTeX形式テキストを生成する
     */
    public function makeCognitiveUnitLatexText(...): string
    {
        // ...
    }

    /**
     * チャート表示用データを生成する
     */
    public function makeChartData(array $data): array
    {
        // ...
    }

    // その他の表示用フォーマットメソッド
}
```

#### 3. DTOクラス

```php
namespace App\Calculations\Centipede\DTO;

class CentipedeSimulationResult
{
    private array $patternData;
    private ?array $combinationData;
    private int $maxStep;

    // コンストラクタ、ゲッター等
}

class CentipedePatternResult
{
    private float $cognitiveUnitValue;
    private array $simulationData;
    // その他の必要なプロパティ

    // コンストラクタ、ゲッター等
}
```

## 使用例

```php
// コントローラーなどで使用する例
$simulator = new CentipedeSimulator();
$formatter = new CentipedeFormatter();

// シミュレーション実行
$simulationResult = $simulator->run($patterns, $max_step, $combination_player_1);

// フロントエンド用にフォーマット
$response = $formatter->format($simulationResult, $max_rc);

return response()->json($response);
```

## 利点

1. **単一責任の原則に準拠**: 各クラスが明確に定義された単一の責任を持つ
2. **テスト容易性の向上**: 計算ロジックと表示ロジックを個別にテスト可能
3. **コードの再利用性**: 表示フォーマットを変更せずに計算ロジックを再利用可能
4. **保守性の向上**: 一方のロジックを変更しても他方に影響を与えない
5. **関心の分離**: ドメインロジック（計算）とプレゼンテーションロジック（表示）の明確な分離

この設計により、将来的な拡張や変更にも柔軟に対応できるようになります。