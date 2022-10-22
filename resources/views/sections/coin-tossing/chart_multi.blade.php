<div id="chart_area_multi" class="tab_chart card pd-20 mg-t-50" style="display: none;">
    <h6 class="card-body-title">Chart(multi)</h6>
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card pd-20">
                <h6 class="tx-12 tx-uppercase tx-info tx-bold mg-b-15">Report</h6>
                <div class="d-flex mg-b-10">
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">average revenue</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="multi_report_average"></span></p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">standard deviation of revenue</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="multi_report_standard_deviation"></span></p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">iteration</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="multi_report_iteration"></span></p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">result increase</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="multi_report_increasing_cases"></span></p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">bankruptcy cases</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="multi_report_bankruptcy_cases"></span></p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">cost</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="multi_report_cost"></span></p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">ROI</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="multi_report_roi"></span></p>
                    </div>
                </div><!-- d-flex -->
            </div><!-- card -->
        </div><!-- col-6 -->
    </div>
    <div class="row row-sm mg-t-50 chart_area">
        <div class="col-xl-12">
            <div class="card pd-20 pd-sm-40">
                <h6 class="card-body-title">Line Chart</h6>
                <div id="multi_re_render_alert_danger" class="row mg-b-25 alert_block" style="display: none;">
                    <div class="col-lg-12 alert alert-danger mg-b-0" role="alert">
                        <button type="button" class="close" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <div class="d-flex align-items-center justify-content-start">
                            <i class="icon ion-ios-close alert-icon tx-24"></i>
                            <span id="multi_re_render_alert_danger_message"></span>
                        </div><!-- d-flex -->
                    </div>
                </div>
                <div class="row mg-t-10 mg-b-30">
                    <div class="col-lg-1">
                        <label class="rdiobox">
                            <input id="multi_render_mode_all" name="multi_render_mode" type="radio" value="all" checked>
                            <span>All</span>
                        </label>
                    </div><!-- col-3 -->
                    <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                        <label class="rdiobox">
                            <input name="multi_render_mode" type="radio" value="bankruptcy">
                            <span>Bankruptcy</span>
                        </label>
                    </div><!-- col-3 -->
                    <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                        <label class="rdiobox">
                            <input name="multi_render_mode" type="radio" value="custom">
                            <span>Custom</span>
                        </label>
                    </div><!-- col-3 -->
                    <div class="col-lg-3 mg-t-20 mg-lg-t-0">
                        <input class="form-control" id="render_custom_from" type="text" name="render_custom_from" maxlength="10" value="" style="display: inline; width: 40%;">
                        〜
                        <input class="form-control" id="render_custom_to" type="text" name="render_custom_from" maxlength="10" value="" style="display: inline; width: 40%;">
                    </div>
                    <div class="col-lg-1 mg-t-20 mg-lg-t-0">
                        <button id="multi_re_render" class="btn btn-primary mg-b-10">Re-Render</button>
                    </div>
                </div>
                <canvas id="chart_multi" height="450"></canvas>
                <div class="col-sm-6 col-md-3 mg-lg-t-30">
                    <button target="chart_multi" class="btn btn-primary mg-b-10 chart_download">Download</button>
                </div>

                <div id="multi_child_alert_danger" class="row mg-b-25 alert_block" style="display: none;">
                    <div class="col-lg-12 alert alert-danger mg-b-0" role="alert">
                        <button type="button" class="close" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <div class="d-flex align-items-center justify-content-start">
                            <i class="icon ion-ios-close alert-icon tx-24"></i>
                            <span id="multi_child_alert_danger_message"></span>
                        </div><!-- d-flex -->
                    </div>
                </div>

                <input type="hidden" id="multi_save_each_transitions">
                <input type="hidden" id="multi_step">
                <input type="hidden" id="multi_min_cache">
                <input type="hidden" id="multi_max_cache">
                <input type="hidden" id="child_x_label" val="xxx">
                <div class="col-sm-6 col-md-3">
                    <button id="render_multi_child" target="chart_multi_child" class="btn btn-primary mg-b-10 render_child">Render Child Chart Nearly: <span class="child_x_label">***</span></button>
                </div>
            </div><!-- card -->
        </div>
    </div>
</div>
