<?php /*a:1:{s:87:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\admin\view\writeoff\addwriteoffamount.html";i:1709143572;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>添加余额</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/style/admin.css" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <form class="layui-form" action="" lay-filter="component-form-element">
                        <input type="hidden" name="write_off_id" value="<?php echo htmlentities($writeOff['write_off_id']); ?>"/>
                        <div class="layui-row layui-col-space10 layui-form-item">

                            <div class="layui-col-lg6">
                                <label class="layui-form-label">核商标识：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="write_off_sign" lay-verify="required" placeholder=""
                                           readonly
                                           autocomplete="off" class="layui-input" value="<?php echo htmlentities($writeOff['write_off_sign']); ?>">
                                </div>
                            </div>

                            <div class="layui-col-lg6">
                                <label class="layui-form-label">当前余额：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="write_off_deposit" lay-verify="required"
                                           value="<?php echo htmlentities($writeOff['write_off_deposit']); ?>"
                                           readonly
                                           autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">添加金额：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="add_write_off_deposit" lay-verify="required" placeholder=""
                                           autocomplete="off" class="layui-input" value="">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">操作密钥：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="token" placeholder=""
                                           autocomplete="off" class="layui-input" value="">
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button class="layui-btn" lay-submit lay-filter="component-form-element">立即提交
                                    </button>
                                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/static/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/admin/' //静态资源所在路径
    }).use(['form'], function () {
        var $ = layui.$
            , admin = layui.admin
            , element = layui.element
            , form = layui.form;

        form.on('submit(component-form-element)', function (data) {

            $.post("<?php echo url('writeoff/addWriteOffAmount'); ?>", data.field, function (res) {

                if (0 == res.code) {

                    layer.msg(res.msg);
                    setTimeout(function () {

                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        window.parent.renderTable();
                    }, 200);
                } else {

                    layer.alert(res.msg, {
                        'title': '添加错误',
                        'icon': 2
                    });
                }
            }, 'json');
            return false;
        });
    });
</script>
</body>
</html>