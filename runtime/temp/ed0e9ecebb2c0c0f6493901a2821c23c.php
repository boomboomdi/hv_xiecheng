<?php /*a:1:{s:69:"D:\phpStudy\PHPTutorial\WWW\dvvv\application\admin\view\role\add.html";i:1635833793;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>添加角色</title>
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
                                <label class="layui-form-label">角色名称：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="role_name" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
                                </div>
                            </div>

                            <div class="layui-col-lg6">
                                <label class="layui-form-label">是否启用：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="role_status" value="1" title="启用" checked>
                                    <input type="radio" name="role_status" value="2" title="禁用">
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
<script src="/static/common/js/jquery.min.js"></script>
<script src="/static/common/js/layTool.js"></script>

<script>
    layui.config({
        base: '/static/admin/'
    }).use(['form'], function(){
        var $ = layui.$
            ,admin = layui.admin
            ,element = layui.element
            ,form = layui.form;

        form.on('submit(component-form-element)', function(data){

            $.post("<?php echo url('role/add'); ?>", data.field, function (res) {

                if(0 == res.code) {

                    layer.msg(res.msg);
                    setTimeout(function () {

                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.close(index);
                        window.parent.renderTable();
                    }, 200);
                } else {
                    layTool.alert(res.msg, '友情提示', 2);
                }
            }, 'json');
            return false;
        });
    });
</script>
</body>
</html>