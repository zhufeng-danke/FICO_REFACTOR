<div class="pagelistbox">
    @if ($isMoblie)
        <!--moblie 翻页插件-->
        @if ($rows->lastPage() > 1)
            @if ($rows->previousPageUrl())
                {!! link_url($rows->previousPageUrl(), '上一页') !!}
            @else
                <a class="btn btn-default" href="javascript:void(0)" disabled>上一页</a>
            @endif
        @endif

        @if ($rows->nextPageUrl())
            {!! link_url($rows->nextPageUrl(), '下一页') !!}
        @else
            <a class="btn btn-default" href="javascript:void(0)" disabled>下一页</a>
        @endif
        <p style="color:gray;">第 {{ $rows->currentPage() . ' / ' .  $rows->lastPage() }} 页</p>
    <!--moblie 翻页插件-->
    @else
    <!--PC 翻页插件-->
    @if ($rows->lastPage() > 1)
        @if ($rows->previousPageUrl())
            {!! link_url($rows->previousPageUrl(), '<') !!}
            @endif
            @for($i = $rows->currentPage()-1; $i > $rows->currentPage()-5; $i--)
            @break($i < 1)
            <?php
                $pamaArray['page'] = $i;
                $url = Request::fullUrlWithQuery($pamaArray);
                $urladd = link_url($url, $i);
                $urld = $urladd . $urld;
            ?>
            @endfor
            {!! $urld !!}
            @for($j = $rows->currentPage(); $j < $rows->currentPage()+5 ; $j++)
            @break($j > $rows->lastPage())
            <?php
                $pamaArray['page'] = $j;
                $url = Request::fullUrlWithQuery($pamaArray);
            ?>
            <a href="{{$url}}" {{$j==$rooms->currentPage()? ' class=on':''}}>{{$j}}</a>
            @endfor
            @if ($rows->nextPageUrl())
                {!! link_url($rows->nextPageUrl(), '>') !!}
            @endif
        @endif
    <!--PC 翻页插件 end-->
    @endif
</div>