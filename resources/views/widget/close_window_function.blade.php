{{-- add function _close_window() --}}

@if( UserAgent::isDingTalk() )
    {{-- 忽略了钉钉PC版情况,这部分细化的收益比太低 --}}
    <script src="https://g.alicdn.com/ilw/ding/0.6.6/scripts/dingtalk.js"></script>
    <script>
        var _close_window = function () {
            dd.biz.navigation.back();
        }
    </script>
@elseif ( UserAgent::isWeChat() )
    <script>
        var _close_window = function () {
            WeixinJSBridge.invoke('closeWindow',{},function(e){});
        }
    </script>
@else
    <script>
        var _close_window = function () {
            self.close();
        }
    </script>
@endif
