<?php

namespace App\Jobs;

use App\Services\ProgressService;
use App\Services\SpGame\MultiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateMulti implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    public $_data;
    public $tries = 1;
    public $timeout = 90;

    /**
     * Create a new job instance.
     * @param array $post_data
     * @return void
     */
    public function __construct(array $post_data)
    {
        set_time_limit(1800);
        $this->_data = $post_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $multi = new MultiService();
        $multi->run(
            $this->_data['banker_prepared_change'],
            $this->_data['participation_fee'],
            $this->_data['participant_number'],
            $this->_data['banker_budget_degree'],
            $this->_data['iteration'],
            $this->_data['initial_setup_cost'],
            $this->_data['facility_unit_cost'],
            $this->_data['facility_unit'],
            $this->_data['random_seed'],
            $this->_data['get_chart_data'],
            $this->_data['save_each_transitions'],
            $this->_data['token'],
            $this->_data['processing_data'] ?? []
        );

        if (! $multi->isCompleted()) {
            CalculateMulti::dispatch(
                [
                    'banker_prepared_change' => $this->_data['banker_prepared_change'],
                    'participation_fee' => $this->_data['participation_fee'],
                    'participant_number' => $this->_data['participant_number'],
                    'banker_budget_degree' => $this->_data['banker_budget_degree'],
                    'iteration' => $this->_data['iteration'],
                    'random_seed' => $this->_data['random_seed'],
                    'initial_setup_cost' => $this->_data['initial_setup_cost'],
                    'facility_unit_cost' => $this->_data['facility_unit_cost'],
                    'facility_unit' => $this->_data['facility_unit'],
                    'get_chart_data' => $this->_data['get_chart_data'],
                    'save_each_transitions' => $this->_data['save_each_transitions'],
                    'token' => $this->_data['token'],
                    'processing_data' => $multi->getProcessingData(),
                ]
            );
        }
    }

    public function failed($exception)
    {
        $progress_service = new ProgressService($this->_data['token']);
        $progress_service->setFailed();
    }
}
