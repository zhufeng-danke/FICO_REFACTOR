<?
$action = str_replace('.html', '', Input::segment(2));
?>
<div class="hp_usbox">
    <ul>
        @foreach([
            '关于蛋壳' => 'aboutus',
            '联系蛋壳' => 'contact',
            '加入蛋壳' => 'join',
        ] as $key => $val)
            <li class="{{ $action === $val ? 'active' : '' }}">
                <a href="{{ action( 'HomeController@about', $val) }}">{{ $key }}</a>
            </li>
        @endforeach
    </ul>
</div>
