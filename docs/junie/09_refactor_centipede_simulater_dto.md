# Centipedeシミュレータのarray型PHPDoc改善とオブジェクト化提案

## 1. arrayとのみ宣言されている箇所のリストアップ

app/Calculations/Centipede/* 配下で、PHPDocにarrayとのみ宣言されている箇所を以下にリストアップします。

### CentipedeDataCombiner.php
```php
/**
 * 2つのシミュレート結果を合算する
 * @param array $combinationPlayer1
 * @param array $patternData
 * @return array
 */
public function combine(array $combinationPlayer1, array $patternData): array
```

### CentipedeFormatter.php
```php
/**
 * シミュレーション結果をフロントエンド用に整形する
 * @param CentipedeSimulationResultInterface $result
 * @return array
 */
public function format(CentipedeSimulationResultInterface $result): array

/**
 * チャート用のデータを生成する
 * @param array $data
 * @return array
 */
public function makeChartData(array $data): array
```

### CentipedeSimulationResult.php
```php
/**
 * @param float $cognitiveUnitValue Cognitive Unitの値
 * @param string $cognitiveUnitLatexText Cognitive UnitのLatex形式のテキスト
 * @param float $averageOfReversedCausality 逆因果性の平均値
 * @param array $data シミュレーション結果データ
 * @param array $chartData チャート用データ
 */
public function __construct(...)

/**
 * シミュレーション結果データを取得する
 * @return array
 */
public function getData(): array

/**
 * チャート用データを取得する
 * @return array
 */
public function getChartData(): array
```

### CentipedeSimulationResultInterface.php
```php
/**
 * シミュレーション結果データを取得する
 * @return array
 */
public function getData(): array;

/**
 * チャート用データを取得する
 * @return array
 */
public function getChartData(): array;
```

## 2. オブジェクト化の提案

### 2.1 データ構造の分析

コードを分析した結果、以下の配列構造が特定できました：

1. **シミュレーション結果データ (`$data`)**:
   ```php
   [
       [
           't' => int,                  // ステップ数
           'max_nu_value' => int,       // 最大nu値
           'left_side_value' => string, // 左辺値
           'right_side_value' => string, // 右辺値
           'result' => bool,            // 結果
       ],
       // ...
   ]
   ```

2. **チャートデータ (`$chartData`)**:
   ```php
   [
       [
           'x' => int, // X座標
           'y' => int, // Y座標
       ],
       // ...
   ]
   ```

3. **フォーマット結果**:
   ```php
   [
       'cognitive_unit_latex_text' => string,
       'cognitive_unit_value' => float,
       'average_of_reversed_causality' => float,
       'data' => array,
       'chart_data' => array,
   ]
   ```

4. **CentipedeDataCombiner::combine()の結果**:
   ```php
   [
       'key' => [
           'data' => array,
           'chart_data' => array,
           'cognitive_unit_latex_text_1' => string,
           'cognitive_unit_latex_text_2' => string,
           'cognitive_unit_value_1' => float,
           'cognitive_unit_value_2' => float,
           'average_of_reversed_causality' => float,
       ],
       // ...
   ]
   ```

### 2.2 オブジェクト化の提案

以下のDTOクラスを新たに作成することを提案します：

#### 1. CentipedeSimulationStep

```php
<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * Centipedeシミュレーションの各ステップの結果を表すDTO
 */
final class CentipedeSimulationStep implements CentipedeSimulationStepInterface
{
    private readonly int $t;
    private readonly int $maxNuValue;
    private readonly string $leftSideValue;
    private readonly string $rightSideValue;
    private readonly bool $result;

    /**
     * @param int $t ステップ数
     * @param int $maxNuValue 最大nu値
     * @param string $leftSideValue 左辺値
     * @param string $rightSideValue 右辺値
     * @param bool $result 結果
     */
    public function __construct(
        int $t,
        int $maxNuValue,
        string $leftSideValue,
        string $rightSideValue,
        bool $result
    ) {
        $this->t = $t;
        $this->maxNuValue = $maxNuValue;
        $this->leftSideValue = $leftSideValue;
        $this->rightSideValue = $rightSideValue;
        $this->result = $result;
    }

    /**
     * ステップ数を取得する
     * @return int
     */
    public function getT(): int
    {
        return $this->t;
    }

    /**
     * 最大nu値を取得する
     * @return int
     */
    public function getMaxNuValue(): int
    {
        return $this->maxNuValue;
    }

    /**
     * 左辺値を取得する
     * @return string
     */
    public function getLeftSideValue(): string
    {
        return $this->leftSideValue;
    }

    /**
     * 右辺値を取得する
     * @return string
     */
    public function getRightSideValue(): string
    {
        return $this->rightSideValue;
    }

    /**
     * 結果を取得する
     * @return bool
     */
    public function getResult(): bool
    {
        return $this->result;
    }

    /**
     * 配列に変換する
     * @return array{t: int, max_nu_value: int, left_side_value: string, right_side_value: string, result: bool}
     */
    public function toArray(): array
    {
        return [
            't' => $this->t,
            'max_nu_value' => $this->maxNuValue,
            'left_side_value' => $this->leftSideValue,
            'right_side_value' => $this->rightSideValue,
            'result' => $this->result,
        ];
    }
}
```

#### 2. CentipedeChartPoint

```php
<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * Centipedeシミュレーションのチャートポイントを表すDTO
 */
final class CentipedeChartPoint implements CentipedeChartPointInterface
{
    private readonly int $x;
    private readonly int $y;

    /**
     * @param int $x X座標
     * @param int $y Y座標
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * X座標を取得する
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * Y座標を取得する
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * 配列に変換する
     * @return array{x: int, y: int}
     */
    public function toArray(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
        ];
    }
}
```

#### 3. CentipedeFormattedResult

```php
<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * Centipedeシミュレーションのフォーマット済み結果を表すDTO
 */
final class CentipedeFormattedResult implements CentipedeFormattedResultInterface
{
    private readonly string $cognitiveUnitLatexText;
    private readonly float $cognitiveUnitValue;
    private readonly float $averageOfReversedCausality;
    private readonly array $data; // CentipedeSimulationStep[]
    private readonly array $chartData; // CentipedeChartPoint[]

    /**
     * @param string $cognitiveUnitLatexText Cognitive UnitのLatex形式のテキスト
     * @param float $cognitiveUnitValue Cognitive Unitの値
     * @param float $averageOfReversedCausality 逆因果性の平均値
     * @param array<CentipedeSimulationStepInterface> $data シミュレーション結果データ
     * @param array<CentipedeChartPointInterface> $chartData チャート用データ
     */
    public function __construct(
        string $cognitiveUnitLatexText,
        float $cognitiveUnitValue,
        float $averageOfReversedCausality,
        array $data,
        array $chartData
    ) {
        $this->cognitiveUnitLatexText = $cognitiveUnitLatexText;
        $this->cognitiveUnitValue = $cognitiveUnitValue;
        $this->averageOfReversedCausality = $averageOfReversedCausality;
        $this->data = $data;
        $this->chartData = $chartData;
    }

    // ゲッターメソッド...

    /**
     * 配列に変換する
     * @return array{
     *     cognitive_unit_latex_text: string,
     *     cognitive_unit_value: float,
     *     average_of_reversed_causality: float,
     *     data: array,
     *     chart_data: array
     * }
     */
    public function toArray(): array
    {
        return [
            'cognitive_unit_latex_text' => $this->cognitiveUnitLatexText,
            'cognitive_unit_value' => $this->cognitiveUnitValue,
            'average_of_reversed_causality' => $this->averageOfReversedCausality,
            'data' => array_map(fn($step) => $step->toArray(), $this->data),
            'chart_data' => array_map(fn($point) => $point->toArray(), $this->chartData),
        ];
    }
}
```

#### 4. CentipedeCombinedResult

```php
<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * Centipedeシミュレーションの結合結果を表すDTO
 */
final class CentipedeCombinedResult implements CentipedeCombinedResultInterface
{
    private readonly array $combinationResults; // key => CentipedeCombinationResult

    /**
     * @param array<string, CentipedeCombinationResultInterface> $combinationResults 結合結果
     */
    public function __construct(array $combinationResults)
    {
        $this->combinationResults = $combinationResults;
    }

    /**
     * 結合結果を取得する
     * @return array<string, CentipedeCombinationResultInterface>
     */
    public function getCombinationResults(): array
    {
        return $this->combinationResults;
    }

    /**
     * 配列に変換する
     * @return array<string, array>
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->combinationResults as $key => $combinationResult) {
            $result[$key] = $combinationResult->toArray();
        }
        return $result;
    }
}
```

#### 5. CentipedeCombinationResult

```php
<?php

declare(strict_types=1);

namespace App\Calculations\Centipede\DTO;

/**
 * Centipedeシミュレーションの組み合わせ結果を表すDTO
 */
final class CentipedeCombinationResult implements CentipedeCombinationResultInterface
{
    private readonly array $data; // CentipedeSimulationStep[]
    private readonly array $chartData; // CentipedeChartPoint[]
    private readonly string $cognitiveUnitLatexText1;
    private readonly string $cognitiveUnitLatexText2;
    private readonly float $cognitiveUnitValue1;
    private readonly float $cognitiveUnitValue2;
    private readonly float $averageOfReversedCausality;

    /**
     * @param array<CentipedeSimulationStepInterface> $data シミュレーション結果データ
     * @param array<CentipedeChartPointInterface> $chartData チャート用データ
     * @param string $cognitiveUnitLatexText1 Cognitive UnitのLatex形式のテキスト1
     * @param string $cognitiveUnitLatexText2 Cognitive UnitのLatex形式のテキスト2
     * @param float $cognitiveUnitValue1 Cognitive Unitの値1
     * @param float $cognitiveUnitValue2 Cognitive Unitの値2
     * @param float $averageOfReversedCausality 逆因果性の平均値
     */
    public function __construct(
        array $data,
        array $chartData,
        string $cognitiveUnitLatexText1,
        string $cognitiveUnitLatexText2,
        float $cognitiveUnitValue1,
        float $cognitiveUnitValue2,
        float $averageOfReversedCausality
    ) {
        $this->data = $data;
        $this->chartData = $chartData;
        $this->cognitiveUnitLatexText1 = $cognitiveUnitLatexText1;
        $this->cognitiveUnitLatexText2 = $cognitiveUnitLatexText2;
        $this->cognitiveUnitValue1 = $cognitiveUnitValue1;
        $this->cognitiveUnitValue2 = $cognitiveUnitValue2;
        $this->averageOfReversedCausality = $averageOfReversedCausality;
    }

    // ゲッターメソッド...

    /**
     * 配列に変換する
     * @return array{
     *     data: array,
     *     chart_data: array,
     *     cognitive_unit_latex_text_1: string,
     *     cognitive_unit_latex_text_2: string,
     *     cognitive_unit_value_1: float,
     *     cognitive_unit_value_2: float,
     *     average_of_reversed_causality: float
     * }
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(fn($step) => $step->toArray(), $this->data),
            'chart_data' => array_map(fn($point) => $point->toArray(), $this->chartData),
            'cognitive_unit_latex_text_1' => $this->cognitiveUnitLatexText1,
            'cognitive_unit_latex_text_2' => $this->cognitiveUnitLatexText2,
            'cognitive_unit_value_1' => $this->cognitiveUnitValue1,
            'cognitive_unit_value_2' => $this->cognitiveUnitValue2,
            'average_of_reversed_causality' => $this->averageOfReversedCausality,
        ];
    }
}
```

### 2.3 ディレクトリ構造とクラス名の提案

提案するディレクトリ構造は以下の通りです：

```
app/Calculations/Centipede/
├── DTO/
│   ├── CentipedeSimulationResult.php (既存)
│   ├── CentipedeSimulationResultInterface.php (既存)
│   ├── CentipedeSimulationStep.php (新規)
│   ├── CentipedeSimulationStepInterface.php (新規)
│   ├── CentipedeChartPoint.php (新規)
│   ├── CentipedeChartPointInterface.php (新規)
│   ├── CentipedeFormattedResult.php (新規)
│   ├── CentipedeFormattedResultInterface.php (新規)
│   ├── CentipedeCombinedResult.php (新規)
│   ├── CentipedeCombinedResultInterface.php (新規)
│   ├── CentipedeCombinationResult.php (新規)
│   └── CentipedeCombinationResultInterface.php (新規)
```

### 2.4 既存コードの修正方針

1. **CentipedeSimulationResult**:
    - `$data`プロパティを`CentipedeSimulationStep[]`型に変更
    - `$chartData`プロパティを`CentipedeChartPoint[]`型に変更
    - PHPDocを適切に更新

2. **CentipedeFormatter**:
    - `format()`メソッドの戻り値を`CentipedeFormattedResult`に変更
    - `makeChartData()`メソッドの引数と戻り値を適切なDTO型に変更

3. **CentipedeDataCombiner**:
    - `combine()`メソッドの引数と戻り値を適切なDTO型に変更

4. **CentipedeSimulator**:
    - `calculatePattern()`メソッド内で配列の代わりにDTOオブジェクトを生成

これらの変更により、Centipedeシミュレータで扱うパラメータの厳密性が向上し、型安全性が確保されます。また、各データ構造の意味が明確になり、コードの可読性と保守性も向上します。
