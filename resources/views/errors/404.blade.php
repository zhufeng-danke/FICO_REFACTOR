@extends('layout_v3_upgrade')

@section('nav_channel')
    @css('css/room.css')
    <div class="fl nav_channel">
        <ul>
            <li><a href="/" class="active">蛋壳公寓</a></li>
            <li>
                <a href="{{ route('rooms', '') }}">我要租房</a>
            </li>
            <li>
                <a href="{{ route('rooms', '') . '?' . http_build_query(['search'=>1, 'search_text'=>'月租房源']) }}">月租房源</a>
            </li>
            <li>
                <a href="{{ action('HomeController@about', 'notice') }}/">租前必读</a>
            </li>
            <li>
                <a href="{{ action('HomeController@about', 'yezhu') }}/">业主加盟</a>
            </li>
            <li>
                <a href="{{ action('HomeController@about', 'aboutus') }}/">关于蛋壳</a>
            </li>
        </ul>
    </div>
@endsection
@section('main')
    <div class="erro404">
        <div class="wrapper">
            <img src="http://public.wutongwan.org/public-20170122-FnXUodxObjVXvAuT3EWzWrJ9yt6s">
            <h3>糟糕！</h3>
            <span>我们似乎无法找到您要找的页面。</span>
            <label>错误代码：404</label>
            <p>下面是一些有用的链接：</p>
            <div class="link404">
                <b><a href="/">官网首页</a></b>
                <b><a href="{{ route('rooms', '') }}">所有房源</a></b>
                <b><a href="{{ action('HomeController@about', 'yezhu') }}/">业主加盟</a></b>
                <b><a href="{{ action('HomeController@about', 'aboutus') }}/">关于蛋壳</a></b>
            </div>
        </div>
    </div>
    <div class="wrapper">
        <div class="room_nearbox">
            <div class="title_fris">
                <h2 class="cn">猜你喜欢</h2>
            </div>
            <div class="lk_room_box">
            </div>
        </div>
    </div>
    <script>
        @include('widget.ajax_setup')
        $(function () {
            $.post("{{ action("HomeController@showRecommendRooms") }}", function (data) {
                var ohtml = '';
                for (var i in data.data) {
                    ohtml += '<dl>' +
                            '<dt>' +
                            '<a href="http://www.dankegongyu.com/room/' + data.data[i].id + '.html">' +
                            '<img src=" ' + data.data[i].img + '" width="300" title="' + data.data[i].address_super_title + '" alt="' + data.data[i].address_super_title + '图片"/>' +
                            '<div class="month_y">' + data.data[i].price + '<span>元/月</span>' +
                            '</div>' +
                            '</a>' +
                            '</dt>' +
                            '<dd>' +
                            '<a href="http://www.dankegongyu.com/room/' + data.data[i].id + '.html">' + data.data[i].address_super_title + '</a>' +
                            '<p>' + data.data[i].address_sub_title + '</p>' +
                            '</dd>' +
                            '</dl>';
                }
                $('.lk_room_box').append(ohtml);
            })
        });
    </script>
    <style>
        .erro404 {
            display: block;
            overflow: hidden;
            padding: 50px 0 65px;
            border-bottom: 1px solid #eee;
        }

        .erro404 img {
            float: left;
            margin: 0 0 0 70px;
        }

        .erro404 h3, .erro404 span, .erro404 label, .erro404 p, .erro404 .link404 {
            display: block;
            margin: 0 0 0 670px;
        }

        .link404 b {
            display: block;
        }

        .erro404 h3 {
            padding: 55px 0 0;
            line-height: 85px;
            font-size: 60px;
        }

        .erro404 span {
            line-height: 45px;
            font-size: 30px;
        }

        .erro404 label {
            color: #666;
            font-weight: normal;
            padding: 0 0 25px;
            font-size: 16px;
            line-height: 35px;
        }

        .erro404 p {
            font-size: 20px;
            line-height: 30px;
            padding: 0 0 10px;
        }

        .erro404 a {
            font-weight: normal;
            font-size: 20px;
            line-height: 30px;
            color: #008489;
        }

        .erro404 a:hover {
            text-decoration: underline;
        }

        .link404 {
            display: block;
            overflow: hidden;
        }

        .lk_room_box dl {
            margin-right: 60px;
        }

        @media (max-width: 660px) {
            .wrapper {
                width: 100%;
            }

            .room_nearbox {
                display: none;
            }

            .erro404 {
                padding: 20px 0;
            }

            .erro404 img {
                float: none;
                width: 80%;
                margin: 10px 10%;
            }

            .erro404 h3, .erro404 span, .erro404 label, .erro404 p, .erro404 b {
                margin: 0 10px;
            }

            .erro404 h3 {
                padding: 0;
                line-height: 35px;
                font-size: 20px;
            }

            .erro404 span {
                line-height: 25px;
                font-size: 16px;
            }

            .erro404 label {
                font-size: 12px;
                padding: 0 0 10px;
                line-height: 20px;
            }

            .erro404 p {
                font-size: 16px;
                line-height: 20px;
            }

            .erro404 b {
                float: left;
                width: 23%;
                margin: 0 1%;
            }

            .erro404 b a {
                display: block;
                text-align: center;
                font-size: 14px;
                color: #fff;
                background: #008489;
                -webkit-border-radius: 4px;
                -moz-border-radius: 4px;
                border-radius: 4px;
            }

            .erro404 b a:hover {
                text-decoration: none;
            }

            .footer {
                display: block;
            }

            .erro404 .link404 {
                display: block;
                overflow: hidden;
                margin: 0 10px;
            }

        }
    </style>
@endsection