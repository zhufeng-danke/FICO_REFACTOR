{{-- 容联云客服的在线客服代码
参数:
    $type 非必填, 参数可选值见下方 $tokens
--}}
<?
$tokens = [
        'default' => '71debe60-0e04-11e6-bc3c-bdd89c47cbec',    // 官网
        'pre-sale' => 'fb9f3ac0-278c-11e6-81bd-094430c19b4b',   // 售前
        'after-sale' => '48d7c0f0-278d-11e6-81bd-094430c19b4b', // 售后
];
$token = array_get($tokens, $type ?? 'default');
?>

@if(isProduction())
    @if($user = Auth::user())
        <script type='text/javascript'>
            var qimoClientId = {
                userId: 'U{{ $user->id }}',
                nickName:'{{ $user->nickname }}{{ $user->mobile ? " - {$user->mobile}" : null }}'
            };
        </script>
    @endif

    @if(UserAgent::isPC())
        <script type='text/javascript'
                src='http://webchat.7moor.com/javascripts/7moorInit.js?accessId={{ $token }}&autoShow=true'
                async='async'>
        </script>
    @else
        <script type='text/javascript'
                src='http://webchat.7moor.com/javascripts/7moorInit.js?accessId={{ $token }}&autoShow=false'
                async='async'>
        </script>
        <style>
            #btn-7moor {
                position: fixed;
                right: 0;
                bottom: 10%;
                background-color: #19CAA6;
                color: #FFFFFF;
                text-align: center;
            }
        </style>
        <button id="btn-7moor" class="btn" onclick="qimoChatClick()">咨<br>询<br>客<br>服</button>
    @endif
@endif
