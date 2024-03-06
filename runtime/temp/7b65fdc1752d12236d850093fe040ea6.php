<?php /*a:1:{s:74:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\admin\view\prepare\index.html";i:1651603901;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>预拉单管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/style/admin.css" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">预拉单名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="order_amount" placeholder="请输入" autocomplete="off" class="layui-input">
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
            <?php if((buttonAuth('prepare/addprepare'))): ?>
            <div style="padding-bottom: 10px;">
                <button class="layui-btn layuiadmin-btn-admin" data-type="add"><i class="layui-icon">&#xe654;</i> 添加
                </button>
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
                <?php if((buttonAuth('prepare/editprepare'))): ?>
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                        class="layui-icon layui-icon-edit"></i>编辑</a>
                <?php endif; if((buttonAuth('prepare/delprepare'))): ?>
                {{#  if(d.admin_id == '1'){ }}
                <a class="layui-btn layui-btn-disabled layui-btn-xs"><i class="layui-icon layui-icon-delete"></i>删除</a>
                {{#  } else { }}
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                        class="layui-icon layui-icon-delete"></i>删除</a>
                {{#  } }}
                <?php endif; ?>
            </script>
        </div>
    </div>
</div>

<script src="/static/layui/layui.js"></script>
<script src="/static/common/js/layTool.js"></script>
<script src="/static/common/js/jquery.min.js"></script>

<script>
    layui.config({
        base: '/static/admin/'
    }).use(['table'], function () {
        var $ = layui.$
            , form = layui.form
            , table = layui.table;

        var active = {

            add: function () {
                layTool.open("<?php echo url('prepare/addPrepare'); ?>", "添加预拉单", '50%', '50%');
            }
        };

        $('.layui-btn.layuiadmin-btn-admin').on('click', function () {
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });

        // 监听搜索
        form.on('submit(LAY-user-back-search)', function (data) {
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
        layTool.table("#LAY-user-table", "/admin/prepare/index", [
            [{
                field: "id",
                title: "预拉单ID"
            }, {
                field: "order_amount",
                title: "预拉单金额",
            }, {
                field: "prepare_num",
                title: "预拉数量",
            }, {
                field: "canUseNum",
                title: "可下单量",
            }, {
                field: "doPrepareNum",
                title: "预拉中量",
            }, {
                field: "status",
                title: "状态",
                templet: '#statusTpl'
            }, {
                field: "add_time",
                title: "添加事件",
            }, {
                title: "操作",
                align: "center",
                width: 150,
                fixed: "right",
                toolbar: "#table-seller-admin"
            }]
        ]);

        layui.use(['table', 'layer'], function () {
            let layer = layui.layer;
            let table = layui.table;

            table.on("tool(LAY-user-table)",
                function (e) {
                    if ("del" === e.event) {

                        layer.ready(function () {
                            var index = layer.confirm('您确定要删除该预拉单？', {
                                title: '友情提示',
                                icon: 3,
                                btn: ['确定', '取消']
                            }, function () {

                                $.getJSON('<?php echo url("prepare/delPrepare"); ?>', {id: e.data.admin_id}, function (res) {

                                    if (0 == res.code) {

                                        layer.msg(res.msg);
                                        setTimeout(function () {
                                            renderTable();
                                        }, 300);
                                    } else {
                                        layer.alert(res.msg);
                                    }
                                });
                            }, function () {

                            });
                        });
                    } else if ("edit" === e.event) {

                        layTool.open("/admin/prepare/editPrepare/id/" + e.data.id, "编辑预拉单", '50%', '50%');
                    }
                });
        });
    }
</script>
</body>
</html>
