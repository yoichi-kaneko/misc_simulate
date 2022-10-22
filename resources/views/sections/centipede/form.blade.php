<div id="participants_block" class="tab_form card pd-20 mg-t-20">
    <h6 class="card-body-title">Calculation of Centipede</h6>
    <div class="card pd-20">
        <div class="col-sm-6 col-md-3 simulate_player">
            <button class="btn btn-primary mg-b-10 calculate">Calculate</button>
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
