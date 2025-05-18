@extends('layouts.app')
@section('use_ajax_post', true)
@section('page_header', 'Nash Simulator')

@section('content')
    <div class="kt-pagebody">
        @include('sections.tab', ['tab' => 'main'])
        @include('sections.nash.form')
    </div><!-- kt-pagebody -->
@endsection
