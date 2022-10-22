<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SimulateTmpResultDetail
 *
 * @property int $id
 * @property int $obtained_cache ゲーム終了時の収支金額
 * @property string $transitions ゲームの金額の推移
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
class SimulateTmpResultDetail extends Model
{
    protected $fillable = [
        'obtained_cache',
        'transitions',
    ];

    public const MAX_MULTI_INSERT = 2000; // 一度にまとめて保存するレコード件数

    public function compressTransitions(array $transitions)
    {
        return gzcompress(json_encode($transitions), 5);
    }

    public function getTransitions()
    {
        return gzuncompress($this->transitions);
    }
}
