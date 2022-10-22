@extends('layouts.app')
@section('use_ajax_post', true)
@section('page_header', 'St. Petersburg Simulator Main')

@section('content')
    <div class="kt-pagebody">
        @include('sections.tab', ['tab' => 'main'])
        @include('sections.participants.form')
        @include('sections.coin-tossing.form', ['with_participants' => true])
        @include('sections.participants.chart')
        @include('sections.coin-tossing.chart_multi')
        @include('sections.coin-tossing.chart_multi_child')
        @include('sections.coin-tossing.chart_single')
    </div><!-- kt-pagebody -->
@endsection
