<?php /*a:1:{s:71:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\admin\view\index\home.html";i:1653485808;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>主页{</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/admin/lib/layui-v2.5.4/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/lib/font-awesome-4.7.0/css/font-awesome.min.css" media="all">
    <link rel="stylesheet" href="/static/admin/css/public.css" media="all">
</head>
<style>
    .layui-top-box {
        padding: 40px 20px 20px 20px;
        color: #fff
    }

    .panel {
        margin-bottom: 17px;
        background-color: #fff;
        border: 1px solid transparent;
        border-radius: 3px;
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
        box-shadow: 0 1px 1px rgba(0, 0, 0, .05)
    }

    .panel-body {
        padding: 15px
    }

    .panel-title {
        margin-top: 0;
        margin-bottom: 0;
        font-size: 14px;
        color: inherit
    }

    .label {
        display: inline;
        padding: .2em .6em .3em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25em;
        margin-top: .3em;
    }

    .layui-red {
        color: red
    }

    .main_btn > p {
        height: 40px;
    }
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main layui-top-box">
        <div class="layui-row layui-col-space10">

            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel layui-bg-cyan">
                        <div class="panel-body">
                            <div class="panel-title">
                                <span class="label pull-right layui-bg-blue">统计截至:<?php echo htmlentities($endTime); ?></span>
                                <h5>订单统计</h5>
                            </div>
                            <div class="panel-content">
                                <h2 class="no-margins">订单总数:<?php echo htmlentities($orderNum); ?></h2>
                                <h2 class="no-margins">支付单数:<?php echo htmlentities($payOrderNum); ?></h2>
                                <h2 class="no-margins">手动回调:<?php echo htmlentities($notifyPayOrderNum); ?></h2>
                                <h2 class="no-margins">回调金额<?php echo htmlentities($payOrderAmount); ?></h2>
<!--                                <div class="stat-percent font-bold text-gray"><i class="fa fa-commenting"></i>回调金额<?php echo htmlentities($payOrderAmount); ?>-->
<!--                                </div>-->

                                <h2 class="no-margins">成功率:<?php echo htmlentities($successOrderRate); ?></h2>
<!--                                <small>成功率:<?php echo htmlentities($successOrderRate); ?></small>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel layui-bg-blue">
                        <div class="panel-body">
                            <div class="panel-title">
                                <span class="label pull-right layui-bg-blue">统计截至:<?php echo htmlentities($endTime); ?></span>
                                <h5>核销单统计</h5>
                            </div>
                            <div class="panel-content">
                                <h2 class="no-margins">总单数量:<?php echo htmlentities($tOrderNum); ?></h2>
                                <h2 class="no-margins">使用单数:<?php echo htmlentities($usedTOrderNum); ?></h2>
                                <h2 class="no-margins">可用单数:<?php echo htmlentities($canUseTOrderNum); ?></h2>
                                <h2 class="no-margins">支付单数:<?php echo htmlentities($payTOrderNum); ?></h2>
                                <h2 class="no-margins">总成功率:<?php echo htmlentities($successTOrderRate); ?></h2>
<!--                                <div class="stat-percent font-bold text-gray"><i class="fa fa-commenting"></i> 1234-->
<!--                                </div>-->
<!--                                <small>当前分类总记录数</small>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!--    <div class="layui-box">-->
    <!--        <div class="layui-row layui-col-space12">-->

    <!--            <fieldset class="layui-elem-field">-->
    <!--                <legend>系统信息</legend>-->
    <!--                <div class="layui-field-box">-->
    <!--                    <table class="layui-table">-->
    <!--                        <tbody>-->
    <!--                        <tr>-->
    <!--                            <th>服务器IP</th>-->
    <!--                            <td><?php echo getHostByName(request()->host()); ?></td>-->
    <!--                        </tr>-->
    <!--                        <tr>-->
    <!--                            <th>操作系统</th>-->
    <!--                            <td><?php echo PHP_OS; ?></td>-->
    <!--                        </tr>-->
    <!--                        <tr>-->
    <!--                            <th>运行环境</th>-->
    <!--                            <td><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></td>-->
    <!--                        </tr>-->
    <!--                        <tr>-->
    <!--                            <th>PHP版本</th>-->
    <!--                            <td><?php echo PHP_VERSION; ?></td>-->
    <!--                        </tr>-->
    <!--                        <tr>-->
    <!--                            <th>PHP运行方式</th>-->
    <!--                            <td><?php echo php_sapi_name(); ?></td>-->
    <!--                        </tr>-->
    <!--                        <tr>-->
    <!--                            <th>MYSQL版本</th>-->
    <!--                            <td><?php echo getMysqlVersion(); ?></td>-->
    <!--                        </tr>-->
    <!--                        <tr>-->
    <!--                            <th>ThinkPHP</th>-->
    <!--                            <td><?php echo htmlentities($tp_version); ?></td>-->
    <!--                        </tr>-->
    <!--                        <tr>-->
    <!--                            <th>上传附件限制</th>-->
    <!--                            <td><?php echo ini_get("file_uploads"); ?>M</td>-->
    <!--                        </tr>-->
    <!--                        <tr>-->
    <!--                            <th>执行时间限制</th>-->
    <!--                            <td><?php echo ini_get("max_execution_time"); ?>s</td>-->
    <!--                        </tr>-->
    <!--                        </tbody>-->
    <!--                    </table>-->
    <!--                </div>-->
    <!--            </fieldset>-->
    <!--        </div>-->
    <!--    </div>-->
</div>
<script src="/static/admin/lib/layui-v2.5.4/layui.js" charset="utf-8"></script>
</body>
</html>