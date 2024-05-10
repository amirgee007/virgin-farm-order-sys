@extends('layouts.app')

@section('page-title', trans('app.edit_files'))
@section('page-heading', trans('app.edit_files'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('app.edit_files')
    </li>
@stop

@section('content')
    @include('partials.messages')
    {{--NEED to make file name as {{route('shipping.procedure.save')}} HERE??--}}

    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="card">
                <form method="post" action="{{route('help.faq.update')}}">
                    @csrf()
                    <input type="hidden" value="{{$text->id}}" name="file_type_amir">
                    <div class="card-body">
                        <div class="card-title font-weight-bold">
                            <h3>
                                Edit {{strtoupper($text->label)}}

                                <span style="font-size: 11px" class="badge badge-danger float-right">Last update by {{@$text->user->first_name}} at {{$text->updated_at}}</span>
                            </h3>
                            <hr>
                        </div>

                        <div class="form-group">
                            <label for="procedure"></label>
                                <textarea class="form-control ckeditor" id="procedure" name="value" cols="300">{{$text->value}}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary float-right btn-sm">
                            Update Text
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@stop

@section('scripts')
    @include('partials.toaster-js')
    <script src="{{ url('assets/plugins/tinymce.4.4.3/tinymce.min.js') }}"></script>
    <script type="text/javascript">

        tinymce.init({
            selector: '.ckeditor',
            menubar: '',
            plugins: "code, preview, lists, link , textcolor , imagetools,media",
            media_live_embeds: true, // Enable live embeds for real-time previews
            paste_as_text: true,
            height : 730,
            toolbar: [
                "media|bold underline aligncenter fontsizeselect fontselect forecolor backcolor | bullist numlist outdent indent | styleselect  link | code"
            ],
            style_formats: [
                {title: 'Headers', items: [
                        {title: 'Header 1', format: 'h1'},
                        {title: 'Header 2', format: 'h2'},
                        {title: 'Header 3', format: 'h3'},
                        {title: 'Header 4', format: 'h4'},
                        {title: 'Header 5', format: 'h5'},
                        {title: 'Header 6', format: 'h6'}
                    ]},
                {title: 'Inline', items: [
                        {title: 'Bold', icon: 'bold', format: 'bold'},
                        {title: 'Italic', icon: 'italic', format: 'italic'},
                        {title: 'Underline', icon: 'underline', format: 'underline'},
                        {title: 'Strikethrough', icon: 'strikethrough', format: 'strikethrough'},
                        {title: 'Superscript', icon: 'superscript', format: 'superscript'},
                        {title: 'Subscript', icon: 'subscript', format: 'subscript'}
                    ]},
                {title: 'Blocks', items: [
                        {title: 'Paragraph', format: 'p'},
                        {title: 'Blockquote', format: 'blockquote'}
                    ]},
                {title: 'Alignment', items: [
                        {title: 'Left', icon: 'alignleft', format: 'alignleft'},
                        {title: 'Center', icon: 'aligncenter', format: 'aligncenter'},
                        {title: 'Right', icon: 'alignright', format: 'alignright'},
                        {title: 'Justify', icon: 'alignjustify', format: 'alignjustify'}
                    ]}
            ]

        });
    </script>

@stop
