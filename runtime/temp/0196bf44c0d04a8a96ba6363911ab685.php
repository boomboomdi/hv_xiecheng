<?php /*a:1:{s:77:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\admin\view\statistics\index.html";i:1653485808;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>核单统计</title>
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
                    <label class="layui-form-label">核销商标识</label>
                    <div class="layui-input-block">
                        <input type="text" name="write_off_sign" placeholder="请输入" autocomplete="off"
                               class="layui-input">
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">运营商</label>
                    <div class="layui-input-block">
                        <select name="operator">
                            <option value="">三网</option>
                            <option value="联通">联通</option>
                            <option value="电信">电信</option>
                            <option value="移动">移动</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">操作日期</label>
                    <div class="layui-input-block">
                        <input type="text" name="startTime" placeholder="默认当天00:00:00-23:59:59" autocomplete="off"
                               class="layui-input" id="startTime">
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
            <script type="text/html" id="statusTpl">
                {{#  if(d.status == 1){ }}
                <button class="layui-btn layui-btn-success layui-btn-xs">启用</button>
                {{#  } else { }}
                <button class="layui-btn layui-btn-danger layui-btn-xs">禁用</button>
                {{#  } }}
            </script>
            <script type="text/html" id="table-seller-admin">
                <?php if((buttonAuth('writeoff/editwriteoff'))): ?>
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                        class="layui-icon layui-icon-edit"></i>编辑</a>
                <?php endif; if((buttonAuth('writeoff/delwriteoff'))): ?>
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
                layTool.open("<?php echo url('writeoff/addWriteoff'); ?>", "添加核销", '50%', '50%');
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
        layTool.table("#LAY-user-table", "/admin/statistics/index", [
            [{
                field: "order_amount",
                title: "金额",
            }, {
                field: "operator",
                title: "运营商"
            }, {
                field: "write_off_sign",
                title: "核销商"
            }, {
                field: "orderTotalNum",
                title: "推单数量",
            }, {
                field: "totalOrderAmount",
                title: "推单金额(总)",
            }, {
                field: "totalPayOrderAmountNum",
                title: "支付数量",
            }, {
                field: "totalPayOrderAmount",
                title: "支付金额(总)",
            }, {
                field: "canOrderAmountNum",
                title: "可用数量",
            }]
        ]);

        layui.use(['table', 'layer'], function () {
            let layer = layui.layer;
            let table = layui.table;

            table.on("tool(LAY-user-table)",
                function (e) {
                    if ("del" === e.event) {

                        layer.ready(function () {
                            var index = layer.confirm('您确定要删除该管理员？', {
                                title: '友情提示',
                                icon: 3,
                                btn: ['确定', '取消']
                            }, function () {

                                $.getJSON('<?php echo url("writeoff/delWriteoff"); ?>', {write_off_id: e.data.write_off_id}, function (res) {

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

                        layTool.open("/admin/writeoff/editWriteoff/write_off_id/" + e.data.write_off_id, "编辑核销", '50%', '50%');
                    }
                });
        });
    }

    layTool.layDate('#startTime')
</script>
</body>
</html>
