<div id="participants_block" class="tab_form card pd-20 mg-t-20">
    <h6 class="card-body-title">Calculation of Participants</h6>
    <div class="card pd-20">
        <div class="form-layout form-layout-4">
            @include('sections/alert')
            <h6 class="tx-inverse">Market Parameters</h6>
            <div class="row mg-b-25">
                <div class="col-lg-6">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex" expression="n"></span>
                            &nbsp;(Potential Participants):
                        </label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="potential_participants" type="text" name="potential_participants" maxlength="5" value="{{ $params['potential_participants'] }}" default_val="{{ $params['potential_participants'] }}" placeholder="Enter Potential Participants">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-6">
                    <div class="row row-xs form-exponential-value" data-name="prize_unit">
                        <label class="col-sm-6 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex" expression="\iota"></span>
                            &nbsp;(Prize Unit):
                        </label>
                        <span data-container="body" data-toggle="popover" data-popover-color="default" data-placement="top" title="Prize Unitの計算について"
                              data-content="現在、Prize Unitが1以外の計算は今後対応予定です"
                              style="position: absolute; margin-top: 5px;"
                        >
                                <i class="fa fa-question-circle" style="font-size: 14px;"></i>
                        </span>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="prize_unit" type="text" name="prize_unit" maxlength="5" value="{{ $params['prize_unit'] }}" default_val="{{ $params['prize_unit'] }}" placeholder="Enter Prize Unit">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row row-xs form-exponential-value" data-name="bankers_budget">
                        <label class="col-sm-6 form-control-label">
                            <span class="tx-danger">*</span>&nbsp
                            <span class="katex" expression="\overline{B}"></span>
                            &nbsp;(Banker's Budget):
                        </label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="bankers_budget" type="text" name="bankers_budget" maxlength="8" value="{{ $params['bankers_budget'] }}" default_val="{{ $params['bankers_budget'] }}" placeholder="Enter Banker's Budget">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-6">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label">
                            <span class="tx-danger">*</span>
                            &nbsp;<span class="katex" expression="\pi"></span>
                            &nbsp;(Participation Fee):
                        </label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="participation_fee" type="text" name="participation_fee" maxlength="8" value="{{ $params['participation_fee'] }}" default_val="{{ $params['participation_fee'] }}" placeholder="Enter Participation Fee">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-6">
                    <div id="player_distributions" class="row row-xs">
                        <label class="col-sm-6 form-control-label">
                            <span class="tx-danger">*</span>
                            &nbsp;Distribution of Cognitive Degrees:
                        </label>
                        <div class="col-sm-9 mg-t-10 mg-sm-t-0">
                            <div style="width: 50px; float: left;">Peak:</div>
                            <input id="distribution_peak" type="number" min="0" max="{{Config::get('simulate.max_banker_budget_degree')}}" value="{{ $params['distribution_peak'] }}" default_val="{{ $params['distribution_peak'] }}" >
                        </div>
                        <div class="col-sm-9 mg-t-10 mg-sm-t-0">
                            <div style="width: 50px; float: left;">Total:</div>
                            <ul id="distribution_number_total_message" class="parsley-errors-list filled" style="position: absolute; margin-left: 120px;">
                                <li>合計値を100にしてください。</li>
                            </ul>
                            <input id="distribution_number_total" type="number" value="0" disabled="disabled">
                        </div>
                    </div>
                </div>
            </div>
            <h6 class="tx-inverse">Program Parameters</h6>
            <div class="row mg-b-25">
                <div class="col-lg-8">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span>Run Participants Simulation: </label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <label class="ckbox">
                                <input type="checkbox" id="run_participants_simulation" name="run_participants_simulation">
                                <span>Run</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25" id="participants_allocate_mode_block">
                <div class="col-lg-8">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span>Participants Allocate Mode: </label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            {{Form::select('allocate_mode', $allocate_list, 'fix', ['id' => 'participants_allocate_mode', 'class' => 'form-control select2'])}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 simulate_player">
            <button class="btn btn-primary mg-b-10 calculate">Calculate</button>
            <button id="reset_participants" class="btn btn-secondary mg-b-10">Reset</button>
            &nbsp;
            <button id="go_next" class="btn btn-info mg-b-10 disabled">Next Step &gt;</button>
            <input type="hidden" id="max_banker_budget_degree" value="{{Config::get('simulate.max_banker_budget_degree')}}" />
            <input type="hidden" id="expected_participant_number" value="">
        </div>
        <div id="participants_spinner" class="d-flex ht-40 pos-relative align-items-center" style="display: none;">
            <div class="sk-chasing-dots">
                <div class="sk-child sk-dot1 bg-gray-800"></div>
                <div class="sk-child sk-dot2 bg-gray-800"></div>
            </div>
        </div><!-- d-flex -->
        <div id="participants-simulate-progress-bar" class="calculate_progress-bar progress" style="margin-top: -25px; display: none;">
            <div class="progress-bar progress-bar-striped bg-success" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

<script id="distributionTemplate" type="text/x-jsrender">
    <div class="col-sm-9 mg-t-10 mg-sm-t-0 distribution_block">
        <div style="width: 50px; float: left;">@{{:i}}: </div>
        <input class="distribution_number" id="distribution_number_@{{:i}}" type="number" name="distribution_number[@{{:i}}]" min="0" max="100" value="0" step="5" number="@{{:i}}">
        <input class="distribution_slider" id="distribution_slider_@{{:i}}" type="range" name="distribution[@{{:i}}]" min="0" max="100" value="0" step="5" number="@{{:i}}">
    </div>
</script>
