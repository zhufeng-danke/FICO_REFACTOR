<?
//  参数:
/* @var string $src 发短信的请求链接。请求方式: POST {$src}/{$mobile} */

$id = md5($src);
?>

<a id="{{ $id }}" href="javascript:0;" class="btn btn-default btn-sm">获取验证码</a>

<script>
    $(window).ready(function () {
        var allowCall = true;
        var $codeBtn = $('#{{$id}}');
        var countDown = function (seconds) {
            $codeBtn.css('cursor', 'not-allowed');
            seconds -= 1;
            if (seconds > 0) {
                $codeBtn.text(seconds + '秒后重试');
                setTimeout(function () {
                    countDown(seconds);
                }, 1000);
            } else {
                allowCall = true;
                $codeBtn.css('cursor', 'pointer');
                $codeBtn.text('获取验证码');
            }
        };

        $codeBtn.on('click', function () {
            if (allowCall !== true) {
                return false;
            }

            var imgCode = $(this).parents('form').find('input[name=img_code]').val();
            if (!imgCode) {
                alert('请输入图片验证码内容');
                return false;
            }

            var mobile = $('#mobile').val();
            if (!mobile) {
                alert('手机号码不能为空！');
                return false;
            }

            @include('widget.ajax_setup')
            $.ajax({
                type: "POST",
                url: '{{ $src }}/' + mobile + '/' + imgCode,
                async: false,
                error: function (msg) {
                    alert("提交失败，请退出重试。");
                },
                success: function (data) {
                    if (data['success'] === true) {
                        allowCall = false;
                        countDown(60);
                    }
                    if (data['msg']) {
                        alert(data['msg']);
                    }
                }
            });
        });
    });
</script>
