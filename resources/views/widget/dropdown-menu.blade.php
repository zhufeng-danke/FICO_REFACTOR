{{--

依赖:
    bootstrap框架

参数列表:
    必选  name    field name
    必选  items   选项列表

Usage :

    @include('widget.dropdown-menu', [
        'name' => 'input_name',
        'items' => [
            '现房' => 'room_ready_count',
            '现房+期房' => 'room_count',
        ],
        'default_value' => 默认值,
        'class' => 'btn-group xxx xxx',
        'right' => false,                           //下拉列表是否靠右
    ]])

--}}

<div id="dd_{{$name}}" class="dropdown {{$class or ''}}">
    <input type="hidden" name="{{$name}}" value="{{ $default_value or key($items)}}">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
        <span class="value">{{  $items[$default_value] or reset($items) }}</span>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu {{ ( isset($right) && $right === true ) ? 'dropdown-menu-right' : ''}}">
        @foreach ( $items as $value => $text)
            <li><a href="javascript:dropdown_selected('dd_{{$name}}', '{{$value}}', '{{$text}}');">{{$text}}</a></li>
        @endforeach
    </ul>
</div>

<script>
    function dropdown_selected(dd, val, text) {
        var dropdown = $("#" + dd);
        dropdown.find('input').val(val);
        dropdown.find('span.value').text(text);
    }
</script>
