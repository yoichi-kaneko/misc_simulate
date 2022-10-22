@if (session('flash_message'))
    <div id="flash_message" class="row mg-b-25 alert_block">
        <div class="col-lg-12 alert alert-success mg-b-0" role="alert">
            <button type="button" class="close" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <div class="d-flex align-items-center justify-content-start">
                <i class="icon ion-ios-close alert-icon tx-24"></i>
                <span>{{ session('flash_message') }}</span>
            </div><!-- d-flex -->
        </div>
    </div>
@endif
