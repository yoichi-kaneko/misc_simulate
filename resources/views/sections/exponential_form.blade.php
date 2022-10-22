<script id="exponentialCheckboxTemplate" type="text/x-jsrender">
    &nbsp;
    <input class="check_exp" type="checkbox" data-name="@{{:name}}" checked="checked"><span>&nbsp;Exp.</span>
</script>
<script id="exponentialInputFormTemplate" type="text/x-jsrender">
    <div class="input-group form_exp" data-name="@{{:name}}" style="display: none;">
        <span class="input-group-addon tx-size-sm lh-2">2^</span>
        <input type="number" data-name="@{{:name}}" class="form-control" maxlength="2" min="0" max="25" style="flex: none; width: 100px;">
        <span class="input-group-addon tx-size-sm lh-2" style="flex: auto;">
            =&nbsp;
            <span class="display_amount"></span>
        </span>
    </div>
</script>
