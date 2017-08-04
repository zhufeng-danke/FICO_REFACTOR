@extends('errors.layout')

@section('title')
    {{ $message }}
@endsection

@section('content')
    <div class="panel panel-danger text-center">
        <div class="panel-body">
            <p class="text-danger lead">{!! $message !!}</p>

            <small>
                定位码：{{ $locate }}
                @if($code)
                    ，错误码: <code>{{ $code }}</code>
                @endif
            </small>
        </div>
        <div class="panel-footer">
            <a href='javascript:history.go(-1);' class="btn btn-white btn-sm">
                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                返回之前页面
            </a>
        </div>
    </div>
@stop
