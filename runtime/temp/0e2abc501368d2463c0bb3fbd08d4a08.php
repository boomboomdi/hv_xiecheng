<?php /*a:1:{s:73:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\admin\view\walmart\edit.html";i:1706164919;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑卡种</title>
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
                        <input type="hidden" name="id" value="<?php echo htmlentities($camiChannelData['id']); ?>"/>
                        <input type="hidden" name="cami_type_id" value="<?php echo htmlentities($camiChannelData['cami_type_id']); ?>"/>
                        <div class="layui-row layui-col-space10 layui-form-item">

                            <div class="layui-col-lg6">
                                <label class="layui-form-label">卡种标识：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="cami_type_sign" lay-verify="required" placeholder="" readonly
                                           autocomplete="off" class="layui-input" value="Walmart">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">卡种名称：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="cami_type_username" lay-verify="required" readonly
                                           placeholder="协议密匙"
                                           autocomplete="off" class="layui-input" value="沃尔玛">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">选择核销：</label>
                                <div class="layui-input-block">
                                    <select name="write_off_id" lay-verify="required">
                                        <?php if(!empty($writeOffData)): if(is_array($writeOffData) || $writeOffData instanceof \think\Collection || $writeOffData instanceof \think\Paginator): if( count($writeOffData)==0 ) : echo "" ;else: foreach($writeOffData as $key=>$vo): ?>
                                        <option value="<?php echo htmlentities($vo['write_off_id']); ?>" <?php if($camiChannelData['write_off_id'] == $vo['write_off_id']): ?>
                                        selected <?php endif; ?>><?php echo htmlentities($vo['write_off_sign']); ?></option>

                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">派单权重：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="weight" lay-verify="required"  placeholder="请输入1-9"
                                           autocomplete="off" class="layui-input" value="<?php echo htmlentities($camiChannelData['weight']); ?>">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">费率调整：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="weight" lay-verify="required" placeholder="百分值5就是0.05,最低0.001"
                                           autocomplete="off" class="layui-input" value="<?php echo htmlentities($camiChannelData['rate']); ?>">
                                </div>
                            </div>
                            <div class="layui-col-lg6">

                                <label class="layui-form-label">是否启用：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="status" value="1" title="启用" <?php if($camiChannelData['status'] == 1): ?>
                                    checked <?php endif; ?>>
                                    <input type="radio" name="status" value="2" title="禁用" <?php if($camiChannelData['status'] == 2): ?>
                                    checked <?php endif; ?>>
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

            $.post("<?php echo url('walmart/editCamitypechannel'); ?>", data.field, function (res) {

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