# Centipedeクラスの構造的課題の解決策

現在の実装における主な課題は以下の通りです：

1. `CentipedeFormatter::makeChartData()`が異なる場所で異なる入力形式で呼び出されている
2. フォーマット処理と結合処理の順序が最適でない

## 解決策

### 1. データの流れを整理する

現在の処理フローを改善するために、以下のような変更を提案します：

```php
public function run(
    array $patterns,
    int $max_step,
    ?int $max_rc,
    ?array $combination_player_1
): array {
    // 1. まず生のシミュレーション結果を取得する（フォーマットなし）
    $simulation_results = [];
    foreach ($patterns as $key => $pattern_val) {
        $simulation_results[$key] = $this->simulator->calculatePattern(
            (int) $pattern_val['base_numerator'],
            (int) $pattern_val['numerator_exp_1'],
            (int) $pattern_val['numerator_exp_2'],
            (int) $pattern_val['denominator_exp'],
            $max_step
        );
    }

    // 2. 必要に応じて結合処理を行う（生データを使用）
    $combined_result = null;
    if (!is_null($combination_player_1)) {
        $combined_result = $this->dataCombiner->combine($combination_player_1, $simulation_results);
    }

    // 3. 最後にフォーマット処理を行う
    $pattern_data = [];
    foreach ($simulation_results as $key => $result) {
        $pattern_data[$key] = $this->formatter->format($result);
    }

    // 結合データがある場合はフォーマット
    $combination_data = null;
    if ($combined_result !== null) {
        $combination_data = $this->formatter->formatCombinedResult($combined_result);
    }

    return [
        'result' => 'ok',
        'render_params' => [
            'max_step' => $max_step,
            'max_rc' => $max_rc,
        ],
        'pattern_data' => $pattern_data,
        'combination_data' => $combination_data,
    ];
}
```

### 2. CentipedeDataCombinerの改修

`CentipedeDataCombiner::combine()`メソッドを修正して、フォーマット済みデータではなく生のシミュレーション結果を受け取るようにします：

```php
/**
 * 2つのシミュレート結果を合算する
 * @param array $combinationPlayer1
 * @param array $simulationResults 各キーにCentipedeSimulationResultInterfaceのインスタンスを含む配列
 * @return array 結合されたシミュレーション結果の配列
 */
public function combine(array $combinationPlayer1, array $simulationResults): array
{
    $combinedResults = [];

    foreach ($combinationPlayer1 as $combinationPlayerKey => $combinationPlayerVal) {
        $result1 = $simulationResults[$combinationPlayerKey . '_1'];
        $result2 = $simulationResults[$combinationPlayerKey . '_2'];

        // 生のシミュレーションステップデータを取得
        $steps1 = $result1->getData();
        $steps2 = $result2->getData();

        // データを結合
        $combinedSteps = $this->combineSteps($steps1, $steps2, $combinationPlayerVal === '1');
        
        // 結合結果を保存
        $combinedResults[$combinationPlayerKey] = [
            'steps' => $combinedSteps,
            'cognitive_unit_value_1' => $result1->getCognitiveUnitValue(),
            'cognitive_unit_value_2' => $result2->getCognitiveUnitValue(),
            'cognitive_unit_latex_text_1' => $result1->getCognitiveUnitLatexText(),
            'cognitive_unit_latex_text_2' => $result2->getCognitiveUnitLatexText(),
        ];
    }

    return $combinedResults;
}

/**
 * 2つのステップ配列を結合する
 * @param array $steps1
 * @param array $steps2
 * @param bool $player1Is1
 * @return array
 */
private function combineSteps(array $steps1, array $steps2, bool $player1Is1): array
{
    // 結合ロジック...
}
```

### 3. CentipedeFormatterの改修

`CentipedeFormatter`クラスに新しいメソッドを追加して、結合されたデータをフォーマットする機能を提供します：

```php
/**
 * 結合されたシミュレーション結果をフォーマットする
 * @param array $combinedResults
 * @return array
 */
public function formatCombinedResult(array $combinedResults): array
{
    $formattedResults = [];
    
    foreach ($combinedResults as $key => $combinedResult) {
        $chartData = $this->makeChartData($combinedResult['steps']);
        $averageOfReversedCausality = (array_sum(array_column($chartData, 'y')) / count($chartData));
        
        $formattedResults[$key] = [
            'data' => array_map(function($step) {
                return $step->toArray();
            }, $combinedResult['steps']),
            'chart_data' => $chartData,
            'cognitive_unit_latex_text_1' => $combinedResult['cognitive_unit_latex_text_1'],
            'cognitive_unit_latex_text_2' => $combinedResult['cognitive_unit_latex_text_2'],
            'cognitive_unit_value_1' => $combinedResult['cognitive_unit_value_1'],
            'cognitive_unit_value_2' => $combinedResult['cognitive_unit_value_2'],
            'average_of_reversed_causality' => $averageOfReversedCausality,
        ];
    }
    
    return $formattedResults;
}
```

### 4. CentipedeSimulatorの改修

`CentipedeSimulator::calculatePattern()`メソッドでは、チャートデータの生成を`CentipedeSimulationResult`オブジェクトの作成時に行うのではなく、`CentipedeFormatter::format()`メソッドに任せるようにします：

```php
public function calculatePattern(
    int $baseNumerator,
    int $numeratorExp1,
    int $numeratorExp2,
    int $denominatorExp,
    int $maxStep
): CentipedeSimulationResult {
    $data = [];
    $cognitiveUnitValue = $this->calcCognitiveUnitValue(
        $baseNumerator,
        $numeratorExp1,
        $numeratorExp2,
        $denominatorExp
    );

    // シミュレーションステップを生成
    for ($i = 1; $i <= $maxStep; $i++) {
        // 計算ロジック...
        $data[] = new CentipedeSimulationStep(
            $i,
            (int)$maxNuValue,
            $leftSideValue,
            $rightSideValue,
            ($leftSideValue < $rightSideValue)
        );
    }

    $cognitiveUnitLatexText = $this->formatter->makeCognitiveUnitLatexText(
        $baseNumerator,
        $numeratorExp1,
        $numeratorExp2,
        $denominatorExp
    );

    // チャートデータの生成はformat()メソッドに任せる
    return new CentipedeSimulationResult(
        $cognitiveUnitValue,
        $cognitiveUnitLatexText,
        0.0, // 平均値は後でformat()メソッドで計算
        $data,
        [] // 空の配列を渡し、format()メソッドでチャートデータを生成
    );
}
```

## まとめ

この改善により、以下の利点が得られます：

1. **責任の明確化**: 各クラスの責任が明確になり、データの流れが整理されます
2. **一貫性**: `makeChartData()`メソッドは常に同じ形式の入力（`CentipedeSimulationStepInterface`の配列）を受け取ります
3. **処理順序の最適化**: 生データを先に結合してから、最後にフォーマットするという自然な流れになります
4. **拡張性**: 新しいフォーマット要件が追加された場合も、フォーマッタクラスの修正だけで対応できます

これらの変更により、コードの保守性と可読性が向上し、将来の機能追加や変更にも柔軟に対応できるようになります。