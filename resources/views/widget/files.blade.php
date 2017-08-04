{{--
文件预览控件
参数:
    + $model instanceOf BaseModel with trait ImagesTrait or DocumentsTrait, required
    + $docs array, optional, eg: ['平面布置图', '插座布置图', ...]
    + $images array, optional, eg: ['平面布置图', '插座布置图', ...]
--}}
@if(isset($docs))
    <div class="ibox-content">
        <h3><i class="fa fa-shield fa-rotate-270"></i> 文档</h3>
        @foreach($docs as $name)
            <div class="panel panel-default">
                <div class="panel-heading"><strong>{{ $name }}</strong></div>
                <div class="panel-body">
                    @include('widget.documents', ['docs' => $model->document($name)])
                </div>
            </div>
        @endforeach
    </div>
@endif
@if(isset($images))
    <div class="ibox-content">
        <h3><i class="fa fa-shield fa-rotate-270"></i> 图片</h3>
        @foreach($images as $name)
            <div class="panel panel-default">
                <div class="panel-heading"><strong>{{ $name }}</strong></div>
                <div class="panel-body">
                    @include('widget.documents', ['docs' => $model->image($name)])
                </div>
            </div>
        @endforeach
    </div>
@endif