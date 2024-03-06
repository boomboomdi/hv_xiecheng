<?php /*a:1:{s:74:"D:\phpStudy\PHPTutorial\WWW\dvpay\application\admin\view\torder\index.html";i:1642593923;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>推单管理</title>
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
                    <label class="layui-form-label">订单编号</label>
                    <div class="layui-input-block">
                        <input type="text" name="apiMerchantOrderNo" placeholder="请输入" autocomplete="off"
                               class="layui-input">
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
                <?php if((buttonAuth('torder/changestatus'))): ?>
                {{#  if(d.status == 1){ }}
                <button class="layui-btn layui-btn-success layui-btn-xs" lay-event="changestatus">点击禁用</button>
                {{#  } else { }}
                <button class="layui-btn layui-btn-success layui-btn-xs" lay-event="changestatus">点击开启</button>
                {{#  } }}
                <?php endif; ?>


            </script>
            <script type="text/html" id="table-seller-admin">
                <?php if((buttonAuth('torder/notify'))): ?>
                {{#  if(d.admin_id == '1'){ }}
                <a class="layui-btn layui-btn-disabled layui-btn-xs"><i class="layui-icon layui-icon-user"></i>失败回调</a>
                {{#  } else { }}
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="notifyfail">失败回调</a>
                {{#  } }}
                <?php endif; if((buttonAuth('torder/notify'))): ?>
                {{#  if(d.admin_id == '1'){ }}
                <a class="layui-btn layui-btn-disabled layui-btn-xs"><i class="layui-icon layui-icon-user"></i>成功回调</a>
                {{#  } else { }}
                <a class="layui-btn layui-btn-success layui-btn-xs" lay-event="notifysuccess">回调</a>
                {{#  } }}
                <?php endif; if((buttonAuth('torder/deltorder'))): ?>
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
                layTool.open("<?php echo url('torder/addTorder'); ?>", "添加推单", '50%', '50%');
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
        layTool.table("#LAY-user-table", "/admin/torder/index", [
            [{
                field: "merchant_sign",
                title: "商户"
            }, {
                field: "apiMerchantOrderNo",
                title: "订单编号"
            }, {
                field: "apiMerchantOrderCardNo",
                title: "充值油卡号",
            }, {
                field: "apiMerchantOrderAmount",
                title: "充值金额"
            }, {
                field: "orderStatus",
                title: "订单状态"
            }, {
                field: "apiMerchantOrderCardNo",
                title: "充值油卡号"
            }, {
                field: "apiMerchantOrderDate",
                title: "请求时间"
            }, {
                field: "apiMerchantOrderType",
                title: "充值类型"
            }, {
                field: "apiMerchantOrderNotifyUrl",
                title: "异步回调地址"
            }, {
                field: "orderDiscount",
                title: "订单折扣"
            }, {
                field: "status",
                title: "状态",
                templet: '#statusTpl'
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
                            var index = layer.confirm('您确定要删除该推单？', {
                                title: '友情提示',
                                icon: 3,
                                btn: ['确定', '取消']
                            }, function () {

                                $.getJSON('<?php echo url("torder/delTorder"); ?>', {t_id: e.data.t_id}, function (res) {

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
                        layTool.open("/admin/manager/editTorder/t_id/" + e.data.t_id, "编辑推单", '50%', '50%');
                    } else if ("changestatus" === e.event) {
                        layer.ready(function () {
                            var index = layer.confirm('您确定修改推单状态？', {
                                title: '友情提示',
                                icon: 3,
                                btn: ['确定', '取消']
                            }, function () {
                                $.getJSON('<?php echo url("torder/changestatus"); ?>', {t_id: e.data.t_id}, function (res) {
                                    // layer.msg(res.code);
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
                    }else if("notifyfail" === e.event){
                        layer.ready(function () {
                            var index = layer.confirm('您确定手动回调？', {
                                title: '友情提示',
                                icon: 3,
                                btn: ['确定', '取消']
                            }, function () {
                                $.getJSON('<?php echo url("torder/notifyfail"); ?>', {t_id: e.data.t_id}, function (res) {
                                    // layer.msg(res.code);
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
                    }else if("notifysuccess" === e.event){
                        layer.ready(function () {
                            var index = layer.confirm('您确定手动回调？', {
                                title: '友情提示',
                                icon: 3,
                                btn: ['确定', '取消']
                            }, function () {
                                $.getJSON('<?php echo url("torder/notifysuccess"); ?>', {t_id: e.data.t_id}, function (res) {
                                    // layer.msg(res.code);
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
                    }
                });
        });
    }
</script>
</body>
</html>
