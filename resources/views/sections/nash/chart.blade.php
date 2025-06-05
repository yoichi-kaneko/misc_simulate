<div id="chart_area_nash" class="tab_chart card pd-20 mg-t-50" style="display: none;">
    <h6 class="card-body-title">Result</h6>
    <div class="row row-sm report_area">
        <div class="col-lg-12" id="nash_result">
        </div><!-- col-6 -->
    </div>
    <div class="row row-sm chart_area">
        <div class="col-xl-12">
            <div class="card pd-20 pd-sm-40">
                <h6 class="card-body-title">Simulation Chart:</h6>
                <div id="legend-container"></div>
                <canvas id="chart_nash_social_welfare" height="450"></canvas>
            </div><!-- card -->
        </div>
    </div>
</div>

<script id="nashResultTemplate" type="text/x-jsrender">
    <div class="card pd-20 report_block">
        <h6 class="tx-12 tx-uppercase tx-info tx-bold mg-b-15">Report</h6>
        <div class="d-flex mg-b-10">
            <div class="bd-r pd-l-12">
                <label class="tx-12">
                    <span class="katex_exp" expression="a({\rho})"></span>
                </label>
                <p class="tx-lato tx-inverse tx-bold">
                    <span>@{{:a_rho}}</span>
                </p>
            </div>
        </div>
    </div><!-- card -->
</script>