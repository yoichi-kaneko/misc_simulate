<?php
namespace App\Services;

/**
 * Class StopwatchService
 * 呼び出してからの経過時間を測定するクラス
 * @package App\Services
 */
class StopwatchService
{
    private $_msec = 0;
    private $_microtime;

    /**
     * StopwatchService constructor.
     * @param int $msec 測定する経過時間。単位はミリ秒
     */
    public function __construct(int $msec = 0)
    {
        $this->_microtime = microtime(true);
        $this->_msec = $msec;
    }

    /**
     * 測定開始してから、指定の経過時間を過ぎているかの確認
     * @return bool
     */
    public function isExceeded() :bool
    {
        return (microtime(true) - $this->_microtime) * 1000 >= $this->_msec;
    }

    /**
     * 時刻を再セットする。
     */
    public function reset() :void
    {
        $this->_microtime = microtime(true);
    }
}
