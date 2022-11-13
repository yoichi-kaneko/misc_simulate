<?php

namespace App\CaseConst;

/**
 * Case1の定数
 */
class Case1
{
    public const ODD_NU = 'floor((2 * %1$d - 1) / %2$.15f)';
    public const ODD_LEFT_SIDE = '2 * %1$d';
    public const ODD_RIGHT_SIDE = '(%2$d + 1) * %1$.15f';
    public const EVEN_NU = 'floor((2 * %1$d + 2) / %2$.15f)';
    public const EVEN_LEFT_SIDE = '2 * %1$d + 3';
    public const EVEN_RIGHT_SIDE = '(%2$d + 1) * %1$.15f';

    public function get_delta_value() :float
    {
        return 300 / 256;
    }
}