@extends('layouts.app')
@section('use_ajax_post', true)
@section('page_header', 'St. Petersburg Simulator NonLinear(Beta)')

@section('content')
    <div class="kt-pagebody">
        @include('sections.tab', ['tab' => 'non-linear'])
        @include('sections.non-linear-participants.form')
        @include('sections.non-linear-participants.chart')
    </div><!-- kt-pagebody -->
@endsection
