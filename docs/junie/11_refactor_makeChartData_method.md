
# 評価と改善提案: CentipedeFormatterのmakeChartDataメソッド

## 現状の評価

`CentipedeFormatter`クラスの`makeChartData`メソッドは、単純なデータフォーマット変換を超えた、ドメイン固有のロジックを含んでいます。具体的には：

1. シミュレーションステップデータ（`CentipedeSimulationStepInterface`）から、特殊なルールに基づいてチャートポイント（`CentipedeChartPoint`）を生成しています。
2. このロジックには、`result`値がtrueの場合に基準点を変更し、それに基づいてY座標を計算するという、ドメイン知識に依存した処理が含まれています。
3. これは単純なデータ変換ではなく、学術的・専門的な意味を持つデータ変換です。

フォーマッタークラスの一般的な責務は、データの表示形式を整えることであり、複雑なドメインロジックを含む変換処理は通常フォーマッターの責務を超えています。

## 改善提案

### 1. 専用のトランスフォーマークラスの作成

```php
namespace App\Calculations\Centipede\Transformer;

use App\Calculations\Centipede\DTO\CentipedeChartPoint;
use App\Calculations\Centipede\DTO\CentipedeSimulationStepInterface;

class CentipedeChartDataTransformer
{
    /**
     * シミュレーションステップデータからチャートデータを生成する
     * @param array<CentipedeSimulationStepInterface|array> $data
     * @return array<CentipedeChartPoint>
     */
    public function transform(array $data): array
    {
        // 現在のmakeChartDataメソッドのロジックをここに移動
    }
}
```

### 2. CentipedeSimulatorクラスの修正

```php
class CentipedeSimulator
{
    private CentipedeFormatter $formatter;
    private CentipedeChartDataTransformer $chartTransformer;

    public function __construct(
        CentipedeFormatter $formatter,
        CentipedeChartDataTransformer $chartTransformer
    ) {
        $this->formatter = $formatter;
        $this->chartTransformer = $chartTransformer;
    }

    public function calculatePattern(/* ... */): CentipedeSimulationResult
    {
        // ...
        
        // フォーマッターではなくトランスフォーマーを使用
        $chartData = $this->chartTransformer->transform($data);
        
        // ...
    }
}
```

### 3. 代替案: ドメインサービスとしての実装

もし変換ロジックがさらに複雑で、ドメインの中核的な部分を表現している場合は、ドメインサービスとして実装することも検討できます：

```php
namespace App\Calculations\Centipede\Service;

class CentipedeChartService
{
    /**
     * シミュレーションステップからチャートデータを生成する
     */
    public function generateChartData(array $simulationSteps): array
    {
        // 現在のmakeChartDataメソッドのロジック
    }
}
```

## 結論

`makeChartData`メソッドは単純なフォーマット変換ではなく、ドメイン固有のロジックを含む変換処理であるため、`CentipedeFormatter`クラスから分離することが望ましいです。この変換ロジックは、専用のトランスフォーマークラスまたはドメインサービスとして実装することで、単一責任の原則に従った設計になり、コードの保守性と再利用性が向上します。

また、この変更により、`CentipedeFormatter`クラスは純粋にデータの表示形式を整える責務に集中できるようになります。