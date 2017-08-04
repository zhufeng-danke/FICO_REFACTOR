# 来源说明
Copy From : https://github.com/dodgepudding/wechat-php-sdk

# 为啥 Copy ？
原作者提供的 qywechat.class.php wecaht.class.php 中 ClassName 都叫 Wechat ，导致同时引用时候会报错，好恶心！

# 修改了什么。

没有做任何具体的代码修改，只是把4个文件放到对应目录下面，并且规定了不同的 NameSpace。   
下次更新代码的时候只要 copy 新的进来，并添加顶部 NameSpace 行即可。

- qywechat.class.php -> Qy/Wechat.php
- qyerrCode.php      -> Qy/ErrCode.php
- wechat.class.php   -> Mp/Wechat.php
- errCode.php        -> Mp/ErrCode.php