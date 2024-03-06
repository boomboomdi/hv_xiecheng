<?php /*a:1:{s:73:"D:\phpStudy\PHPTutorial\WWW\dvpay\application\index\view\index\index.html";i:1640164843;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>进入后台</title>
    <style>
        html,body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }
        p {
            margin: 0;
            padding: 0;
        }
        a {
            text-decoration:none;
            color: rgb(42, 131, 255);
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e2e2e2;
        }
        .content {
            width: 500px;
            height: 400px;
            background: #fff;
            box-shadow: 10px 10px 5px #888888;
        }
        .header {
            height: 50px;
            width: 100%;
            background: #1E9FFF;
            line-height: 50px;
            text-align: left;
        }
        .header span {
            padding-left: 50px;
            font-size: 16px;
            color: #fff;
        }
        .license {
            padding: 20px 20px;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="header">
            <span>欢迎使用</span>
        </div>
        <div class="license">
            <a href="/admin/index" style="margin-top: 10px;display: inline-block">点击此处进入后台</a>
<!--            <p style="margin-top: 10px;">或者在域名后直接输入 /admin 进入后台</p>-->
<!--            <p style="margin-top: 10px;">默认用户名、密码  admin  admin</p>-->
        </div>
    </div>
</body>
</html>
