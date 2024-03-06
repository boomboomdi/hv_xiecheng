<?php /*a:1:{s:70:"D:\phpStudy\PHPTutorial\WWW\dvvv\application\admin\view\node\edit.html";i:1635833793;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>编辑节点</title>
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
                        <input type="hidden" name="node_id" value="<?php echo htmlentities($node_info['node_id']); ?>"/>
                        <input type="hidden" name="node_pid" value="<?php echo htmlentities($node_info['node_pid']); ?>"/>
                        <div class="layui-row layui-col-space10 layui-form-item">
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">上级节点：</label>
                                <div class="layui-input-block">
                                    <input type="text" autocomplete="off" class="layui-input" value="<?php echo htmlentities($p_node); ?>" readonly style="background: #e2e2e2">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">节点名称：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="node_name" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" value="<?php echo htmlentities($node_info['node_name']); ?>">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">节点路径：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="node_path" lay-verify="required" placeholder="manager/addAdmin" autocomplete="off" class="layui-input" value="<?php echo htmlentities($node_info['node_path']); ?>">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">节点图标：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="node_icon" placeholder="layui-icon layui-icon-template" autocomplete="off" class="layui-input" value="<?php echo htmlentities($node_info['node_icon']); ?>">
                                </div>
                            </div>
                            <div class="layui-col-lg6">
                                <label class="layui-form-label">是否菜单：</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_menu" value="1" title="否" <?php if($node_info['is_menu'] == 1): ?>checked<?php endif; ?>>
                                    <input type="radio" name="is_menu" value="2" title="是" <?php if($node_info['is_menu'] == 2): ?>checked<?php endif; ?>>
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
<script src="/static/common/js/layTool.js"></script>

<script>
    layui.config({
        base: '/static/admin/' //静态资源所在路径
    }).use(['form'], function(){
        var $ = layui.$
            ,layer = layui.layer
            ,form = layui.form;

        form.on('submit(component-form-element)', function(data){

            $.post("<?php echo url('node/edit'); ?>", data.field, function (res) {

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