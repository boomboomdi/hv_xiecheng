<?php /*a:1:{s:76:"D:\phpStudy\PHPTutorial\WWW\dvvv\application\admin\view\notifylog\index.html";i:1648219652;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>回调日志</title>
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
                    <label class="layui-form-label">商户单号</label>
                    <div class="layui-input-block">
                        <input type="text" name="order_no" placeholder="请输入" autocomplete="off" class="layui-input">
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
            <table id="LAY-user-table" lay-filter="LAY-user-table"></table>

        </div>

    </div>
</div>

<script src="/static/layui/layui.js"></script>
<script src="/static/common/js/jquery.min.js"></script>
<script src="/static/common/js/layTool.js"></script>
<script>
    layui.config({
        base: '/static/admin/'
    }).use(['table'], function () {
        var $ = layui.$
            , form = layui.form
            , table = layui.table;

        var active = {};

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

        layTool.table("#LAY-user-table", "/admin/notifylog/index", [
            [{
                field: "notify_id",
                title: "ID"
            }, {
                field: "client_id",
                title: "设备id"
            }, {
                field: "account",
                title: "收款账号"
            }, {
                field: "order_no",
                title: "商户单号"
            }, {
                field: "order_pay",
                title: "平台单号",
            }, {
                field: "status",
                title: "状态",
            }, {
                field: "add_time",
                title: "上传时间"
            }, {
                field: "pay_time",
                title: "付款时间"
            }, {
                field: "notify_log_desc",
                title: "备注"
            }]
        ], 20);

        layui.use(['table', 'layer'], function () {
            let layer = layui.layer;
            let table = layui.table;

            table.on("tool(LAY-user-table)",
                function (e) {
                    if ("notify" === e.event) {

                        layer.ready(function () {
                            var index = layer.confirm('您确定要回调此订单？', {
                                title: '友情提示',
                                icon: 3,
                                btn: ['确定', '取消']
                            }, function () {

                                $.getJSON('<?php echo url("order/notify"); ?>', {id: e.data.order_no}, function (res) {

                                    if (1000 == res.code) {

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

                        layTool.open("/admin/manager/editAdmin/admin_id/" + e.data.admin_id, "编辑管理员", '50%', '50%');
                    }
                });
        });
    }

    layTool.layDate('#start_time')
    layTool.layDate('#end_time')
</script>
</body>
</html>
