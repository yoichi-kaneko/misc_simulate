<div class="card pd-15">
    <div class="pd-10 bg-gray-300">
        <ul id="tablist" class="nav nav-pills flex-column flex-md-row" role="tablist" page="{{ $tab }}">
            <li class="nav-item"><a class="nav-link" href="{{ action('CentipedeController@index') }}" role="centipede">Centipede</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ action('NashController@index') }}" role="centipede">Nash</a></li>
        </ul>
    </div>
</div>
