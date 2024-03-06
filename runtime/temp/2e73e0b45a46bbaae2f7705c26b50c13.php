<?php /*a:1:{s:72:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\admin\view\order\index.html";i:1654078379;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>订单列表</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/style/admin.css" media="all">
</head>
<body>
<style>
    /*.layui-table-cell {*/
    /*    height: auto;*/
    /*    line-height: 60;*/
    /*}*/
</style>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <!--                <div class="layui-inline">-->
                <!--                    <label class="layui-form-label">开始时间</label>-->
                <!--                    <div class="layui-input-block">-->
                <!--                        <input type="text" name="start_time" placeholder="请输入" autocomplete="off" class="layui-input"-->
                <!--                               id="start_time">-->
                <!--                    </div>-->
                <!--                </div>-->
                <!--                <div class="layui-inline">-->
                <!--                    <label class="layui-form-label">结束时间</label>-->
                <!--                    <div class="layui-input-block">-->
                <!--                        <input type="text" name="start_time" placeholder="请输入" autocomplete="off" class="layui-input"-->
                <!--                               id="end_time">-->
                <!--                    </div>-->
                <!--                </div>-->
                <div class="layui-inline">
                    <label class="layui-form-label">商户单号</label>
                    <div class="layui-input-block">
                        <input type="text" name="order_no" placeholder="请输入" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">平台单号</label>
                    <div class="layui-input-block">
                        <input type="text" name="order_me" placeholder="请输入" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">匹配帐号</label>
                    <div class="layui-input-block">
                        <input type="text" name="account" placeholder="请输入" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <!--                <div class="layui-inline">-->
                <!--                    <label class="layui-form-label">通道编号</label>-->
                <!--                    <div class="layui-input-block">-->
                <!--                        <input type="text" name="order_pay" placeholder="请输入" autocomplete="off" class="layui-input">-->
                <!--                    </div>-->
                <!--                </div>-->
                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-admin" lay-submit lay-filter="LAY-user-back-search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="layui-card-body">
            <table id="LAY-user-table" lay-filter="LAY-user-table"></table>
            <script type="text/html" id="notifyStatusTpl">
                {{#  if(d.notify_status == 1){ }}
                <span class="layui-badge-dot green"></span>回调成功
                {{#  } else if(d.notify_status == 2){ }}
                <span class="layui-badge-dot gray"></span>回调失败
                {{#  } else if(d.notify_status == 0){ }}
                <span class="layui-badge-dot gray"></span>未回调
                {{#  } }}
            </script>
            <script type="text/html" id="table-seller-admin">

                <?php if((buttonAuth('order/check'))): ?>
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="check"><i
                        class="layui-icon layui-icon-edit"></i>查单</a>
                <?php endif; if((buttonAuth('order/notify'))): ?>
                {{#  if(d.admin_id == '1'){ }}
                <a class="layui-btn layui-btn-disabled layui-btn-xs" style="color: blue"><i
                        class="layui-icon layui-icon-ercifenjian"></i>手动回调</a>
                {{#  } else { }}
                <a class="layui-btn layui-btn-danger layui-btn-xs" style="color: blue" lay-event="notify"><i
                        class="layui-icon layui-icon-ercifenjian"></i>手动回调</a>
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

        layTool.table("#LAY-user-table", "/admin/order/index", [
            [{
                field: "merchant_sign",
                title: "商户"
            }, {
                field: "write_off_sign",
                title: "核商"
            }, {
                field: "order_no",
                title: "商户单号"
            }, {
                field: "order_me",
                title: "平台单号",
            }, {
                field: "account",
                title: "匹配帐号",
            }, {
                field: "operator",
                title: "运营商",
            }, {
                field: "amount",
                title: "订单金额",
            }, {
                field: "start_check_amount",
                title: "开单金额",
            }, {
                field: "add_time",
                title: "下单时间",
                align: "center",
                width: 170,
            }, {
                field: "pay_name",
                title: "选择方式",
            }, {
                field: "click_time",
                title: "点击时间",
            }, {
                field: "next_check_time",
                title: "下次查询",
            }, {
                field: "pay_time",
                title: "支付时间"
            }, {
                field: "order_status",
                title: "订单状态",
                align: "center",
            }, {
                field: "notify_status",
                title: "回调状态",
                align: "center",
                templet: '#notifyStatusTpl'
            },
                //     {
                //     field: "notify_time",
                //     title: "回调时间"
                // },
                {
                    field: "check_result",
                    title: "查单备注"
                }, {
                field: "order_desc",
                title: "备注"
            }, {
                title: "操作",
                align: "center",
                width: 180,
                fixed: "right",
                toolbar: "#table-seller-admin"
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

                                $.getJSON('<?php echo url("order/notify"); ?>', {id: e.data.id}, function (res) {

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
                    } else if ("check" === e.event) {

                        layer.ready(function () {
                            var index = layer.confirm('查询此订单？', {
                                title: '友情提示',
                                icon: 3,
                                btn: ['确定', '取消']
                            }, function () {

                                $.getJSON('<?php echo url("order/check"); ?>', {id: e.data.id}, function (res) {

                                    if (0 == res.code) {
                                        layer.msg(res.msg);
                                        setTimeout(function () {
                                            renderTable();
                                        }, 300);
                                    } else if (1 == res.code) {
                                        layer.msg(res.msg);
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

    layTool.layDate('#start_time')
    layTool.layDate('#end_time')
</script>
</body>
</html>
