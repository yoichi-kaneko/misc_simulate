<div id="chart_area_lottery" class="tab_chart card pd-20 mg-t-50" style="display: none;">
    <h6 class="card-body-title">Result</h6>
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card pd-20">
                <h6 class="tx-12 tx-uppercase tx-info tx-bold mg-b-15">Report</h6>
                <div id="lottery_result"></div>
            </div><!-- card -->
        </div><!-- col-6 -->
    </div>
</div>

<script id="lotteryResultTemplate" type="text/x-jsrender">
    <div class="d-flex mg-b-10">
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                Blank Rate
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span>@{{:result}}</span>
            </p>
        </div>
    </div>
</script>

