<?php

namespace App\CaseConst;

/**
 * Case2の定数
 */
class Case2
{
    public const ODD_NU = 'floor(sqrt(2 * %1$d - 1) / %2$.15f)';
    public const ODD_LEFT_SIDE = 'number_format(sqrt(2 * %1$d), 8)';
    public const ODD_RIGHT_SIDE = 'number_format((%2$d + 1) * %1$.15f, 8)';
    public const EVEN_NU = 'floor(sqrt(2 * %1$d + 2) / %2$.15f)';
    public const EVEN_LEFT_SIDE = 'number_format(sqrt(2 * %1$d + 3), 8)';
    public const EVEN_RIGHT_SIDE = 'number_format((%2$d + 1) * %1$.15f, 8)';

    public function get_delta_value() :float
    {
        return sqrt(300) / 256;
    }
}