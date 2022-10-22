@extends('layouts.app')
@section('page_header', 'St. Petersburg Simulator Document')

@section('content')
    <div class="kt-pagebody">
        @include('sections.tab', ['tab' => 'document'])
        <div class="card pd-20 pd-sm-40 mg-t-25">
            <h6 class="card-body-title">Inline Elements</h6>
            <p class="mg-b-20 mg-sm-b-30">Styling for common inline elements.</p>
            <p>You can use the mark tag to <mark>highlight</mark> text.</p>
            <p><s>This line of text is meant to be treated as no longer accurate.</s></p>
            <p><u>This line of text will render as underlined</u></p>
            <p><small>This line of text is meant to be treated as fine print.</small></p>
            <p><strong>This line rendered as bold text.</strong></p>
            <p class="mg-b-0"><em>This line rendered as italicized text.</em></p>
        </div>
        <div class="card pd-20 pd-sm-40 mg-t-25">
            <h6 class="card-body-title">Blockquotes</h6>
            <p class="mg-b-20 mg-sm-b-30">For quoting blocks of content from another source within your document.</p>
            <blockquote class="blockquote bd-l bd-5 pd-l-20">
                <p class="mg-b-5 tx-inverse">Two things are infinite: the universe and human stupidity; and I'm not sure about the universe.</p>
                <footer class="blockquote-footer tx-14">Albert Einstein</footer>
            </blockquote>
        </div>
        <div class="card pd-20 pd-sm-40 mg-t-25">
            <h6 class="card-body-title">Description List Alignment</h6>
            <p class="mg-b-20 mg-sm-b-30">Align terms and descriptions horizontally by using our grid system’s predefined classes.</p>

            <dl class="row">
                <dt class="col-sm-3 tx-inverse">Description lists</dt>
                <dd class="col-sm-9">A description list is perfect for defining terms.</dd>

                <dt class="col-sm-3 tx-inverse">Euismod</dt>
                <dd class="col-sm-9">
                    <p class="mg-b-10">Vestibulum id ligula porta felis euismod semper eget lacinia odio sem nec elit.</p>
                    <p>Donec id elit non mi porta gravida at eget metus.</p>
                </dd>

                <dt class="col-sm-3 tx-inverse">Malesuada porta</dt>
                <dd class="col-sm-9">Etiam porta sem malesuada magna mollis euismod.</dd>

                <dt class="col-sm-3 text-truncate tx-inverse">Truncated term is truncated</dt>
                <dd class="col-sm-9">Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</dd>

                <dt class="col-sm-3 tx-inverse">Nesting</dt>
                <dd class="col-sm-9">
                    <dl class="row">
                        <dt class="col-sm-4 tx-inverse">Nested definition list</dt>
                        <dd class="col-sm-8">Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc.</dd>
                    </dl>
                </dd>
            </dl>
        </div>
    </div><!-- kt-pagebody -->
@endsection
