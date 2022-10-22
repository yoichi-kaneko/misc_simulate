@extends('layouts.app')
@section('use_ajax_post', true)
@section('page_header', 'St. Petersburg Simulator Coin Tossing')

@section('content')
    <div class="kt-pagebody">
        @include('sections.tab', ['tab' => 'coin-tossing'])
        @include('sections.coin-tossing.form')
        @include('sections.coin-tossing.chart_multi')
        @include('sections.coin-tossing.chart_multi_child')
        @include('sections.coin-tossing.chart_single')
    </div><!-- kt-pagebody -->
@endsection
