<div id="chart_area_centipede" class="tab_chart card pd-20 mg-t-50" style="display: none;">
    <h6 class="card-body-title">Result</h6>
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card pd-20">
                <h6 class="tx-12 tx-uppercase tx-info tx-bold mg-b-15">Report</h6>
                <div class="d-flex mg-b-10">
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">Cognitive Unit</label>
                        <p class="tx-lato tx-inverse tx-bold">
                            <span id="cognitive_unit_latex_text" class="katex_exp"></span>
                        </p>
                    </div>
                    <div class="bd-r pd-l-12">
                        <label class="tx-12">Cognitive Unit Value</label>
                        <p class="tx-lato tx-inverse tx-bold">
                            <span id="cognitive_unit_value"></span>
                        </p>
                    </div>
                </div>
                <div id="centipede_result"></div>
            </div><!-- card -->
        </div><!-- col-6 -->
    </div>
    <div class="row row-sm mg-t-50 chart_area">
        <div class="col-xl-12">
            <div class="card pd-20 pd-sm-40">
                <h6 class="card-body-title">Simulation Chart:</h6>
                <canvas id="chart_centipede_simulation" height="450"></canvas>
            </div><!-- card -->
        </div>
    </div>
</div>


<script id="centipedeTemplate" type="text/x-jsrender">
    <table id="centipede_result_table" class="table mg-b-0">
        <thead>
            <tr class="centipede_result_body">
                <th>
                    t
                </th>
                <th>
                    <span class="katex_exp" expression="\nu_{M}"></span>
                </th>
                <th>
                    left side
                </th>
                <th>
                    right side
                </th>
                <th>
                    result
                </th>
            </tr>
        </thead>
        <tbody>
            @{{for data}}
                <tr class="centipede_result_body @{{if result}}result_true@{{/if}}">
                    <td>
                        @{{:t}}
                    </td>
                    <td>
                        @{{:max_nu_value}}
                    </td>
                    <td>
                        @{{:left_side_value}}
                    </td>
                    <td>
                        @{{:right_side_value}}
                    </td>
                    <td>
                        @{{:result}}
                    </td>
                </tr>
            @{{/for}}
        </tbody>
    </table>
</script>

<script id="participantsSimulationResultTemplate" type="text/x-jsrender">
    <div class="d-flex mg-b-10">
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                Potential Participants
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:potential_participants}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                Iteration #runs
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:iteration}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="\mu_{M}"></span>
                &nbsp;
                <span data-container="body" data-toggle="popover" data-popover-color="default" data-placement="top" title="" data-content="Measured Expected Number of Participants">
                    <i class="fa fa-question-circle" style="font-size: 14px;"></i>
                </span>
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:expected_participants}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="\sigma^2_{M}"></span>
                &nbsp;
                <span data-container="body" data-toggle="popover" data-popover-color="default" data-placement="top" title="" data-content="Measured Total Variance">
                    <i class="fa fa-question-circle" style="font-size: 14px;"></i>
                </span>
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:total_variance}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="\sigma_{M}"></span>
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:total_variance_root}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="\mu_{M} - \sigma_{M} \leq \#participants \leq \mu_{M} + \sigma_{M}"></span>
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:first_confidence_interval}} / @{{:iteration}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="\mu_{M} - 2\sigma_{M} \leq \#participants \leq \mu_{M} + 2\sigma_{M}"></span>
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:second_confidence_interval}} / @{{:iteration}}"></span>
            </p>
        </div>
    </div>
</script>
