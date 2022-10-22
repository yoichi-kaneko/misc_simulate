<?php

namespace App\Jobs;

use App\Calculations\Linear\Comparison;
use App\Services\ProgressService;
use App\Services\ParticipantsSimulationService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateParticipantsSimulation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
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
        $comparison_result = Comparison::run(
            $this->_data['bankers_budget'],
            $this->_data['participation_fee'],
            $this->_data['prize_unit'],
            $this->_data['potential_participants'],
            $this->_data['cognitive_degrees_distribution']
        );
        $multi = new ParticipantsSimulationService();
        $multi->run(
            $comparison_result,
            $comparison_result['cognitive_degrees_distribution'],
            $comparison_result['row'],
            $comparison_result['potential_participants'],
            $comparison_result['total_variance'],
            $this->_data['participants_allocate_mode'],
            $this->_data['token']
        );
    }

    public function failed($exception)
    {
        $progress_service = new ProgressService($this->_data['token']);
        $progress_service->setFailed();
    }
}
