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

`CentipedeDataCombiner::combine()`メソッドを修正して、フォーマット済みデータではなく生のシミュレーション結果を受け取るようにします。また、LaTeX文字列の生成はフォーマッタに任せるため、必要なパラメータを保存するようにします：

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

        // 結合結果を保存（LaTeX文字列生成に必要なパラメータも含める）
        $combinedResults[$combinationPlayerKey] = [
            'steps' => $combinedSteps,
            'cognitive_unit_value_1' => $result1->getCognitiveUnitValue(),
            'cognitive_unit_value_2' => $result2->getCognitiveUnitValue(),
            'base_numerator_1' => $result1->getBaseNumerator(),
            'numerator_exp1_1' => $result1->getNumeratorExp1(),
            'numerator_exp2_1' => $result1->getNumeratorExp2(),
            'denominator_exp_1' => $result1->getDenominatorExp(),
            'base_numerator_2' => $result2->getBaseNumerator(),
            'numerator_exp1_2' => $result2->getNumeratorExp1(),
            'numerator_exp2_2' => $result2->getNumeratorExp2(),
            'denominator_exp_2' => $result2->getDenominatorExp(),
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

`CentipedeFormatter`クラスの`format()`メソッドを修正して、LaTeX文字列の生成を行うようにし、また新しいメソッドを追加して結合されたデータをフォーマットする機能を提供します：

```php
/**
 * シミュレーション結果をフロントエンド用に整形する
 * @param CentipedeSimulationResultInterface $result
 * @return array
 */
public function format(CentipedeSimulationResultInterface $result): array
{
    $data = array_map(function ($step) {
        return is_array($step) ? $step : $step->toArray();
    }, $result->getData());

    // LaTeX文字列を生成
    $cognitiveUnitLatexText = $this->makeCognitiveUnitLatexText(
        $result->getBaseNumerator(),
        $result->getNumeratorExp1(),
        $result->getNumeratorExp2(),
        $result->getDenominatorExp()
    );

    // チャートデータを生成（既存のデータがあれば使用、なければ生成）
    $chartData = $result->getChartData();
    if (empty($chartData)) {
        $chartData = $this->makeChartData($result->getData());
    }

    // 逆因果性の平均値を計算（既存の値があれば使用、なければ計算）
    $averageOfReversedCausality = $result->getAverageOfReversedCausality();
    if ($averageOfReversedCausality === 0.0 && !empty($chartData)) {
        $averageOfReversedCausality = (array_sum(array_column($chartData, 'y')) / count($chartData));
    }

    return [
        'cognitive_unit_latex_text' => $cognitiveUnitLatexText,
        'cognitive_unit_value' => $result->getCognitiveUnitValue(),
        'average_of_reversed_causality' => $averageOfReversedCausality,
        'data' => $data,
        'chart_data' => $chartData,
    ];
}

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

        // LaTeX文字列を生成
        $cognitiveUnitLatexText1 = $this->makeCognitiveUnitLatexText(
            $combinedResult['base_numerator_1'],
            $combinedResult['numerator_exp1_1'],
            $combinedResult['numerator_exp2_1'],
            $combinedResult['denominator_exp_1']
        );

        $cognitiveUnitLatexText2 = $this->makeCognitiveUnitLatexText(
            $combinedResult['base_numerator_2'],
            $combinedResult['numerator_exp1_2'],
            $combinedResult['numerator_exp2_2'],
            $combinedResult['denominator_exp_2']
        );

        $formattedResults[$key] = [
            'data' => array_map(function($step) {
                return $step->toArray();
            }, $combinedResult['steps']),
            'chart_data' => $chartData,
            'cognitive_unit_latex_text_1' => $cognitiveUnitLatexText1,
            'cognitive_unit_latex_text_2' => $cognitiveUnitLatexText2,
            'cognitive_unit_value_1' => $combinedResult['cognitive_unit_value_1'],
            'cognitive_unit_value_2' => $combinedResult['cognitive_unit_value_2'],
            'average_of_reversed_causality' => $averageOfReversedCausality,
        ];
    }

    return $formattedResults;
}
```

### 4. CentipedeSimulatorの改修

`CentipedeSimulator::calculatePattern()`メソッドでは、チャートデータの生成とLaTeX文字列の生成を`CentipedeSimulationResult`オブジェクトの作成時に行うのではなく、`CentipedeFormatter::format()`メソッドに任せるようにします：

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

    // LaTeX文字列の生成とチャートデータの生成はformat()メソッドに任せる
    return new CentipedeSimulationResult(
        $cognitiveUnitValue,
        $baseNumerator,
        $numeratorExp1,
        $numeratorExp2,
        $denominatorExp,
        0.0, // 平均値は後でformat()メソッドで計算
        $data,
        [] // 空の配列を渡し、format()メソッドでチャートデータを生成
    );
}
```

### 5. CentipedeSimulationResultの改修

`CentipedeSimulationResult`クラスを修正して、LaTeX文字列を保持する代わりに、LaTeX文字列の生成に必要なパラメータを保持するようにします：

```php
final class CentipedeSimulationResult implements CentipedeSimulationResultInterface
{
    use ArrayTypeCheckTrait;
    private readonly float $cognitiveUnitValue;
    private readonly int $baseNumerator;
    private readonly int $numeratorExp1;
    private readonly int $numeratorExp2;
    private readonly int $denominatorExp;
    private readonly float $averageOfReversedCausality;
    /** @var array<CentipedeSimulationStepInterface> */
    private readonly array $data;
    private readonly array $chartData;

    /**
     * @param float $cognitiveUnitValue Cognitive Unitの値
     * @param int $baseNumerator 基数
     * @param int $numeratorExp1 分子の指数1
     * @param int $numeratorExp2 分子の指数2
     * @param int $denominatorExp 分母の指数
     * @param float $averageOfReversedCausality 逆因果性の平均値
     * @param array<CentipedeSimulationStepInterface> $data シミュレーション結果データ
     * @param array $chartData チャート用データ
     */
    public function __construct(
        float $cognitiveUnitValue,
        int $baseNumerator,
        int $numeratorExp1,
        int $numeratorExp2,
        int $denominatorExp,
        float $averageOfReversedCausality,
        array $data,
        array $chartData
    ) {
        $this->assertArrayOfType($data, CentipedeSimulationStepInterface::class, 'data');

        $this->cognitiveUnitValue = $cognitiveUnitValue;
        $this->baseNumerator = $baseNumerator;
        $this->numeratorExp1 = $numeratorExp1;
        $this->numeratorExp2 = $numeratorExp2;
        $this->denominatorExp = $denominatorExp;
        $this->averageOfReversedCausality = $averageOfReversedCausality;
        $this->data = $data;
        $this->chartData = $chartData;
    }

    // 既存のゲッターメソッドに加えて、新しいパラメータのゲッターメソッドを追加

    /**
     * 基数を取得する
     * @return int
     */
    public function getBaseNumerator(): int
    {
        return $this->baseNumerator;
    }

    /**
     * 分子の指数1を取得する
     * @return int
     */
    public function getNumeratorExp1(): int
    {
        return $this->numeratorExp1;
    }

    /**
     * 分子の指数2を取得する
     * @return int
     */
    public function getNumeratorExp2(): int
    {
        return $this->numeratorExp2;
    }

    /**
     * 分母の指数を取得する
     * @return int
     */
    public function getDenominatorExp(): int
    {
        return $this->denominatorExp;
    }

    // 既存のゲッターメソッドは変更なし
}
```

また、`CentipedeSimulationResultInterface`インターフェースも同様に更新する必要があります：

```php
interface CentipedeSimulationResultInterface
{
    public function getCognitiveUnitValue(): float;
    public function getBaseNumerator(): int;
    public function getNumeratorExp1(): int;
    public function getNumeratorExp2(): int;
    public function getDenominatorExp(): int;
    public function getAverageOfReversedCausality(): float;
    public function getData(): array;
    public function getChartData(): array;
}
```

## まとめ

この改善により、以下の利点が得られます：

1. **責任の明確化**: 各クラスの責任が明確になり、データの流れが整理されます
   - `CentipedeSimulator`: 数値計算とシミュレーションのみを担当
   - `CentipedeFormatter`: データの表示形式への変換（LaTeX文字列の生成を含む）を担当
   - `CentipedeDataCombiner`: シミュレーション結果の結合のみを担当
2. **一貫性**: `makeChartData()`メソッドは常に同じ形式の入力（`CentipedeSimulationStepInterface`の配列）を受け取ります
3. **処理順序の最適化**: 生データを先に結合してから、最後にフォーマットするという自然な流れになります
4. **拡張性**: 新しいフォーマット要件が追加された場合も、フォーマッタクラスの修正だけで対応できます
5. **単一責任の原則（SRP）の遵守**: 各クラスが単一の責任を持ち、変更理由も単一になります

これらの変更により、コードの保守性と可読性が向上し、将来の機能追加や変更にも柔軟に対応できるようになります。
