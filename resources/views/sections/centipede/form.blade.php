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
                            <span id="cognitive_unit_formula" class="katex_exp" expression="\dfrac{[X_1]^{\frac{[X_2]}{[X_3]}}}{2^{[X_4]}}"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-4">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label">
                            Simulate Combination:
                        </label>
                        <div class="col-sm-4 mg-t-10 mg-sm-t-0">
                            <input type="checkbox" id="simulate_combination" name="simulate_combination">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-2">
                    &nbsp;
                </div>
                <div class="col-lg-2">
                    <div class="row row-xs">
                        <label class="form-control-label">
                            <span>Pattern A_1: </span>
                        </label>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="row row-xs">
                        <label class="form-control-label">
                            <span>Pattern A_2: </span>
                        </label>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="row row-xs">
                        <label class="form-control-label">
                            <span>Pattern B_1: </span>
                            &nbsp;
                            <input type="checkbox" id="enable_pattern_b" name="enable_pattern_b">
                        </label>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="row row-xs">
                        <label class="form-control-label">
                            <span>Pattern B_2: </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex_exp" expression="[X_1]"></span>
                            :</label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_a_1" id="base_numerator_a_1" type="text" name="base_numerator_a_1" maxlength="4" value="300" default_val="300" placeholder="Enter X1">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_a_2" id="base_numerator_a_2" type="text" name="base_numerator_a_2" maxlength="4" value="300" default_val="300" placeholder="Enter X1">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_b_1" id="base_numerator_b_1" type="text" name="base_numerator_b_1" maxlength="4" value="300" default_val="300" placeholder="Enter X1">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_b_2" id="base_numerator_b_2" type="text" name="base_numerator_b_2" maxlength="4" value="300" default_val="300" placeholder="Enter X1">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex_exp" expression="[X_2]"></span>
                            :</label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_a_1" id="numerator_exp_1_a_1" type="text" name="numerator_exp_1_a_1" maxlength="1" value="1" default_val="1" placeholder="Enter X2">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_a_2" id="numerator_exp_1_a_2" type="text" name="numerator_exp_1_a_2" maxlength="1" value="1" default_val="1" placeholder="Enter X2">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_b_1" id="numerator_exp_1_b_1" type="text" name="numerator_exp_1_b_1" maxlength="1" value="1" default_val="1" placeholder="Enter X2">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_b_2" id="numerator_exp_1_b_2" type="text" name="numerator_exp_1_b_2" maxlength="1" value="1" default_val="1" placeholder="Enter X2">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex_exp" expression="[X_3]"></span>
                            :</label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_a_1" id="numerator_exp_2_a_1" type="text" name="numerator_exp_2_a_1" maxlength="1" value="1" default_val="1" placeholder="Enter X3">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_a_2" id="numerator_exp_2_a_2" type="text" name="numerator_exp_2_a_2" maxlength="1" value="1" default_val="1" placeholder="Enter X3">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_b_1" id="numerator_exp_2_b_1" type="text" name="numerator_exp_2_b_1" maxlength="1" value="1" default_val="1" placeholder="Enter X3">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_b_2" id="numerator_exp_2_b_2" type="text" name="numerator_exp_2_b_2" maxlength="1" value="1" default_val="1" placeholder="Enter X3">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex_exp" expression="[X_4]"></span>
                            :</label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_a_1" id="denominator_exp_a_1" type="text" name="denominator_exp_a_1" maxlength="2" value="8" default_val="8" placeholder="Enter X4">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_a_2" id="denominator_exp_a_2" type="text" name="denominator_exp_a_2" maxlength="2" value="8" default_val="8" placeholder="Enter X4">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_b_1" id="denominator_exp_b_1" type="text" name="denominator_exp_b_1" maxlength="2" value="8" default_val="8" placeholder="Enter X4">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control pattern_b_2" id="denominator_exp_b_2" type="text" name="denominator_exp_b_2" maxlength="2" value="8" default_val="8" placeholder="Enter X4">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            Player 1:
                        </label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="combination_player_1_a" id="combination_player_1_a_1" type="radio" name="combination_player_1_a" value="1" checked>
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="combination_player_1_a" id="combination_player_1_a_2" type="radio" name="combination_player_1_a" value="2">
                        </div>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="combination_player_1_b" id="combination_player_1_b_1" type="radio" name="combination_player_1_b" value="1" checked>
                        </div>
                        <div class="col-sm-2s mg-t-10 mg-sm-t-0">
                            <input class="combination_player_1_b" id="combination_player_1_b_2" type="radio" name="combination_player_1_b" value="2">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex_exp" expression="k_{MAX}"></span>
                            :</label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="max_step" type="text" name="max_step" maxlength="3" value="100" default_val="100" placeholder="Enter k_MAX">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            <span class="katex_exp" expression="RC_{MAX}"></span>
                            :</label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input class="form-control" id="max_rc" type="text" name="max_rc" maxlength="3" value="" default_val="" placeholder="Enter RC_MAX(optional)">
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
