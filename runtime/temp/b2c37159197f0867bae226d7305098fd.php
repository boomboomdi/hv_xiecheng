<?php /*a:1:{s:70:"D:\phpStudy\PHPTutorial\WWW\dvpay\application\api\view\test\index.html";i:1641724119;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>测试下单</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/merchant/style/admin.css" media="all">
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
                                <label class="layui-form-label">订单号：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="order_me" lay-verify="required" value="<?php echo htmlentities($order_me); ?>" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">金额：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="amount" lay-verify="required" autocomplete="off" class="layui-input">
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
        base: '/static/merchant/' //静态资源所在路径
    }).use(['form'], function(){
        var $ = layui.$
            ,merchant = layui.merchant
            ,element = layui.element
            ,form = layui.form;

        form.on('submit(component-form-element)', function(data){

            $.post("<?php echo url('test/index'); ?>", data.field, function (res) {

                if(0 == res.code) {

                    layer.msg(res.msg);
                    setTimeout(function () {

                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        window.parent.renderTable();
                    }, 200);
                } else {

                    layer.alert(res.msg, {
                        'title': '下单成功',
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