<?php /*a:1:{s:81:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\admin\view\writeoff\stoporderhx.html";i:1653485808;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>核销止付</title>
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
                        <div class="layui-row layui-col-space10 layui-form-item">
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">核商标识：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="write_off_sign" disabled="disabled"
                                           value="<?php echo htmlentities($writeOff['write_off_sign']); ?>"
                                           autocomplete="off" class="layui-input">
                                </div>
                            </div>

                            <div class="layui-inline">
                                <label class="layui-form-label">运营商</label>
                                <div class="layui-input-block">
                                    <select name="operator" lay-verify="required">
                                        <option value="">请选择</option>
                                        <option value="联通">联通</option>
                                        <option value="电信">电信</option>
                                        <option value="移动">移动</option>
                                    </select>
                                </div>
                            </div>

                            <div class="layui-inline">
                                <label class="layui-form-label">时间截至</label>
                                <div class="layui-input-block">
                                    <input type="text" name="endTime" placeholder="时间截至" autocomplete="off"
                                           class="layui-input" id="endTime">
                                </div>
                            </div>
<!--                            <div class="layui-col-lg6">-->
<!--                                <label class="layui-form-label">请确认：</label>-->
<!--                                <div class="layui-input-block">-->
<!--                                    <input type="radio" name="status" value="1" title="确定">-->
<!--                                    <input type="radio" name="status" value="2" title="否定" checked>-->
<!--                                </div>-->
<!--                            </div>-->
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn" lay-submit lay-filter="component-form-element">立即提交</button>
                                <!--                                <button type="reset" class="layui-btn layui-btn-primary">重置</button>-->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="/static/layui/layui.js"></script>
<script src="/static/common/js/layTool.js"></script>
<script src="/static/common/js/jquery.min.js"></script>
<script>

    layui.config({
        base: '/static/admin/' //静态资源所在路径
    }).use(['form'], function () {
        var $ = layui.$
            , admin = layui.admin
            , element = layui.element
            , form = layui.form;


        form.on('submit(component-form-element)', function (data) {

            $.post("<?php echo url('writeoff/stopOrderHx'); ?>", data.field, function (res) {

                if (0 == res.code) {

                    layer.alert(res.msg, {
                        'title': res.msg,
                        'icon': 2
                    });
                    // layer.msg(res.msg);
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

    layTool.layDate('#endTime');
</script>
</body>
</html>