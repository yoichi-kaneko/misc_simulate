<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SimulateTmpResultDetail
 *
 * @property int $id
 * @property int $type シミュレーション種別
 * @property string $title タイトル
 * @property string $params パラメータ
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SimulateTmpResultDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SimulateTmpResultDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SimulateTmpResultDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SimulateTmpResultDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SimulateTmpResultDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SimulateTmpResultDetail whereObtainedCache($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SimulateTmpResultDetail whereTransitions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SimulateTmpResultDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SimulatePresetParam extends Model
{
    protected $fillable = [
        'type',
        'title',
        'params',
    ];

    public const TYPE_NONLINEAR_COINTOSSING = 2; // 非線形のコイントス
}
