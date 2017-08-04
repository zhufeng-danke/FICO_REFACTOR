<?
$id = md5(microtime());
?>

<img id="{{ $id }}" src="{{ Captcha::instance()->src() }}" alt="图片验证码">

<script>
    $('#{{$id}}').click(function () {
        $(this).attr('src');
    });
</script>
