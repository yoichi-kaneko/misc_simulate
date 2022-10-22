<?php
namespace App\Stores;

use App\Models\SimulateTmpResultDetail;

/**
 * Class TmpResultDetail
 * @package App\Stores
 */
class TmpResultDetailStore
{
    private $_tmp_result_detail;
    private $_transition_data = [];

    public function __construct()
    {
        $this->_tmp_result_detail = new SimulateTmpResultDetail();
    }

    public function init($do_truncate)
    {
        if ($do_truncate) {
            $this->_tmp_result_detail->truncate();
        }
        $this->_transition_data = [];
    }

    /**
     * @param int $obtained_cache
     * @param array $transitions
     */
    public function try_save(int $obtained_cache, array $transitions): void
    {
        $this->_transition_data[] = [
            'obtained_cache' => $obtained_cache,
            'transitions' => $this->_tmp_result_detail->compressTransitions($transitions),
            'created_at' => date('Y-m-d H:i:s')
        ];

        // 保存するデータ量が多いため、Insert処理はまとめて行う。
        if (count($this->_transition_data) >= SimulateTmpResultDetail::MAX_MULTI_INSERT) {
            $this->_tmp_result_detail->insert($this->_transition_data);
            $this->_transition_data = [];
        }
    }

    public function __destruct()
    {
        // try_save()で保存されなかった端数分をまとめて保存する
        if (count($this->_transition_data) > 0) {
            $this->_tmp_result_detail->insert($this->_transition_data);
            $this->_transition_data = [];
        }
    }
}
