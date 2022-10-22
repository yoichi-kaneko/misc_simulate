<div id="chart_area_player" class="tab_chart card pd-20 mg-t-50" style="display: none;">
    <h6 class="card-body-title">Result</h6>
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card pd-20">
                <h6 class="tx-12 tx-uppercase tx-info tx-bold mg-b-15">Report</h6>
                <div id="comparison_result"></div>
            </div><!-- card -->
        </div><!-- col-6 -->
    </div>
    <div class="row row-sm mg-t-50 chart_area">
        <div class="col-xl-12">
            <div class="card pd-20 pd-sm-40">
                <h6 class="card-body-title">Distribution Chart</h6>
                <canvas id="chart_comparison" height="450"></canvas>
            </div><!-- card -->
        </div>
    </div>
    {{--
<div id="participants_distribution_spinner" class="d-flex ht-40 pos-relative align-items-center" style="display: none;">
    <div class="sk-chasing-dots">
        <div class="sk-child sk-dot1 bg-gray-800"></div>
        <div class="sk-child sk-dot2 bg-gray-800"></div>
    </div>
</div><!-- d-flex -->
--}}
</div>

<div id="chart_area_participants_simulation" class="tab_chart card pd-20 mg-t-50" style="display: none;">
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card pd-20">
                <h6 class="tx-12 tx-uppercase tx-info tx-bold mg-b-15">Report</h6>
                <div id="participants_simulation_result"></div>
            </div><!-- card -->
        </div><!-- col-6 -->
    </div>
    <div class="row row-sm mg-t-50 chart_area">
        <div class="col-xl-12">
            <div class="card pd-20 pd-sm-40">
                <h6 class="card-body-title">Simulation: Distribution of #participants / #runs</h6>
                <canvas id="chart_participants_simulation" height="450"></canvas>
            </div><!-- card -->
        </div>
    </div>
</div>

<!-- expand formula modal -->
<div id="expanded_formula_modal" class="modal fade">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content tx-size-sm">
            <div class="modal-body pd-20">
                <p class="mg-b-5" id="expanded_formula_modal_text"></p>
            </div><!-- modal-body -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary pd-x-20" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div><!-- modal-dialog -->
</div><!-- modal -->

<script id="comparisonTemplate" type="text/x-jsrender">
    <div class="d-flex mg-b-10">
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="\iota \bullet 2^{\overline{t}}"></span>
                (Maximum Prize)
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:banker_maximum_prize}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="{\overline{t}}"></span>
                (Budget Degree)
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:banker_budget_degree}}"></span>
            </p>
        </div>
                <div class="bd-r pd-l-12">
            <label class="tx-12">Participation Rate</label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:participation_rate}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="\mu_{T}"></span>
                &nbsp;
                <span data-container="body" data-toggle="popover" data-popover-color="default" data-placement="top" title="" data-content="Theoretical Expected Number of Participants">
                        <i class="fa fa-question-circle" style="font-size: 14px;"></i>
                </span>
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:expected_participant_number}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="\sigma^2_{T}"></span>
                &nbsp;
                <span data-container="body" data-toggle="popover" data-popover-color="default" data-placement="top" title="" data-content="Theoretical Total Variance">
                    <i class="fa fa-question-circle" style="font-size: 14px;"></i>
                </span>
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:total_variance}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="{\theta_i(\pi)}"></span>
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:theta_function_values.theta_fee}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="{\theta_i(0)}"></span>
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:theta_function_values.theta_zero}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="{\theta_i(2^{\overline{t}})}"></span>
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:theta_function_values.theta_exp}}"></span>
            </p>
        </div>
        <div class="bd-r pd-l-12">
            <label class="tx-12">
                <span class="katex_exp" expression="{\theta^*_i(\pi) = 2^{\overline{t}} \bullet \frac{\theta_i(\pi) - \theta_i(0)}{\theta_i(2^{\overline{t}}) - \theta_i(0)}}"></span>
            </label>
            <p class="tx-lato tx-inverse tx-bold">
                <span class="katex_exp" expression="@{{:theta_function_values.rounded_theta_ast}}"></span>
            </p>
        </div>
    </div>
    <table id="comparison_result_table" class="table mg-b-0">
        <thead>
            <tr class="comparison_result_body">
                <th>
                    Cognitive Degree
                    <span class="katex_exp" expression="({\rho_{i}})"></span>
                </th>
                <th>
                    <span class="katex_exp" expression="\iota \left[ \overline{u}_{\rho_{i}} \left( \pi \right) ;\ \underline{u}_{\rho_{i}} \left( \pi \right) \right]"></span>
                </th>
                <th>
                    <span class="katex_exp" expression="\iota \left[ \overline{u}_{\rho_{i}} \left( \sigma_{\rho_{i}} \right)\ ;\ \underline{u}_{\rho_{i}} \left( \sigma_{\rho_{i}} \right) \right]"></span>
                </th>
                <th>
                    &nbsp;
                </th>
                <th>
                    <span class="katex_exp" expression="Pr(\rho_{i})"></span>
                    &nbsp;
                    <span data-container="body" data-toggle="popover" data-popover-color="default" data-placement="top" title="Popover top"
data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
                        <i class="fa fa-question-circle" style="font-size: 14px;"></i>
                    </span>
                </th>
            </tr>
        </thead>
        <tbody>
            @{{for row}}
                <tr class="comparison_result_body">
                    <td style="vertical-align: middle;">
                        @{{:cognitive_degree}}
                    </td>
                    <td>
                        <span class="katex_exp" expression="@{{:utility_functions.fee.display_expression}}"></span>
                    </td>
                    <td style="vertical-align: middle;">
                        <span class="katex_exp" expression="@{{:utility_functions.subjective_understanding.display_expression}}"></span>
                    </td>
                    <td style="vertical-align: middle;">
                        <span class="expanded_formula" data-expanded_formula_expression="@{{:utility_functions.subjective_understanding.expanded_formula_expression}}">
                            <button class="btn btn-outline-secondary">Expand</button>
                        </span>
                    </td>
                    <td style="vertical-align: middle;">
                        @{{:comparison.display}}
                    </td>
                </tr>
            @{{/for}}
        </tbody>
    </table>
</script>
