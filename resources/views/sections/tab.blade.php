<div class="card pd-15">
    <div class="pd-10 bg-gray-300">
        <ul id="tablist" class="nav nav-pills flex-column flex-md-row" role="tablist" page="{{ $tab }}">
            <li class="nav-item"><a class="nav-link" href="{{ action('IndexController@index') }}" role="main">Main</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ action('CoinTossingController@index') }}" role="coin-tossing">Coin Tossing</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ action('LotteryController@index') }}" role="lottery">Lottery</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ action('DocumentController@index') }}" role="document">Document</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ action('NonLinearController@index') }}" role="non-linear">NonLinear(Beta)</a></li>
        </ul>
    </div>
</div>
