@extends('layouts.app')
@section('use_ajax_post', true)
@section('page_header', 'Centipede Simulator')

@section('content')
    <div class="kt-pagebody">
        @include('sections.tab', ['tab' => 'main'])
        @include('sections.centipede.form')
        @include('sections.centipede.chart')
    </div><!-- kt-pagebody -->
@endsection
