<div id="coin_tossing_block" class="tab_form card pd-20 mg-t-20">
    <h6 class="card-body-title">Execution of Lottery</h6>
    <div class="card pd-20">
        <div class="form-layout form-layout-4">
            @include('sections/alert')
            <h6 class="tx-inverse">Market Parameters</h6>
            <div class="row mg-b-25">
                <div class="col-lg-6">
                    <div class="row row-xs">
                        <label class="col-sm-6 form-control-label">
                            <span class="tx-danger">*</span>
                            &nbsp;Lottery Params:
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div class="col-sm-6 col-md-3 simulate_lottery">
                    <div class="row row-xs">
                        <button id="add_lottery_block" class="btn btn-primary mg-b-10">Add</button>&nbsp;
                        <button id="remove_lottery_block" class="btn btn-secondary mg-b-10">Remove</button>
                    </div>
                </div>
            </div>
            <div class="row mg-b-25">
                <div id="lottery_rate_blocks" class="col-lg-8">
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 simulate_lottery">
            <button id="calculate_lottery" class="btn btn-primary mg-b-10 calculate">Calculate</button>
            <button id="reset_lottery" class="btn btn-secondary mg-b-10">Reset</button>
        </div>
        <div id="lottery_spinner" class="d-flex ht-40 pos-relative align-items-center" style="display: none;">
            <div class="sk-chasing-dots">
                <div class="sk-child sk-dot1 bg-gray-800"></div>
                <div class="sk-child sk-dot2 bg-gray-800"></div>
            </div>
        </div><!-- d-flex -->
    </div>
</div>

<script id="lotteryRateTemplate" type="text/x-jsrender">
<div id="block_@{{:count}}" class="row row-xs rate_block">
     <div class="col-sm-2 mg-t-10 mg-sm-t-0">
         Prize: &nbsp;
         <div class="input-group mg-b-10">
             <input id="prize_@{{:count}}" class="prize form-control" count="@{{:count}}" maxlength="10" value="300">
         </div>
    </div>
    <div class="col-sm-6 mg-t-10 mg-sm-t-0">
        Rate:
        <div class="input-group mg-b-10">
            <input type="number" id="rate_number_@{{:count}}" class="rate_number form-control" count="@{{:count}}" maxlength="2" min="1" value="1" max="99" step="1" style="flex: none; width: 60px;">
            <span class="input-group-addon tx-size-sm lh-2">* 10^(-</span>
            <input type="number" id="rate_digit_@{{:count}}" class="rate_digit form-control" count="@{{:count}}" maxlength="2" min="1" value="4" max="15" step="1" style="flex: none; width: 60px;">
            <span class="input-group-addon tx-size-sm lh-2" style="flex: auto;">
            ) &nbsp; = &nbsp;
                <span id="display_amount_@{{:count}}" count="@{{:count}}"></span>
            </span>
        </div>
    </div>
</div>
</script>
