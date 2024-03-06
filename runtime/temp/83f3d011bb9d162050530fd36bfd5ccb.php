<?php /*a:1:{s:75:"D:\phpStudy\PHPTutorial\WWW\dvpay\application\admin\view\merchanta\add.html";i:1641723890;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>添加商户商户接口</title>
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
                        <div class="layui-row layui-col-space10 layui-form-item">
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">商户名称：</label>
                                <div class="layui-input-block">
                                    <input type="hidden" name="merchant_name" value="<?php echo htmlentities($merchant['merchant_sign']); ?>">
<!--                                    <input type="hidden" name="id" value="<?php echo htmlentities($merchant['merchant_sign']); ?>">-->
                                    <?php echo htmlentities($merchant['merchant_name']); ?>
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">商户标识：</label>
                                <div class="layui-input-block">
                                    <input type="hidden" name="merchant_sign" value="<?php echo htmlentities($merchant['merchant_sign']); ?>">

                                    <?php echo htmlentities($merchant['merchant_sign']); ?>
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">通道选择</label>
                                <div class="layui-input-block">
                                    <select name="api_sign" lay-verify="required">
                                        <option value=""></option>
                                        <?php if(!empty($payapis)): if(is_array($payapis) || $payapis instanceof \think\Collection || $payapis instanceof \think\Paginator): if( count($payapis)==0 ) : echo "" ;else: foreach($payapis as $key=>$vo): ?>
                                        <option value="<?php echo htmlentities($vo['api_sign']); ?>"><?php echo htmlentities($vo['api_name']); ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">费率：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="rate" lay-verify="required" placeholder="费率(例如百分之三点八:3.8)" autocomplete="off" class="layui-input">
                                </div>
                            </div>

                            <div class="layui-col-lg6">
                                <label class="layui-form-label">是否启用：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="status" value="1" title="启用" checked>
                                    <input type="radio" name="status" value="0" title="禁用">
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
            ,merchant = layui.merchant
            ,element = layui.element
            ,form = layui.form;

        form.on('submit(component-form-element)', function(data){

            $.post("<?php echo url('merchanta/addmerchantapi'); ?>", data.field, function (res) {

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