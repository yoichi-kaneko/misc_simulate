@extends('layouts.app')
@section('use_ajax_post', true)
@section('page_header', 'Nash Social Welfare')

@section('content')
    <div class="kt-pagebody">
        @include('sections.tab', ['tab' => 'main'])
        @include('sections.nash.form')
        @include('sections.nash.chart')
    </div><!-- kt-pagebody -->
@endsection
