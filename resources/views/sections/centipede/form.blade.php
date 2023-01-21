<div id="centipede_block" class="tab_form card pd-20 mg-t-20">
    <h6 class="card-body-title">Calculation of Centipede</h6>
    <div class="card pd-20">
        <div class="form-layout form-layout-4">
            <h6 class="tx-inverse">Program Parameters</h6>
            @include('sections/alert')
            <div class="row mg-b-25">
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span>Cognitive Unit: </label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            <span class="katex_exp" expression="\dfrac{[X_1]^{\frac{[X_2]}{[X_3]}}}{2^{[X_4]}}"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span>Case: </label>
                        <div class="col-sm-6 mg-t-10 mg-sm-t-0">
                            {{Form::select('case', $case_list, '1', ['id' => 'case', 'class' => 'form-control select2', 'default_val' => 1])}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label"><span class="tx-danger">*</span> Denominator of Delta:</label>
                        <div class="col-sm-4 mg-t-10 mg-sm-t-0">
                            2^
                            <input class="form-control" id="denominator_exp" type="text" name="denominator_exp" maxlength="2" value="8" default_val="8" placeholder="Enter Number of Participants" style="display: inline; width: 40%;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 simulate_player">
            <button class="btn btn-primary mg-b-10 calculate">Calculate</button>
            <button id="reset" class="btn btn-secondary mg-b-10">Reset</button>
        </div>
        <div id="centipede_spinner" class="d-flex ht-40 pos-relative align-items-center" style="display: none;">
            <div class="sk-chasing-dots">
                <div class="sk-child sk-dot1 bg-gray-800"></div>
                <div class="sk-child sk-dot2 bg-gray-800"></div>
            </div>
        </div><!-- d-flex -->
    </div>
</div>

<script id="distributionTemplate" type="text/x-jsrender">
    <div class="col-sm-9 mg-t-10 mg-sm-t-0 distribution_block">
        <div style="width: 50px; float: left;">@{{:i}}: </div>
        <input class="distribution_number" id="distribution_number_@{{:i}}" type="number" name="distribution_number[@{{:i}}]" min="0" max="100" value="0" step="5" number="@{{:i}}">
        <input class="distribution_slider" id="distribution_slider_@{{:i}}" type="range" name="distribution[@{{:i}}]" min="0" max="100" value="0" step="5" number="@{{:i}}">
    </div>
</script>
