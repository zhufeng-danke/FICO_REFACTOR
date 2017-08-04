{{--
Usage.

@include('widget.dropdown-button', ['name' => '租户信息', 'items' => [
    '租户信息' => ['href' => 'xxx', 'target' => 'edit-frame'],
    ...
]])

or

@include('widget.dropdown-button', ['name' => '租户信息', 'items' => [
    '租户信息' => action('xxx'),
    ...
]])

--}}
<div class="btn-group">
    <button type="button" class="btn btn-sm btn-{{ $style ?? 'primary' }} dropdown-toggle" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false"> {{ $name }} <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        @foreach($items as $_name => $_info)
            <li>
                @if(is_array($_info))
                    <a{!! \Html::attributes($_info) !!}>{{ $_name }}</a>
                @else
                    <a href="{{ $_info }}">{{ $_name }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</div>