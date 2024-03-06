<?php /*a:1:{s:78:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\admin\view\orderhexiao\index.html";i:1656831027;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>话费推单</title>
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
                    <label class="layui-form-label">开始时间</label>
                    <div class="layui-input-block">
                        <input type="text" name="startTime" placeholder="时间(上传)" autocomplete="off"
                               class="layui-input" id="startTime">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">时间截至</label>
                    <div class="layui-input-block">
                        <input type="text" name="endTime" placeholder="时间截至" autocomplete="off"
                               class="layui-input" id="endTime">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">核销单号</label>
                    <div class="layui-input-block">
                        <input type="text" name="order_no" placeholder="请输入" autocomplete="off"
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">平台单号</label>
                    <div class="layui-input-block">
                        <input type="text" name="order_me" placeholder="请输入" autocomplete="off"
                               class="layui-input">
                    </div>
                </div>

                <div class="layui-inline">
                    <label class="layui-form-label">核销标识</label>
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
                    <label class="layui-form-label">匹配状态</label>
                    <div class="layui-input-block">
                        <select name="order_status">
                            <option value=-1 selected>全部</option>
                            <option value=0>等待匹配</option>
                            <option value=1>匹配成功</option>
                            <option value=2>停止匹配</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">支付状态</label>
                    <div class="layui-input-block">
                        <select name="pay_status">
                            <option value=-1 selected>全部</option>
                            <option value=0>等待支付</option>
                            <option value=1>支付成功</option>
                            <option value=2>支付失败</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">回调状态</label>
                    <div class="layui-input-block">
                        <select name="notify_status">
                            <option value=-1 selected>全部</option>
                            <option value=0>暂未使用</option>
                            <option value=1>回调成功</option>
                            <option value=2>回调失败</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">充值账号</label>
                    <div class="layui-input-block">
                        <input type="text" name="account" placeholder="请输入" autocomplete="off"
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
                <button class="layui-btn layui-btn-primary layui-btn-xs">未使用</button>
                {{#  } else if(d.status == 1) { }}
                <button class="layui-btn layui-btn-success layui-btn-xs">使用中</button>
                {{#  } else if(d.status == 2) { }}
                <button class="layui-btn layui-btn-danger layui-btn-xs">已禁用</button>
                {{#  } }}
            </script>
            <script type="text/html" id="orderStatusTpl">
                {{#  if(d.order_status == 0){ }}
                <button class="layui-btn layui-btn-primary layui-btn-xs">等待匹配</button>
                {{#  } else if(d.order_status == 1) { }}
                <button class="layui-btn layui-btn-success layui-btn-xs">匹配成功</button>
                {{#  } else if(d.order_status == 2) { }}
                <button class="layui-btn layui-btn-danger layui-btn-xs">停止匹配</button>
                {{#  } }}

            </script>
            <script type="text/html" id="payStatusTpl">
                {{#  if(d.pay_status == 1){ }}
                <button class="layui-btn layui-btn-success layui-btn-xs">支付成功</button>
                {{#  } else if(d.pay_status == 2)  { }}
                <button class="layui-btn layui-btn-danger layui-btn-xs">支付失败</button>
                {{#  }else if(d.pay_status == 0){ }}
                <button class="layui-btn layui-btn-primary layui-btn-xs">等待支付</button>
                {{#  } }}
                <!--                {{#  if(d.pay_status == 0){ }}-->
                <!--                <button class="layui-btn layui-btn-primary layui-btn-xs">等待支付</button>-->
                <!--                {{#  } else if(d.pay_status == 1) { }}-->
                <!--                <button class="layui-btn layui-btn-success layui-btn-xs">支付成功</button>-->
                <!--                {{#  } else if(d.pay_status == 2) { }}-->
                <!--                <button class="layui-btn layui-btn-danger layui-btn-xs">支付失败</button>-->
                <!--                {{#  } }}-->
            </script>
            <script type="text/html" id="notifyStatusTpl">
                {{#  if(d.notify_status == 1){ }}
                <button class="layui-btn layui-btn-success layui-btn-xs">回调成功</button>
                <!--                <span class="layui-badge-dot green"></span>回调成功-->
                {{#  } else if(d.notify_status == 2){ }}
                <button class="layui-btn layui-btn-danger layui-btn-xs">回调失败</button>
                <!--                <span class="layui-badge-dot gray"></span>回调失败-->
                {{#  } else if(d.notify_status == 0){ }}
                <button class="layui-btn layui-btn-primary layui-btn-xs">未回调</button>
                <!--                <span class="layui-badge-dot gray"></span>未回调-->
                {{#  } }}
            </script>

            <script type="text/html" id="table-seller-admin">
                <?php if((buttonAuth('orderhexiao/notify'))): ?>
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="notify"><i
                        class="layui-icon "></i>止付</a>
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
        layTool.table("#LAY-user-table", "/admin/orderhexiao/index", [
            [{
                field: "write_off_sign",
                title: "核销标识"
            }, {
                field: "order_no",
                title: "核销单号"
            }, {
                field: "order_me",
                title: "平台单号"
            }, {
                field: "account",
                title: "充值账号",
            }, {
                field: "operator",
                title: "运营商",
            }, {
                field: "order_amount",
                title: "订单金额"
            }, {
                field: "pay_amount",
                title: "充值金额",
            }, {
                field: "add_time",
                title: "上传时间"
            }, {
                field: "order_limit_time",
                title: "冻结截至"
            },{
                field: "limit_time",
                title: "回调截至"
            },
                {
                    field: "last_check_amount",
                    title: "查询余额"
                },
                {
                    field: "use_time",
                    title: "匹配时间"
                }, {
                field: "pay_time",
                title: "支付时间",
                align: "center",
            }, {
                field: "notify_time",
                title: "回调时间"
            }, {
                field: "use_times",
                title: "使用次数",
            }, {
                field: "status",
                title: "状态",
                templet: '#statusTpl'
            }, {
                field: "order_status",
                title: "匹配状态",
                templet: '#orderStatusTpl'
            }, {
                field: "pay_status",
                title: "支付状态",
                templet: '#payStatusTpl'
            }, {
                field: "notify_status",
                title: "回调状态",
                templet: '#notifyStatusTpl'
            }, {
                field: "check_result",
                title: "查单结果"
            },{
                field: "notify_result",
                title: "回调结果"
            }
                // , {
                //     field: "order_desc",
                //     title: "描述"
                // }
                , {
                title: "操作",
                align: "center",
                width: 170,
                fixed: "right",
                toolbar: "#table-seller-admin"
            }
            ]
        ]);

        layui.use(['table', 'layer'], function () {
            let layer = layui.layer;
            let table = layui.table;

            table.on("tool(LAY-user-table)",
                function (e) {
                    if ("notify" === e.event) {
                        layer.ready(function () {
                            var index = layer.confirm('确定止付回调？', {
                                title: '友情提示',
                                icon: 3,
                                btn: ['确定', '取消']
                            }, function () {

                                $.getJSON('<?php echo url("orderhexiao/notify"); ?>', {id: e.data.id}, function (res) {

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

    layTool.layDate('#startTime');
    layTool.layDate('#endTime');
</script>
</body>
</html>
