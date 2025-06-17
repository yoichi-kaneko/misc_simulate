<?php

declare(strict_types=1);

namespace Tests\Unit\Calculations;

use App\Calculations\Centipede\Formatter\CentipedeFormatter;
use PHPUnit\Framework\TestCase;

class CentipedeTest extends TestCase
{
    /**
     * makeCognitiveUnitLatexTextメソッドが正しいLaTeX形式のテキストを返すことをテストします。
     * @test
     * @return void
     */
    public function testMakeCognitiveUnitLatexText()
    {
        // CentipedeFormatterクラスのインスタンスを作成
        $formatter = new CentipedeFormatter();

        // テストケース
        $testCases = [
            // base_numerator, numerator_exp_1, numerator_exp_2, denominator_exp, expected
            [2, 1, 2, 3, '\dfrac{2^{\frac{1}{2}}}{2^{3}}'],
            [3, 2, 3, 4, '\dfrac{3^{\frac{2}{3}}}{2^{4}}'],
            [5, 3, 4, 5, '\dfrac{5^{\frac{3}{4}}}{2^{5}}'],
            [10, 1, 1, 2, '\dfrac{10^{\frac{1}{1}}}{2^{2}}'],
            [1, 5, 5, 0, '\dfrac{1^{\frac{5}{5}}}{2^{0}}'],
        ];

        // 各テストケースを実行
        foreach ($testCases as [$base_numerator, $numerator_exp_1, $numerator_exp_2, $denominator_exp, $expected]) {
            $result = $formatter->makeCognitiveUnitLatexText($base_numerator, $numerator_exp_1, $numerator_exp_2, $denominator_exp);
            $this->assertEquals($expected, $result, "LaTeX形式のテキストが期待通りではありません。");
        }
    }
}
