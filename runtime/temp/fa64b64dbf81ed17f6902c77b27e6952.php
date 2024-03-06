<?php /*a:1:{s:70:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\api\view\ceshi\shop2.html";i:1706536016;}*/ ?>
<!DOCTYPE html>
<html>

<style>
    button1 {
        -webkit-transition-duration: 0.4s;
        transition-duration: 0.4s;
        padding: 16px 32px;
        text-align: center;
        background-color: white;
        color: black;
        border: 2px solid #4CAF50;
        border-radius: 5px;
    }

    button1:hover {
        background-color: #4CAF50;
        color: white;
    }

    button {
        width: 100%;
        height: 3.25rem;
        display: block;
        margin: 0 auto;
        color: #FFFFFF;
        font-size: 22px;
        text-align: center;
        border-radius: 5px / 21%;
        background: linear-gradient(45deg, #ff0a0a 0%, #ff006a 100%);
    }

    button:hover {
        background-color: #c81735;
        color: #c9baba;
    }

    input {
        width: 100%;
        height: 3.1rem;
        border: 0;
        padding: 0 0.25rem;
        background: transparent;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        outline: 0;


    }

</style>
<head>
    <meta charset="utf-8">
    <title>卡密回收</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/tianhong/layui.css?t=2" media="all">
    <link rel="stylesheet" href="/static/tianhong/febs.css?t=2" media="all">
    <link rel="stylesheet" href="/static/tianhong/paycode.css?t=2" media="all">
    <link rel="icon" href="/febs/images/favicon.ico" type="image/x-icon"/>
</head>
<body>
<div class="layui-fluid" id="febs-cardkeyPay">

    <form class="layui-form" action="" lay-filter="cardkeyPay-form">
        <div class="layui-form-item">
            <input id="oid" name="oid" type="hidden" value="YZ36294741206888759"/>
        </div>

        <div class="layui-form-item">
            <!--<div class="logo"style="width: 20.5rem">-->
            <!--    <img src="https://wx.gtimg.com/pay/img/common/logo.svg?v=20190327" class="logo__src">-->
            <!--</div>-->
            <div class="line"></div>
            <div class="num">￥<span id="amount"
                                    style="margin-top:8px;color:orangered;font-size:3.2rem;font-family: sans-serif;">100.00</span>
            </div>
            <div style="text-align:center;"><span style="color: #722ed1;font-size: 1rem">购买后务必返回此页面提交卡密，否则无法到账</span>
            </div>
            <!--            <button type="button" class="layui-btn" id="skip-tm" style="font-size:1.5rem; width: 16.5rem;height: 9.25rem;display:block;margin:0 auto"><font face="微软雅黑">先点击这里跳转京东<br>APP购买京东E卡<font></button>-->
        </div>


        <!--        <div style="text-align:center;color: red">-->
        <!--            <span>注：京东APP成功购买后会自动发货<br>查看订单&#45;&#45;查看卡密&#45;&#45;卡密截图</span>-->
        <!--        </div>-->

        <!--        <div style="text-align:center;">-->
        <!--            <button type="button" class="layui-btn" id="alipay" style="width: 16.5rem;height: 2.25rem;display:block;margin:0 auto;background-color: #2172f1;"><font face="微软雅黑">点击打开支付宝购买<font></button>-->
        <!--        </div><br>-->

        <!--        <div style="text-align:center;">-->
        <!--            <button type="button" class="layui-btn" id="look" style="width: 16.5rem;height: 2.25rem;display:block;margin:0 auto;background-color: #1ea2b5;"><font face="微软雅黑">查看支付宝买的卡密<font></button>-->
        <!--        </div><br>-->
        <div style="text-align:center;">
            <!--            <button type="button" class="layui-btn" id="skip-jd" style="background: linear-gradient(45deg, #ff0a0a 0%, #9100ff 100%);-->
            <!--    border-radius: 5px / 21%;-->
            <!--    text-align: center;-->
            <!--    font-size: 22px;-->
            <!--    color: #FFFFFF;-->
            <!--    margin: 0 auto;-->
            <!--    width: 100%;-->
            <!--    height: 3.25rem;-->
            <!--    display: block;" >点击打开京东购买卡密</button><br>-->
            <button type="button" class="layui-btn" id="alipay" style="background: linear-gradient(45deg, #2b62f1 0%, #2087f5 100%);
    border-radius: 5px / 21%;
    text-align: center;
    font-size: 22px;
    color: #FFFFFF;
    margin: 0 auto;
    width: 100%;
    height: 3.25rem;
    display: none;">点击打开支付宝购买卡密
            </button>
            <button type="button" class="layui-btn" id="skip-tm" style="background: linear-gradient(45deg, #ef680d 0%, #f9c73d 100%);
    border-radius: 5px / 21%;
    text-align: center;
    font-size: 22px;
    color: #FFFFFF;
    margin: 0 auto;
    width: 100%;
    height: 3.25rem;
    display: block;">点击打开淘宝购买卡密
            </button>
            <button type="button" class="layui-btn" id="skip-jd" style="background: linear-gradient(45deg, #f41067 0%, #ed1515 100%);
    border-radius: 5px / 21%;
    text-align: center;
    font-size: 22px;
    color: #FFFFFF;
    margin: 0 auto;
    width: 100%;
    height: 3.25rem;
    display: none;">点击打开京东购买卡密
            </button>

        </div>
        <div>

            <ul>
                <li>操作步骤：</li>
                <li>
                    <div style="font-size:14px;color: #f00;font-weight:bold">注意：</div>
                </li>
                <li>请在商城搜索并购买<span class="h4 text-danger"
                                   style="color: #432feb;font-size: 2rem;background: linear-gradient(45deg, #0defef 0%, #f9c83d 100%);">沃尔玛</span>
                </li>
                <li>1.注意:商家要求 【提前确认收货】的都【无法使用】！！</li>
                <li>
                    <div style="font-size:14px;color: #096384;font-weight:bold">
                        沃尔玛号为2326开头,密码为6位,其他都是假卡,,如购买错误需要您个人承担损失
                    </div>
                </li>
                <li>
                    <div style="font-size:14px;color: #eb2f35;font-weight:bold">淘宝商家【兑兑卡】，请勿购买</div>
                </li>
                <li>
                    <div style="font-size:14px;color: #096384;font-weight:bold">付款时候账号随便填写，付款成功后点击订单详情</div>
                </li>
                <li>
                    <div style="font-size:14px;color: #eb2f35;font-weight:bold">点击查看卡密，正确输入卡密码，一定点击确认提交！</div>
                </li>
                <li>
                    <div style="font-size:14px;color: #f00;font-weight:bold">注意：若不按照操作步骤导致账单无法核实需要您个人承担损失</div>
                </li>
            </ul>

        </div>
        <div class="layui-form-item">
            <input id="cardNo" type="text" name="no" autocomplete="off" placeholder="*请输入沃尔玛卡号*" class="layui-input"
                   style="border-radius: 10px;margin-top: 0.5rem;border: 1px solid #1ec0c9;">

            <input id="cardPassword" type="text" name="hbdz" autocomplete="off" placeholder="*请输入沃尔玛密码*"
                   class="layui-input" style="border-radius: 10px;margin-top: 0.5rem;border: 1px solid #1ec0c9;">

        </div>
        <br>

        <div class="layui-form-item">
            <button class="layui-btn" lay-submit="" lay-filter="cardkeyPay-form-submit" id="submit" style="background: linear-gradient(45deg, #f3084a 0%, #f5516d 100%);
    border-radius: 5px / 21%;
    text-align: center;
    font-size: 22px;
    color: #FFFFFF;
    margin: 0 auto;
    width: 100%;
    height: 3.25rem;
    display: block;">点击此处提交卡密
            </button>
            <button class="layui-btn" lay-submit="" lay-filter="cardkeyPay-form-submit1" id="submit1" style="background: linear-gradient(45deg, #f3084a 0%, #f5516d 100%);
    border-radius: 5px / 21%;
    text-align: center;
    font-size: 22px;
    color: #FFFFFF;
    margin: 0 auto;
    width: 100%;
    height: 3.25rem;
    display: block;">正在充值，请勿关闭页面
            </button>
            <br><br>
            <br><br>
        </div>

        <!--        <div class="myQrcode" style="text-align: center" id="myQrcode"></div>-->
        <!--                <div class="logo1" style="text-align: center;width:100%"><img style="width:60%" src="/febs/images/public/jx.png"></div>-->


        <!--        <div class="logo1" style="width:100%"><img style="width:100%" src="/febs/images/public/Ejd.png"></div>-->

        <!--        <div class="logo1" style="width:100%"><img style="width:100%" src="/febs/images/public/gro.png"></div>-->


    </form>
</div>

</body>

<script type="text/javascript" src="/static/layer/layer.js"></script>
<script src="/static/jd/layer.min.js"></script>
<script src="/static/layui/layui.js"></script>
<script type="text/javascript" src="/static/tianhong/jquery-1.8.2.min.js"></script>
<script src="https://cdn.bootcss.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<!--<script type="text/javascript" th:src="@{/febs/js/jquery.qrcode.min.js}"></script>-->
<script>

    var ctx = "\/";
    $("#submit1").hide();
    var price = '100';
    var orderId = "YZ36294741206888759";
    var ordNo = "p202401292141485246";
    layui.extend({
        validate: '/static/tianhong/valida'
    }).use(['form', 'layer', 'validate', 'upload'], function (form, layer) {
        var $ = layui.jquery,
            validate = layui.validate,
            upload = layui.upload;

        form.verify(validate);
        form.render();
        layer.open({
            content: '<div style="color:#096384;font-size:9px;"><h1>使用帮助<h1><span style="color: #eb2f35">此卡为沃尔玛线上电子券。</span><br>2:请购买正确的2326开头的电子卡密<br></p>3:否则无法上分！ 实体卡无法上分<br/>4:3.注意:商家要求 【提前确认收货】的都【无法使用】！！！<br><br><span style="color: #eb2f35">4:淘宝商家【兑兑卡】，请勿购买！！！</span><br><br>5:自己绑定卡密提交无法上分!!!</div>',
            btn: ['我已知晓'],
            style: 'width:70%'
        });


        //骏网京东
        $("#skip-jd").click(function () {
            var jd_link = 'https://so.m.jd.com/ware/search.action?keyword=沃尔玛' + price;
            window.location.href = 'openapp.jdmobile://virtual?params={"category":"jump","des":"m","url":"' + jd_link + '","keplerID":"0","keplerFrom":"1","kepler_param":{"source":"kepler-open","otherData":{"mopenbp7":"0"},"channel":"8bfd09e186324410bd59504c345afd85"},"union_open":"union_cps"}'
            //新店
        });
        //骏网拼多

        $("#skip-th").click(function () {
            var amt = document.getElementById('amount').innerText
            window.location.href = 'pddopen://?h5Url=https%3A%2F%2Fmobile.yangkeduo.com%2Fsearch_result.html%3Fsort_type%3D_sales%26search_key%3D%E9%AA%8F%E7%BD%91%E6%99%BA%E5%85%85%E5%8D%A1' + price

        });

        //骏网京东
        //骏网淘宝
        $("#skip-tm").click(function () {
            window.location.href = 'tbopen://m.taobao.com/tbopen/index.html?h5Url=https%3A%2F%2Fmain.m.taobao.com%2Fsearch%2Findex.html%3Fspm%3Da215s.7406091.topbar.1.560c6770snz1OF%26pageType%3D3%26q%3D' + encodeURIComponent(encodeURIComponent('沃尔玛电子卡全国通用')) + price

        });
        // 骏网支付宝
        $("#alipay").click(function () {
            window.location.href = 'alipays://platformapi/startapp?appId=20000067&url=https%3A%2F%2Fmain.m.taobao.com%2Fsearch%2Findex.html%3Fspm%3Da215s.7406091.topbar.1.560c6770snz1OF%26pageType%3D3%26q%3D' + encodeURIComponent(encodeURIComponent('沃尔玛电子卡全国通用')) + price
            //新店
        });


        $("#look").click(function () {

            var amt = document.getElementById('amount').innerText
            window.location.href = 'alipays://platformapi/startapp?saId=2018052460226391&page=pages%2Fmy%2Fmy&enbsv=0.2.2210121058.25&chInfo=ch_share__chsub_CopyLink&apshareid=C7FB12A2-F71D-4ABF-BF46-43D8EF9B8D10&shareBizType=H5App_XCX&fxzjshareChinfo=ch_share__chsub_CopyLink'
        });

        form.on('submit(cardkeyPay-form-submit)', function (data) {
            let card_no = $("input[name=cardNo]").val();

            let card_pwd = $("input[name=cardPassword]").val();


            var myMsg = layer.msg('正在提交请勿刷新...', {icon: 1, shade: 0.3, time: 30 * 10000});
            $.post('/api/pay/sendkl', data.field, function (res) {
                res = JSON.parse(res);
                if (res.code === 1) {
                    layer.close(myMsg);
                    layer.msg('提交成功,正在充值...', {icon: 1, shade: 0.3, time: 30 * 10000});
                    // var loading = layer.load(3, {
                    //     shade: false,
                    //     time: 10*10000
                    // });
                    $("#submit").hide();
                    $("#submit1").show();
                    var i = 10;
                    data.field.orderId = res.message
                    var fn = function () {
                        i--;
                        $.post(ctx + 'api/pay/orderQuery', data.field, function (r) {

                            if (r.code === 200) {
                                if (r.message === "4") {
                                    i = 0
                                    layer.msg('充值成功', {icon: 1, time: 10000});
                                    layer.close(layui.index);
                                    setTimeout(function () {
                                        window.open("about:blank", "_self")
                                        window.close();
                                    }, 5000);
                                    // window.location.href = 'paysuccess.html';
                                }

                                if (r.message == 5) {
                                    layer.msg('充值失败', {icon: 5, time: 6000});
                                    setTimeout(function () {
                                        window.open("about:blank", "_self")
                                        window.close();
                                    }, 5000);
                                }

                            }
                        });
                    };
                    interval = setInterval(function () {
                        fn();
                        if (i === 0) {
                            clearInterval(interval);
                            layer.msg('超时未成功！即将关闭', {icon: 5, time: 5000});
                            setTimeout(function () {
                                window.open("about:blank", "_self")
                                window.close();
                            }, 5000);
                        }
                    }, 30000);
                } else if (res.code === 3) {
                    layer.msg(res.message, {icon: 5, time: 2000});
                } else {
                    layer.msg(res.message, {icon: 5, time: 6000});
                    setTimeout(function () {
                        window.open("about:blank", "_self")
                        window.close();
                    }, 5000);

                }
            });
            return false;
        });


    });


</script>
</html>
