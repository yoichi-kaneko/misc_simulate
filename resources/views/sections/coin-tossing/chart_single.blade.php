<div id="chart_area_single" class="tab_chart card pd-20 mg-t-50" style="display: none;">
    <h6 class="card-body-title">Chart(single)</h6>
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card pd-20">
                <h6 class="tx-12 tx-uppercase tx-info tx-bold mg-b-15">Report</h6>
                <div class="d-flex mg-b-10">
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">start</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="single_report_start"></span></p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">end</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="single_report_end"></span></p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">tried players</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="single_report_tried_players"></span></p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">result</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="single_report_result"></span></p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">cost</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="single_report_cost"></span></p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">ROI</label>
                        <p class="tx-lato tx-inverse tx-bold"><span id="single_report_roi"></span></p>
                    </div>
                </div><!-- d-flex -->
            </div><!-- card -->
        </div><!-- col-6 -->
    </div>
    <div class="row row-sm mg-t-50 chart_area">
        <div class="col-xl-12">
            <div class="card pd-20 pd-sm-40">
                <h6 class="card-body-title">Line Chart</h6>
                <canvas id="chart_single" height="450"></canvas>
                <div class="col-sm-6 col-md-3">
                    <button target="chart_single" class="btn btn-info mg-b-10 chart_animate">Animate</button>
                    <button target="chart_single" class="btn btn-primary mg-b-10 chart_download">Download</button>
                </div>
            </div><!-- card -->
        </div>
    </div>
</div>
