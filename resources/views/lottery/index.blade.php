@extends('layouts.app')
@section('use_ajax_post', true)
@section('page_header', 'St. Petersburg Simulator Lottery')

@section('content')
    <div class="kt-pagebody">
        @include('sections.tab', ['tab' => 'lottery'])
        @include('sections.lottery.form')
        @include('sections.lottery.chart')
    </div><!-- kt-pagebody -->
@endsection
