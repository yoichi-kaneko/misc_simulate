<div id="chart_area_centipede" class="tab_chart card pd-20 mg-t-50" style="display: none;">
    <h6 class="card-body-title">Result</h6>
    <div class="row row-sm chart_area">
        <div class="col-xl-12">
            <div class="card pd-20 pd-sm-40">
                <h6 class="card-body-title">Simulation Chart:</h6>
                <div id="legend-container"></div>
                <canvas id="chart_centipede_simulation" height="450"></canvas>
                <div class="col-sm-6 col-md-3">
                    <button target="chart_centipede_simulation" class="btn btn-primary mg-b-10 chart_download">Download</button>
                </div>
            </div><!-- card -->
        </div>
    </div>

    <div class="pd-10 mg-t-20 bg-gray-300" id="centipede_tab"></div><!-- pd-10 -->
    <div class="row row-sm">
        <div class="col-lg-12" id="centipede_result">
        </div><!-- col-6 -->
    </div>

</div>

<script id="centipedeTabTemplate" type="text/x-jsrender">
    <ul class="nav nav-gray-600 flex-column flex-sm-row" role="tablist">
        @{{props pattern_data}}
            <li class="nav-item"><a class="nav-link switch_pattern" data-toggle="tab" href="#" pattern="@{{:key}}">
                Pattern <span style="text-transform: uppercase;">@{{:key}}</span>
            </a></li>
        @{{/props}}
        @{{if combination_data}}
            @{{props combination_data}}
            <li class="nav-item"><a class="nav-link switch_pattern" data-toggle="tab" href="#" pattern="combination_@{{:key}}">
                Combination(<span style="text-transform: uppercase;">@{{:key}}</span>)
            </a></li>
            @{{/props}}
        @{{/if}}
    </ul>
</script>

<script id="centipedeResultTemplate" type="text/x-jsrender">
    <div class="card pd-20 report_block" id="report_pattern_@{{:pattern}}">
        <h6 class="tx-12 tx-uppercase tx-info tx-bold mg-b-15">Report</h6>
        <div class="d-flex mg-b-10">
            <div class="bd-r pd-l-12">
                <label class="tx-12">Cognitive Unit</label>
                <p class="tx-lato tx-inverse tx-bold">
                    <span class="katex_exp" expression="@{{:cognitive_unit_latex_text}}"></span>
                </p>
            </div>
            <div class="bd-r pd-l-12">
                <label class="tx-12">Cognitive Unit Value</label>
                <p class="tx-lato tx-inverse tx-bold">
                    <span>@{{:cognitive_unit_value}}</span>
                </p>
            </div>
            <div class="bd-r pd-l-12">
                <label class="tx-12">Average of Reversed Causality</label>
                <p class="tx-lato tx-inverse tx-bold">
                    <span>@{{:average_of_reversed_causality}}</span>
                </p>
            </div>
        </div>
        <div class="showmore_block" id="centipede_result_block_@{{:pattern}}">
            <table id="centipede_result_table" class="table mg-b-0">
                <thead>
                    <tr class="centipede_result_body">
                        <th style="text-transform: none; width=15%;">
                            k
                        </th>
                        <th style="text-transform: none; width=20%;">
                            <span class="katex_exp" expression="\nu_{M}"></span>
                        </th>
                        <th style="width=25%;">
                            left side
                        </th>
                        <th style="width=25%;">
                            right side
                        </th>
                        <th style="width=15%;">
                            result
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @{{for table_data}}
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
        </div>
    </div><!-- card -->
</script>

<script id="centipedeCombinationResultTemplate" type="text/x-jsrender">
    <div class="card pd-20 report_block" id="report_pattern_combination_@{{:pattern}}">
        <h6 class="tx-12 tx-uppercase tx-info tx-bold mg-b-15">Report</h6>
        <div class="d-flex mg-b-10">
            <div class="bd-r pd-l-12">
                <label class="tx-12">Cognitive Unit(1)</label>
                <p class="tx-lato tx-inverse tx-bold">
                    <span class="katex_exp" expression="@{{:cognitive_unit_latex_text_1}}"></span>
                </p>
            </div>
            <div class="bd-r pd-l-12">
                <label class="tx-12">Cognitive Unit(2)</label>
                <p class="tx-lato tx-inverse tx-bold">
                    <span class="katex_exp" expression="@{{:cognitive_unit_latex_text_2}}"></span>
                </p>
            </div>
            <div class="bd-r pd-l-12">
                <label class="tx-12">Cognitive Unit Value(1)</label>
                <p class="tx-lato tx-inverse tx-bold">
                    <span>@{{:cognitive_unit_value_1}}</span>
                </p>
            </div>
            <div class="bd-r pd-l-12">
                <label class="tx-12">Cognitive Unit Value(2)</label>
                <p class="tx-lato tx-inverse tx-bold">
                    <span>@{{:cognitive_unit_value_2}}</span>
                </p>
            </div>
            <div class="bd-r pd-l-12">
                <label class="tx-12">Average of Reversed Causality</label>
                <p class="tx-lato tx-inverse tx-bold">
                    <span>@{{:average_of_reversed_causality}}</span>
                </p>
            </div>
        </div>
        <div class="showmore_block" id="centipede_result_block_combination">
            <table id="centipede_result_table" class="table mg-b-0">
                <thead>
                    <tr class="centipede_result_body">
                        <th style="text-transform: none; width=15%;">
                            k
                        </th>
                        <th style="text-transform: none; width=20%;">
                            <span class="katex_exp" expression="\nu_{M}"></span>
                        </th>
                        <th style="width=25%;">
                            left side
                        </th>
                        <th style="width=25%;">
                            right side
                        </th>
                        <th style="width=15%;">
                            result
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @{{for table_data}}
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
        </div>
    </div><!-- card -->
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
