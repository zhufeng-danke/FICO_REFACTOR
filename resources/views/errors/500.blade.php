<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <!-- Set render engine for multi engine browser -->
    <meta name="renderer" content="webkit">
    <!-- Disable Baidu Siteapp -->
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <title>500 内部错误.</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            color: #666666;
            display: table;
            font-weight: 100;
        }

        .container {
            text-align: center;
            margin-top: 20%;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 1.5em;
            margin-bottom: 1em;
        }

        .code {
            white-space: -moz-pre-wrap;
            white-space: -o-pre-wrap;
            white-space: pre-wrap;
            word-wrap: break-word; /* Internet Explorer 5.5+ */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="title">系统内部错误<br>请截图并联系技术团队</div>
        <p class="code">{{ URL::full() }}</p>
        <p><i><small>时间: {{ date('Y-m-d H:i:s') }}</small></i></p>
    </div>
</div>
</body>
</html>
