<?php /*a:1:{s:74:"D:\phpStudy\PHPTutorial\WWW\dvpay\application\admin\view\payapi\index.html";i:1646921846;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>支付接口列表</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/style/admin.css" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">

                <div class="layui-inline">
                    <label class="layui-form-label">接口名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="merchant_name" placeholder="请输入" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-admin" lay-submit lay-filter="LAY-user-back-search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="layui-card-body">
            <?php if((buttonAuth('payapi/addpayapi'))): ?>
            <div style="padding-bottom: 10px;">
                <button class="layui-btn layuiadmin-btn-admin" data-type="add"><i class="layui-icon">&#xe654;</i> 添加</button>
            </div>
            <?php endif; ?>
            <table id="LAY-user-table" lay-filter="LAY-user-table"></table>
            <script type="text/html" id="statusTpl">
                {{#  if(d.status == 1){ }}
                <button class="layui-btn layui-btn-success layui-btn-xs">启用</button>
                {{#  } else { }}
                <button class="layui-btn layui-btn-danger layui-btn-xs">禁用</button>
                {{#  } }}
            </script>
            <script type="text/html" id="table-seller-admin">
                <?php if((buttonAuth('payapi/editpayapi'))): ?>
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>
                <?php endif; if((buttonAuth('payapi/delpayapi'))): ?>
                {{#  if(d.admin_id == '1'){ }}
                <a class="layui-btn layui-btn-disabled layui-btn-xs"><i class="layui-icon layui-icon-delete"></i>删除</a>
                {{#  } else { }}
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
                {{#  } }}
                <?php endif; ?>
            </script>
        </div>
    </div>
</div>

<script src="/static/layui/layui.js"></script>
<script src="/static/common/js/jquery.min.js"></script>
<script src="/static/common/js/layTool.js"></script>
<script>
    layui.config({
        base: '/static/admin/'
    }).use(['table'], function(){
        var $ = layui.$
            ,form = layui.form
            ,table = layui.table;

        var active = {
            add: function() {
                layTool.open( "<?php echo url('payapi/addpayapi'); ?>", "添加接口", '50%', '50%');
            }
        };

        $('.layui-btn.layuiadmin-btn-admin').on('click', function(){
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });

        // 监听搜索
        form.on('submit(LAY-user-back-search)', function(data){
            var field = data.field;

            // 执行重载
            table.reload('LAY-user-table', {
                where: field
            });
        });
    });

    renderTable();
    // 渲染表格
    function renderTable() {

        layTool.table("#LAY-user-table", "/admin/payapi/index", [
            [{
                field: "api_name",
                title: "接口名称"
            }, {
                field: "api_sign",
                title: "接口标识"
            }, {
                field: "payment",
                title: "支付方式"
            }, {
                field: "api_url",
                title: "接口URL"
            }, {
                field: "status",
                title: "支付状态",
                templet: '#statusTpl'
            }, {
                field: "api_token",
                title: "密钥"
            },  {
                field: "add_time",
                title: "添加时间"
            }, {
                field: "update_time",
                title: "修改时间"
            }, {
                field: "api_desc",
                title: "接口描述"
            }, {
                title: "操作",
                align: "center",
                width: 150,
                fixed: "right",
                toolbar: "#table-seller-admin"
            }]
        ], 20);
    }
    layui.use(['table', 'layer'], function () {
        let layer = layui.layer;
        let table = layui.table;

        table.on("tool(LAY-user-table)",
            function(e) {
                if ("del" === e.event) {

                    layer.ready(function () {
                        var index = layer.confirm('您确定要删除该接口？', {
                            title: '友情提示',
                            icon: 3,
                            btn: ['确定', '取消']
                        }, function() {

                            $.getJSON('<?php echo url("payapi/delpayapi"); ?>', {id: e.data.id}, function (res) {

                                if(0 == res.code) {

                                    layer.msg(res.msg);
                                    setTimeout(function () {
                                        renderTable();
                                    }, 300);
                                } else {
                                    layer.alert(res.msg);
                                }
                            });
                        }, function(){

                        });
                    });
                } else if ("edit" === e.event) {

                    layTool.open("/admin/payapi/editpayapi/id/" + e.data.id, "编辑接口", '50%', '50%');
                }
            });
    });
    layTool.layDate('#start_time')
    layTool.layDate('#end_time')
</script>
</body>
</html>
