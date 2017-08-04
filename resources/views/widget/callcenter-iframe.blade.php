{{-- iFrame控件

特性
    - frame高度自适应到100%
    - 移动端第二次加载时，自动滚到frame top

接受三个参数
    - $name frame的唯一标记，eg：传入值`edit`，frame的id为`frame-edit`, name为`edit`.
    - $src，$src 可为空
    - $attr, 可选, 其他属性
--}}
<? $name = isset($name) ? $name : md5(microtime()) ?>

<div style="overflow: auto; -webkit-overflow-scrolling:touch; position: relative; height: 100%;">
    <iframe name="{{ $name }}" class="frame-{{ $name }}" id="frame-{{ $name }}" src="{{ $src or '' }}"
            frameborder="0" width="100%"
            scrolling="yes" height="550"
            {{ isset($frameAttr) ? Html::attributes($frameAttr) : '' }}>
    </iframe>
</div>
<script>
    $(document).ready(function () {
        var count = 0;
        var $frame = $('.frame-' + '{{ $name }}');
        var isMobile = '{{ UserAgent::isMobile() ? 'true' : '' }}';
        $frame.load(function () {
            {{-- frame高度 100% --}}
            // $frame.height($frame.contents().height());

            {{-- 移动端第二次加载时自动滚到frame开始 --}}
            if (isMobile && count > 0) {
                $("html, body").animate({scrollTop: $frame.offset().top}, 500);
            }
            count++;
        });
    });
</script>
