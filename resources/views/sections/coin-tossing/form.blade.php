<div id="coin_tossing_block" class="tab_form card pd-20 mg-t-20">
    <h6 class="card-body-title">Execution of Coin-tossings</h6>
    <div class="card pd-20">
        <div class="form-layout form-layout-4">
            @include('sections/alert')
            <h6 class="tx-inverse">Market Parameters</h6>
            <div class="row mg-b-25">
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span> Number of Participants:</label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="cointoss_participant_number" type="text" name="participant_number" maxlength="8" value="{{ $params['player_number'] }}" default_val="{{ $params['player_number'] }}" placeholder="Enter Number of Participants">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span> Banker's Budget Degree:</label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="cointoss_banker_budget_degree" type="text" name="banker_budget_degree" maxlength="3" value="{{ $params['banker_budget_degree'] }}" default_val="{{ $params['banker_budget_degree'] }}" placeholder="Enter Banker's Budget Degree">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span> Participation Fee:</label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="cointoss_participation_fee" type="text" name="participation_fee" maxlength="8" value="{{ $params['participation_fee'] }}" default_val="{{ $params['participation_fee'] }}" placeholder="Enter Participation Fee">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span> Banker's Prepared Changes:</label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="cointoss_banker_prepared_change" type="text" name="banker_prepared_change" maxlength="8" value="{{ $params['banker_prepared_change'] }}" default_val="{{ $params['banker_prepared_change'] }}" placeholder="Enter Banker's Prepared Changes">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span> Initial Setup Cost:</label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="cointoss_initial_setup_cost" type="text" name="initial_setup_cost" maxlength="8" value="{{ $params['initial_setup_cost'] }}" default_val="{{ $params['initial_setup_cost'] }}" placeholder="Initial Setup Cost">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span> Facility Unit:</label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="cointoss_facility_unit" type="text" name="facility_unit" maxlength="5" value="{{ $params['facility_unit'] }}" default_val="{{ $params['facility_unit'] }}" placeholder="Enter Facility Unit">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span> Facility Unit Cost:</label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="cointoss_facility_unit_cost" type="text" name="facility_unit_cost" maxlength="8" value="{{ $params['facility_unit_cost'] }}" default_val="{{ $params['facility_unit_cost'] }}" placeholder="Enter Facility Unit Cost">
                        </div>
                    </div>
                </div>
            </div>
            <h6 class="tx-inverse">Program Parameters</h6>
            <div class="row mg-b-25">
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span>Mode: </label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            {{Form::select('calculate_mode', $mode_list, 'multi', ['id' => 'cointoss_calculate_mode', 'class' => 'form-control select2'])}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-4">
                    <div class="row row-xs for_multi">
                        <label class="col-sm-6 form-control-label">Iteration:</label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="cointoss_iteration" type="text" name="iteration" maxlength="6" value="{{ $params['iteration'] }}" default_val="{{ $params['iteration'] }}" placeholder="Enter Iteration">
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label">Random Seed:</label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="cointoss_random_seed" type="text" name="random_seed" maxlength="8" value="" default_val="" placeholder="Random Seed(Optional)">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-4 for_multi">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span>Save Each Transition: </label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <label class="ckbox">
                                <input type="checkbox" id="cointoss_save_each_transitions" name="save_each_transitions">
                                <span>Save</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 simulate_main">
            <button class="btn btn-primary mg-b-10 calculate">Calculate</button>
            <button id="reset_cointossing" class="btn btn-secondary mg-b-10">Reset</button>
            @if (!empty($with_participants))
                &nbsp;
                <button id="go_prev" class="btn btn-info mg-b-10">&lt; Prev Step</button>
            @endif
        </div>
        <div id="coin_tossing_spinner" class="d-flex ht-40 pos-relative align-items-center" style="display: none;">
            <div class="sk-chasing-dots">
                <div class="sk-child sk-dot1 bg-gray-800"></div>
                <div class="sk-child sk-dot2 bg-gray-800"></div>
            </div>
        </div><!-- d-flex -->
        <div id="coin-tossing-progress-bar" class="calculate_progress-bar progress" style="margin-top: -25px; display: none;">
            <div class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>
