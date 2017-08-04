{{--只在线上启用第三方代码--}}
@if( isProduction() )
    <script>
        var tracker = {
            channel: '{{ substr( Input::get('src'), 0, 12) }}' || null,
            platform: "{{ UserAgent::isWeChat() ? 'wechat' : UserAgent::env() }}",
            city: "{{ $city->name }}"
        };
    </script>

    {{-- Google Analytics https://support.google.com/analytics/topic/2790009?hl=zh-Hans&ref_topic=3544906&vid=1-635784433617916813-3914526369 --}}
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-67874461-1', 'auto');
        ga('set', 'dimension1', tracker.platform);
        ga('set', 'dimension2', tracker.channel);
        ga('set', 'dimension3', tracker.city);
        ga('send', 'pageview');
    </script>

    {{--add baidu for SEM--}}
    <script>
        var _hmt = _hmt || [];
        (function () {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?814ef98ed9fc41dfe57d70d8a496561d";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>

@else
    <script>
        // for debug
        var ga = function () {
            for (var i = 0; i < arguments.length; i++) {
                console.log(i + ' : ' + arguments[i]);
            }
        };
    </script>
@endif