<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Set render engine for multi engine browser -->
    <meta name="renderer" content="webkit">
    <!-- Disable Baidu Siteapp -->
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <title>@yield('title')</title>
    @css('bower/bootstrap/dist/css/bootstrap.min.css')
    @css('inspinia/css/style.css')
    <style>
        body {
            padding-top: 100px;
            height: 100%;
        }

        #main {
            margin: 0 auto;
            float: none;
            word-break: break-all;
        }

        .panel-body > .lead {
            padding-top: 2em;
            padding-bottom: 2em;
        }

        .panel-body > small {
            color: #999999;
        }
    </style>
</head>

<body class="gray-bg">
<div class="col-md-6" id="main">
    @yield('content')
</div>
</body>
</html>
