{{-- 图片列表控件

传入参数：
    - $images   七牛的图片key列表
    - $size     预览图的大小，eg：100
--}}
<?
$big = new \FileStore\Image();

$size = $size ?? 100;
$small = (new \FileStore\Image())->size($size, $size);
?>

@if(count($images) === 0)
    <span class="text-danger">尚未上传</span>
@endif

<div class="image-widget">
    @foreach($images as $key)
        <a href="{{ $big->url($key) }}" target="_blank">
            <img src="{{ $small->url($key) }}" class="img-thumbnail img-responsive">
        </a>
    @endforeach
</div>

@include('admin.widget.imageviewer', ['img' => '.image-widget img'])