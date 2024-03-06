<?php /*a:1:{s:78:"D:\phpStudy\PHPTutorial\WWW\dvvv\application\admin\view\orderdouyin\index.html";i:1649517335;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>抖音推单</title>
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
<!--                <div class="layui-inline">-->
<!--                    <label class="layui-form-label">添加时间</label>-->
<!--                    <div class="layui-input-block">-->
<!--                        <input type="text" name="start_time" placeholder="请输入" autocomplete="off" class="layui-input"-->
<!--                               id="operate_time">-->
<!--                    </div>-->
<!--                </div>-->
                <div class="layui-inline">
                    <label class="layui-form-label">订单编号</label>
                    <div class="layui-input-block">
                        <input type="text" name="order_no" placeholder="请输入" autocomplete="off"
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
                {{#  if(d.status == 0){ }}
                <button class="layui-btn layui-btn-success layui-btn-xs">未使用</button>
                {{#  } else if(d.status == 1) { }}
                <button class="layui-btn layui-btn-success layui-btn-xs">已使用</button>
                {{#  } else if(d.status == 2) { }}
                <button class="layui-btn layui-btn-danger layui-btn-xs">已禁用</button>
                {{#  } }}
                <!--                <?php if((buttonAuth('torder/changestatus'))): ?>-->
                <!--                {{#  if(d.status == 1){ }}-->
                <!--                <button class="layui-btn layui-btn-success layui-btn-xs" lay-event="changestatus">点击禁用</button>-->
                <!--                {{#  } else { }}-->
                <!--                <button class="layui-btn layui-btn-success layui-btn-xs" lay-event="changestatus">点击开启</button>-->
                <!--                {{#  } }}-->
                <!--                <?php endif; ?>-->

            </script>
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

                <?php if((buttonAuth('orderdouyin/deltorder'))): ?>
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
        layTool.table("#LAY-user-table", "/admin/orderdouyin/index", [
            [{
                field: "write_off_sign",
                title: "核销标识"
            }, {
                field: "order_no",
                title: "订单编号"
            }, {
                field: "account",
                title: "订单账号",
            }, {
                field: "status",
                title: "状态",
                templet: '#statusTpl'
            }, {
                field: "total_amount",
                title: "订单金额"
            }, {
                field: "success_amount",
                title: "充值金额",
            }, {
                field: "notify_status",
                title: "回调状态",
                templet: '#notifyStatusTpl'
            }, {
                field: "limit_time",
                title: "限制时间",
            }, {
                field: "add_time",
                title: "上传时间"
            }, {
                field: "notify_time",
                title: "回调时间"
            }, {
                field: "order_desc",
                title: "推单描述"
            }
                // , {
                //     title: "操作",
                //     align: "center",
                //     width: 150,
                //     fixed: "right",
                //     toolbar: "#table-seller-admin"
                // }
            ]
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

                                $.getJSON('<?php echo url("orderdouyin/delTorder"); ?>', {t_id: e.data.t_id}, function (res) {

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

    layTool.layDate('#start_time')
</script>
</body>
</html>
