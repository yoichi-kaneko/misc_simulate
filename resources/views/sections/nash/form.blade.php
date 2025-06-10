<div id="nash_block" class="tab_form card pd-20 mg-t-20">
    <h6 class="card-body-title">Calculation of Nash Social Welfare</h6>
    <div class="card pd-20">
        <div class="form-layout form-layout-4">
            <h6 class="tx-inverse">Program Parameters</h6>
            @include('sections/alert')
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex_exp" expression="{\alpha}_1"></span>
                            :</label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input
                                    class="form-control"
                                    id="alpha_1_numerator"
                                    type="text"
                                    name="alpha_1_numerator"
                                    maxlength="4"
                                    value="800"
                                    default_val="800"
                                    placeholder="Enter alpha1"
                                    style="width: 90px; float: left;"
                            >
                            <span style="float: left; padding-left: 4px; padding-right: 4px; padding-top: 4px; font-size: 15pt;">/</span>
                            <input
                                    class="form-control"
                                    id="alpha_1_denominator"
                                    type="text"
                                    name="alpha_1_denominator"
                                    maxlength="4"
                                    value="1000"
                                    default_val="1000"
                                    placeholder="Enter alpha1"
                                    style="width: 90px; float: left;"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex_exp" expression="{\alpha}_2"></span>
                            :</label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input
                                    class="form-control"
                                    id="alpha_2_numerator"
                                    type="text"
                                    name="alpha_2_numerator"
                                    maxlength="4"
                                    value="200"
                                    default_val="200"
                                    placeholder="Enter alpha2"
                                    style="width: 90px; float: left;"
                            >
                            <span style="float: left; padding-left: 4px; padding-right: 4px; padding-top: 4px; font-size: 15pt;">/</span>
                            <input
                                    class="form-control"
                                    id="alpha_2_denominator"
                                    type="text"
                                    name="alpha_2_denominator"
                                    maxlength="4"
                                    value="1000"
                                    default_val="1000"
                                    placeholder="Enter alpha2"
                                    style="width: 90px; float: left;"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex_exp" expression="{\beta}_1"></span>
                            :</label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input
                                    class="form-control"
                                    id="beta_1_numerator"
                                    type="text"
                                    name="beta_1_numerator"
                                    maxlength="4"
                                    value="200"
                                    default_val="200"
                                    placeholder="Enter beta1"
                                    style="width: 90px; float: left;"
                            >
                            <span style="float: left; padding-left: 4px; padding-right: 4px; padding-top: 4px; font-size: 15pt;">/</span>
                            <input
                                    class="form-control"
                                    id="beta_1_denominator"
                                    type="text"
                                    name="beta_1_denominator"
                                    maxlength="4"
                                    value="1000"
                                    default_val="1000"
                                    placeholder="Enter beta1"
                                    style="width: 90px; float: left;"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex_exp" expression="{\beta}_2"></span>
                            :</label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input
                                    class="form-control"
                                    id="beta_2_numerator"
                                    type="text"
                                    name="beta_2_numerator"
                                    maxlength="4"
                                    value="800"
                                    default_val="800"
                                    placeholder="Enter beta2"
                                    style="width: 90px; float: left;"
                            >
                            <span style="float: left; padding-left: 4px; padding-right: 4px; padding-top: 4px; font-size: 15pt;">/</span>
                            <input
                                    class="form-control"
                                    id="beta_2_denominator"
                                    type="text"
                                    name="beta_2_denominator"
                                    maxlength="4"
                                    value="1000"
                                    default_val="1000"
                                    placeholder="Enter beta2"
                                    style="width: 90px; float: left;"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-lg-12">
                    <div class="row row-xs">
                        <label class="col-sm-2 form-control-label">
                            <span class="tx-danger">*</span>
                            <span class="katex_exp" expression="{\rho}"></span>
                            :</label>
                        <div class="col-sm-2 mg-t-10 mg-sm-t-0">
                            <input
                                    class="form-control"
                                    id="rho_numerator"
                                    type="text"
                                    name="rho_numerator"
                                    maxlength="4"
                                    value="900"
                                    default_val="900"
                                    placeholder="Enter rho"
                                    style="width: 90px; float: left;"
                            >
                            <span style="float: left; padding-left: 4px; padding-right: 4px; padding-top: 4px; font-size: 15pt;">/</span>
                            <input
                                    class="form-control"
                                    id="rho_denominator"
                                    type="text"
                                    name="rho_denominator"
                                    maxlength="4"
                                    value="1000"
                                    default_val="1000"
                                    placeholder="Enter rho"
                                    style="width: 90px; float: left;"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 simulate_player">
            <button class="btn btn-primary mg-b-10 calculate">Calculate</button>
            <div id="reset-button-container"></div>
        </div>
        <div id="nash_spinner" class="d-flex ht-40 pos-relative align-items-center" style="display: none;">
            <div class="sk-chasing-dots">
                <div class="sk-child sk-dot1 bg-gray-800"></div>
                <div class="sk-child sk-dot2 bg-gray-800"></div>
            </div>
        </div><!-- d-flex -->
    </div>
</div>
