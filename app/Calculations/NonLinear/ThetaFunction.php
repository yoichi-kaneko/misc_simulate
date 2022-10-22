<?php

namespace App\Calculations\NonLinear;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ThetaFunction
{
    private const FEE_CELL = 'A1';
    private const ZERO_CELL = 'A2';
    private const EXP_CELL = 'A3';
    private const THETA_FEE_CELL = 'B1';
    private const THETA_ZERO_CELL = 'B2';
    private const THETA_EXP_CELL = 'B3';
    private const THETA_AST_CELL = 'B4';

    private const X_STRING = '[x]';
    private const THETA_AST_FORMULA = '=A3 * (B1 - B2) / (B3 - B2)';

    /**
     * @param int $fee
     * @param int $banker_budget_degree
     * @param string $formula
     * @return array
     * @throws \Exception
     */
    public static function run(int $fee, int $banker_budget_degree, string $formula): array
    {
        $objSpreadSheet = new Spreadsheet();
        $objStyleSheet = $objSpreadSheet->getActiveSheet();

        // Feeの値と0の値のセルをセット
        $objStyleSheet->setCellValue(self::FEE_CELL, $fee);
        $objStyleSheet->setCellValue(self::ZERO_CELL, 0);

        // banker_budget_degreeの指数をセット
        $exponential = pow(2, $banker_budget_degree);
        $objStyleSheet->setCellValue(self::EXP_CELL, $exponential);

        $formula = '=' . $formula;

        // 関数として、xにFeeと0を代入した式をセット
        $b1_formula = str_replace(self::X_STRING, self::FEE_CELL, $formula);
        $b2_formula = str_replace(self::X_STRING, self::ZERO_CELL, $formula);
        $b3_formula = str_replace(self::X_STRING, self::EXP_CELL, $formula);
        $objStyleSheet->setCellValue(self::THETA_FEE_CELL, $b1_formula);
        $objStyleSheet->setCellValue(self::THETA_ZERO_CELL, $b2_formula);
        $objStyleSheet->setCellValue(self::THETA_EXP_CELL, $b3_formula);
        $objStyleSheet->setCellValue(self::THETA_AST_CELL, self::THETA_AST_FORMULA);

        $theta_ast = $objStyleSheet->getCell(self::THETA_AST_CELL)->getCalculatedValue();

        if (!is_numeric($theta_ast) || $theta_ast < 0) {
            throw new \Exception(trans('messages.exception.invalid_theta_function_result'));
        }

        return [
            'fee' => $objStyleSheet->getCell(self::FEE_CELL)->getCalculatedValue(),
            'zero' => $objStyleSheet->getCell(self::ZERO_CELL)->getCalculatedValue(),
            'exp' => $objStyleSheet->getCell(self::EXP_CELL)->getCalculatedValue(),
            'theta_fee' => $objStyleSheet->getCell(self::THETA_FEE_CELL)->getCalculatedValue(),
            'theta_zero' => $objStyleSheet->getCell(self::THETA_ZERO_CELL)->getCalculatedValue(),
            'theta_exp' => $objStyleSheet->getCell(self::THETA_EXP_CELL)->getCalculatedValue(),
            'theta_ast' => $theta_ast,
            'rounded_theta_ast' => (int) round($theta_ast),
        ];
    }
}
