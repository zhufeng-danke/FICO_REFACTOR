{{-- 文档列表控件

传入参数：
    - $docs   七牛的图片key列表
--}}
<? $previewer = new \FileStore\Document(); ?>

@if(count($docs) === 0)
    <span class="text-danger">尚未上传</span>
@endif

<div style="line-height: 3em;">
    @foreach($docs as $key)
        <a href="{{ $previewer->url($key) }}" class="img-thumbnail text-center" target="_blank">
            <p><i class="fa fa-file"></i> {{ $previewer->title($key) }}</p>
            <small class="text-muted">点击下载</small>
        </a>
    @endforeach
</div>
