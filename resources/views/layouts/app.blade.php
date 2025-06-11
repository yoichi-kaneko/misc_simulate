<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta -->
    @if (View::hasSection('use_ajax_post'))
       <meta name="csrf-token" content="{{ csrf_token() }}">
    @endif
    <title>Miscシミュレータ @include('sections.env')</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icon-192x192.png') }}" sizes="192x192">
    <!-- Vite Assets -->
    @vite(['resources/sass/vendors.scss', 'resources/sass/katniss.scss', 'resources/sass/app.scss'])
</head>

<body class="body">

<!-- ##### HEAD PANEL ##### -->
<div class="kt-headpanel">
</div><!-- kt-headpanel -->

<!-- ##### MAIN PANEL ##### -->
<div class="kt-mainpanel">
    <div class="kt-pagetitle">
        <h5>
            @yield('page_header')
            @include('sections.env')
        </h5>
    </div><!-- kt-pagetitle -->
    @yield('content')
    <div class="kt-footer">
        <span>&nbsp;</span>
        <span>Created by: Webstudio Wanderlust</span>
    </div><!-- kt-footer -->
</div><!-- kt-mainpanel -->

@include('sections.exponential_form')
@vite(['resources/js/vendors.js', 'resources/js/ResizeSensor.js', 'resources/js/main.js'])
@isset($js_file)
    @vite([$js_file])
@endisset

<script id="downloadTemplate" type="text/x-jsrender">
    <a id='image-file' class='hidden' type='application/octet-stream' href='@{{:href}}' download='Chart.png'>
        Download
    </a>
</script>
</body>
</html>
