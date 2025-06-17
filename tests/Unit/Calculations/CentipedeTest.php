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
     * @dataProvider makeCognitiveUnitLatexTextDataProvider
     * @return void
     */
    public function testMakeCognitiveUnitLatexText(
        int $base_numerator,
        int $numerator_exp_1,
        int $numerator_exp_2,
        int $denominator_exp,
        string $expected
    ) {
        // CentipedeFormatterクラスのインスタンスを作成
        $formatter = new CentipedeFormatter();

        $result = $formatter->makeCognitiveUnitLatexText(
            $base_numerator,
            $numerator_exp_1,
            $numerator_exp_2,
            $denominator_exp
        );
        $this->assertEquals($expected, $result, "LaTeX形式のテキストが期待通りではありません。");
    }

    /**
     * makeCognitiveUnitLatexTextの検証用データセット
     * @return array<string, mixed>
     */
    public static function makeCognitiveUnitLatexTextDataProvider(): array
    {
        return [
            'ケース1' => [
                'base_numerator' => 2,
                'numerator_exp_1' => 1,
                'numerator_exp_2' => 2,
                'denominator_exp' => 3,
                'expected' => '\dfrac{2^{\frac{1}{2}}}{2^{3}}',
            ],
            'ケース2' => [
                'base_numerator' => 3,
                'numerator_exp_1' => 2,
                'numerator_exp_2' => 3,
                'denominator_exp' => 4,
                'expected' => '\dfrac{3^{\frac{2}{3}}}{2^{4}}',
            ],
            'ケース3' => [
                'base_numerator' => 5,
                'numerator_exp_1' => 3,
                'numerator_exp_2' => 4,
                'denominator_exp' => 5,
                'expected' => '\dfrac{5^{\frac{3}{4}}}{2^{5}}',
            ],
            'ケース4' => [
                'base_numerator' => 10,
                'numerator_exp_1' => 1,
                'numerator_exp_2' => 1,
                'denominator_exp' => 2,
                'expected' => '\dfrac{10^{\frac{1}{1}}}{2^{2}}',
            ],
            'ケース5' => [
                'base_numerator' => 1,
                'numerator_exp_1' => 5,
                'numerator_exp_2' => 5,
                'denominator_exp' => 0,
                'expected' => '\dfrac{1^{\frac{5}{5}}}{2^{0}}',
            ],
        ];
    }
}
