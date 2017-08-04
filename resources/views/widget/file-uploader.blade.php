{{--
需要参数：name, value, parts, type, status, required

注意：需要在外面主动调用一次JS的`initQNFileUploader()`函数
--}}

<?
$required = isset($required) ? $required : '';
$links = [];
foreach (json_decode($value ?: '{}', true) as $name => $value) {
    $value = (isset($value) && is_array($value)) ? $value : [];
    foreach ($value as $key) {
        if ($this->fileType == 'doc') {
            $links[$key] = [
                    'url' => config('rapyd.qn-doc-store')->url($key),
                    'title' => config('rapyd.qn-doc-store')->title($key)
            ];
        } elseif ($this->fileType == 'image') {
            $links[$key] = [
                    'url' => config('rapyd.qn-image-store')->url($key),
                    'small' => config('rapyd.qn-small-image-store')->url($key),
            ];
        }
    }
}
?>
@if(in_array($status, ['create', 'modify']))
    {!! Form::text($name, $value, ['class' => 'qn-input']) !!}
@else
    <span name="{{ $name }}" class="qn-input" style="display: none;">{{ $value }}</span>
@endif
<div class="qn-upload" data-name="{{ $name }}" data-status="{{ $status }}"
     data-required="{{ $required }}" style="display: none;">;
    @foreach ($parts as $part)
        <span class="qn-upload-part" data-name="{{ $part }}" data-type="{{ $type }}"></span>
    @endforeach
    <span class="qn-upload-links">{{ json_encode($links) }}</span>
</div>