<?php /*a:1:{s:74:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\admin\view\merchant\edit.html";i:1654045269;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑商户员</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
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
                        <input type="hidden" name="merchant_id" value="<?php echo htmlentities($merchant['merchant_id']); ?>"/>
                        <div class="layui-row layui-col-space10 layui-form-item">
<!--                            <div class="layui-col-lg6">-->
<!--                                <label class="layui-form-label">商户名称：</label>-->
<!--                                <div class="layui-input-block">-->
<!--                                    <input type="text" name="merchant_name" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" value="<?php echo htmlentities($merchant['merchant_name']); ?>">-->
<!--                                </div>-->
<!--                            </div>-->
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">商户标识：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="merchant_sign" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" value="<?php echo htmlentities($merchant['merchant_sign']); ?>">
                                </div>
                            </div>
<!--                            <div class="layui-col-lg6">-->
<!--                                <label class="layui-form-label">登录名称：</label>-->
<!--                                <div class="layui-input-block">-->
<!--                                    <input type="text" name="merchant_username" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" value="<?php echo htmlentities($merchant['merchant_username']); ?>">-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="layui-col-lg6">-->
<!--                                <label class="layui-form-label">登录密码：</label>-->
<!--                                <div class="layui-input-block">-->
<!--                                    <input type="text" name="merchant_password" placeholder="输入则为重置" autocomplete="off" class="layui-input">-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="layui-col-lg6">-->
<!--                                <label class="layui-form-label">回调地址：</label>-->
<!--                                <div class="layui-input-block">-->
<!--                                    <input type="text" name="notify_url" placeholder="" autocomplete="off" class="layui-input" value="<?php echo htmlentities($merchant['notify_url']); ?>">-->
<!--                                </div>-->
<!--                            </div>-->
                            <div class="layui-col-lg6">
                                <div class="layui-col-lg6">
                                    <label class="layui-form-label">是否启用：</label>
                                    <div class="layui-input-block">
                                        <input type="radio" name="status" value="1" title="启用" <?php if($merchant['status'] == 1): ?> checked <?php endif; ?>>
                                        <input type="radio" name="status" value="0" title="禁用" <?php if($merchant['status'] == 0): ?> checked <?php endif; ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button class="layui-btn" lay-submit lay-filter="component-form-element">立即提交</button>
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
    }).use(['form'], function(){
        var $ = layui.$
            ,admin = layui.admin
            ,element = layui.element
            ,form = layui.form;

        form.on('submit(component-form-element)', function(data){

            $.post("<?php echo url('merchant/editMerchant'); ?>", data.field, function (res) {

                if(0 == res.code) {

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