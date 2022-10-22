@if(config('app.env') === 'local')
    (Local)
@elseif(config('app.env') === 'development')
    (Working)
@elseif(config('app.env') === 'production')
    (Final)
@endif
