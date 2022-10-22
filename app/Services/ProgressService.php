<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Request;

/**
 * Class ProgressService
 * 進行度を保存するクラス
 * @package App\Services
 */
class ProgressService
{
    // キャッシュの有効期間。単位は秒
    private static $EXPIRE_TIME = 600;
    // キャッシュのプレフィックス
    private static $CACHE_PREFIX = 'progress_rate_';

    // 保存するインターバル期間。単位はミリ秒
    private $_interval_msec = 1000;
    private $_token;
    private $_stopwatch;

    /**
     * ProgressService constructor.
     * @param string $token 保存するキー名
     * @param int $interval_msec 保存するインターバル期間。単位はミリ秒
     */
    public function __construct(string $token = '', int $interval_msec = 0)
    {
        if ($interval_msec > 0) {
            $this->_interval_msec = $interval_msec;
        }
        if (empty($token)) {
            abort(500);
        }
        $this->_stopwatch = new StopwatchService($this->_interval_msec);
        $this->_token = self::$CACHE_PREFIX . $token;
    }

    /**
     * 進捗度を初期化する
     * @return void
     */
    public function init() :void
    {
        $data = json_encode([
            'status' => 'ready',
            'progress_rate' => 0
        ]);
        Cache::put($this->_token, $data, self::$EXPIRE_TIME);
    }

    /**
     * 進捗度をセットする
     * @param int $progress_rate
     * @param int $iteration
     * @param int $progress_count
     * @param bool $force
     * @return void
     */
    public function set(int $progress_rate, int $iteration, int $progress_count, bool $force = false) :void
    {
        // 進捗度をセットするのは、インターバル期間を過ぎた場合のみ。
        if ($this->_stopwatch->isExceeded() || $force) {
            $data = json_encode([
                'status' => 'running',
                'progress_rate' => $progress_rate,
                'iteration' => $iteration,
                'progress_count' => $progress_count
            ]);
            Cache::put($this->_token, $data, self::$EXPIRE_TIME);
            $this->_stopwatch->reset();
        }
    }

    /**
     * 失敗をセットする
     * @return void
     */
    public function setFailed() :void
    {
        $data = json_encode([
            'status' => 'failed',
            'progress_rate' => 0
        ]);
        Cache::put($this->_token, $data, self::$EXPIRE_TIME);
    }

    public function setComplete(array $result_data, string $function) :void
    {
        $data = json_encode([
            'status' => 'complete',
            'function' => $function,
            'progress_rate' => 100,
            'result' => $result_data
        ]);
        Cache::put($this->_token, $data, self::$EXPIRE_TIME);
    }

    /**
     * 進捗度を取得する
     * @return string
     */
    public function get() :string
    {
        return Cache::get($this->_token) ?: json_encode([
            'progress_rate' => 0
        ]);
    }
}
