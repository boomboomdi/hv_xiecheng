<?php /*a:1:{s:72:"D:\phpStudy\PHPTutorial\WWW\dvvv\application\admin\view\device\edit.html";i:1648220555;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑设备</title>
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
                        <input type="hidden" name="id" value="<?php echo htmlentities($device['id']); ?>"/>
                        <input type="hidden" name="studio" value="<?php echo htmlentities($device['studio']); ?>"/>
                        <div class="layui-row layui-col-space10 layui-form-item">
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">账户：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="account" lay-verify="required" placeholder=""
                                           autocomplete="off" class="layui-input" value="<?php echo htmlentities($device['account']); ?>">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">密码：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="account_password" lay-verify="required" placeholder=""
                                           autocomplete="off" class="layui-input" value="<?php echo htmlentities($device['account_password']); ?>">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">链接：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="qr_url" lay-verify="required" placeholder="解析链接"
                                           autocomplete="off" class="layui-input" value="<?php echo htmlentities($device['qr_url']); ?>">
                                </div>
                            </div>

                            <div class="layui-col-lg6">
                                <label class="layui-form-label">二维码：</label>
                                <input name="thumbnail" id="thumbnail" type="hidden" value="<?php echo htmlentities($device['thumbnail']); ?>"/>
                                <div class="form-inline">
                                    <div class="input-group col-sm-2">
                                        <button type="button" class="layui-btn" id="test1">
                                            <i class="layui-icon">&#xe67c;</i>上传图片
                                        </button>
                                    </div>
                                    <div class="input-group col-sm-3">
                                        <div id="sm">
                                            <img src="<?php echo htmlentities($device['thumbnail']); ?>" width="40px" height="40px"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">描述：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="device_desc" placeholder="输入则为重置" autocomplete="off"
                                           class="layui-input">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">是否启用：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="status" value="1" title="启用" <?php if($device['status'] == 1): ?>
                                    checked <?php endif; ?>>
                                    <input type="radio" name="status" value="2" title="禁用" <?php if($device['status'] == 2): ?>
                                    checked <?php endif; ?>>
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
    }).use(['form'], function () {
        var $ = layui.$
            , admin = layui.admin
            , element = layui.element
            , form = layui.form;

        // 上传图片
        layui.use('upload', function () {
            var upload = layui.upload;

            //执行实例
            var uploadInst = upload.render({
                elem: '#test1' //绑定元素
                , url: "<?php echo url('device/uploadQrImg'); ?>" //上传接口
                , done: function (res) {
                    //上传完毕回调
                    $("#thumbnail").val(res.data.src);
                    $("#sm").html('<img src="' + res.data.src + '" style="width:40px;height: 40px;"/>');
                }
                , error: function () {
                    //请求异常回调
                }
            });
        });

        form.on('submit(component-form-element)', function (data) {

            $.post("<?php echo url('device/editDevice'); ?>", data.field, function (res) {

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