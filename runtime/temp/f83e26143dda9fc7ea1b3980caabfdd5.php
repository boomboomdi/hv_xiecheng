<?php /*a:1:{s:69:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\api\view\ceshi\shop.html";i:1706292884;}*/ ?>
<!DOCTYPE html>
<!-- saved from url=(0042)https://p.6ffk7p.z1024.top/zfb/pc/29330620 -->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="Content-Language" content="zh-cn">
    <meta name="apple-mobile-web-app-capable" content="no">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="format-detection" content="telephone=no,email=no">
    <meta name="apple-mobile-web-app-status-bar-style" content="white">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Cache" content="no-cache">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>商品购买充值</title>
    <link rel="icon" href="/static/cami/favicon.ico"/>
    <link href="/static/cami/kami.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="/static/cami/layui.css">
    <link rel="stylesheet" href="/static/cami/bootstrap.min.css">
    <script src="/static/cami/jquery3.3.1.js"></script>
    <script src="/static/cami/qrcode.min.js"></script>
    <script src="/static/cami/layui.js" type="text/javascript"></script>
    <!--<script src="/assets/static/js/clipboard.min.js" type="text/javascript"></script>-->
    <script src="/static/cami/clipboard.min.js"></script>
    <script src="/static/cami/bootstrap.min.js"></script>

    <style>
        p {
            margin: 0 0 3px 0;
        }

        .title {
            color: #fff;
            background: #1983c8;
            font-size: 16px;
            font-weight: 550;
        }

        .gooddes {
            margin: 8px 10px 0px 10px;
            border-radius: 0.25rem;
            padding: 5px 0px 0px 5px;
            color: #32465b;
            background: #fff;
            border-radius: 5px;
        }

        .description {
            margin: 8px 6px 0px 10px;
            border-radius: 0.25rem;
            padding: 5px 0px 0px 5px;
            font-size: 14px;
            overflow: hidden;
            color: #32465b;
            background: #fff;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="body" style="background:#e8edf0 ">


    <div class="mod-ct" style="border: 0px;">
        <div class="title">
            <p align="center" style="padding:5px 0px 5px 0px"><?php echo htmlentities($orderData['camiTypeName']); ?></p>
        </div>
        <div class="gooddes" style="text-align: left;">
            <p>商品名称：<span style="color:#1983c8;font-size:16px;font-weight:800"><?php echo htmlentities($orderData['camiTypeName']); ?><?php echo htmlentities($orderData['amount']); ?>元</span>
            </p>
            <p>商品价格：<span style="color:#1983c8;font-size:16px;font-weight:800"><?php echo htmlentities($orderData['amount']); ?></span></p>
        </div>

        <div class="time-item">
            <img id='show_qrcode' alt="加载中..." src="/assets/img/jlykt2.jpg" width="150" height="50"
                 style="display: inline-block;">
        </div>

        <div class="description" style="text-align: left;">
            <p>请按照下列步骤充值</p>
            <p>1.点击打开淘宝或者天猫搜索并购买<b><span style="color: red;"><?php echo htmlentities($orderData['camiTypeName']); ?><?php echo htmlentities($orderData['amount']); ?>元</span></b></p>
            <p>2.购买后联系客服获取<b><span style="color: red;">卡号和密码</span></b>，一定要回来正确输入卡号和密码，点击确认提交！</p>
            <p>3.一个卡密切勿重复多次提交，避免充值失败</p>
            <p style="color:red;"><b>4.我已确认该卡的单卡面值选择准确无误，卡种或面值选择错误将导致整单核销失败，损失自行承担!!损失自行承担!</b></p>
        </div>

        <!--<div class="time-item-alipay" >
            <strong id="hour_show" style="background: #1b76fb;"><s id="h"></s>00时</strong>
            <strong id="minute_show" style="background: #1b76fb;"><s></s>00分</strong>
            <strong id="second_show" style="background: #1b76fb;"><s></s>00秒</strong>
        </div>-->

        <form class="form-inline">

            <div class="form-group" style="margin-top: 10px;">

                <input type="string" class="form-control" id="cardno" placeholder="请输入聚力一卡通卡号16位" name="cardno"
                       style="display: inline-block;width: 90%;height:40px;">
                <input type="string" class="form-control" id="cardpwd" placeholder="请输入聚力一卡通密码16位" name="cardpwd"
                       style="display: inline-block;width: 90%;margin-top:6px;height:40px;">

            </div>
            <button type="button" id="subAcount" class="btn btn-lg btn-block"
                    style="background-color: #1983c8;color: #fff;width: 90%;display: inline-block;">提交卡密
            </button>
        </form>
        <div class="tip">
            <!--<a id='toalipay' href="javascript:;" style="color: #fff;text-decoration: none; text-align: center;padding: .95rem 0; display: inline-block; width: 90%; height:50px;border-radius: .5rem; font-size: 18px;background-color: #1b76fb; border: 1px solid #1b76fb;letter-spacing:normal;font-weight: normal"
               class='action'>点击打开支付宝购买</a>-->

            <a id='totbpay1' href="javascript:;"
               style="color: #fff;text-decoration: none; text-align: center;padding: 6px; display: inline-block; width: 90%; height:35px;border-radius: .5rem; font-size: 16px;background-color: #fe6f2c; border: 1px solid #fe6f2c;letter-spacing:normal;font-weight: normal"
               class='action'>[渠道1]点击打开淘宝购买</a>

            <a id='totbpay2' href="javascript:;"
               style="color: #fff;text-decoration: none; text-align: center;padding: 6px; display: inline-block; width: 90%; height:35px;border-radius: .5rem; font-size: 16px;background-color: #fe6f2c; border: 1px solid #fe6f2c;letter-spacing:normal;font-weight: normal;margin-top:5px;"
               class='action'>[渠道2]点击打开淘宝购买</a>

            <a id='totbpay' href="javascript:;"
               style="margin-top:10px;color: #fff;text-decoration: none; text-align: center;padding: 6px;  display: inline-block; width: 90%; height:35px;border-radius: .5rem; font-size: 16px;background-color: #fe6f2c; border: 1px solid #fe6f2c;letter-spacing:normal;font-weight: normal"
               class='action'>[渠道3]点击打开淘宝购买</a>

        </div>


        <div class="time-item" style="margin-top:20px;">
            <p style="color: red;"><b>优先选择销量高的购买，示例如下</b></p>
            <img id='show_qrcode' alt="加载中..." src="/assets/img/jlykt1.jpg" width="330" height="500"
                 style="display: inline-block;">
        </div>

    </div>


</div>

<!--注意下面加载顺序 顺序错乱会影响业务-->
<script>
    layui.use(['layer', 'form'], function () {
        var $ = layui.jquery, layer = layui.layer, form = layui.form;

        /*layer.confirm('购买后记得回来本页面提交卡密才能充值成功', {
          title: "支付提示",
          icon: 1,
          btn: ['我已知晓'],
          closeBtn :0,
          btnAlign: "c",
        }, function(index, layero){

          layer.close(index);

        });*/


        var str = '<b><span style="color:#1983c8">此卡是聚力一卡通</span><br><span style="color:red">推荐选择渠道1和2直接跳转店铺购买</span><br><span style="color:#1983c8">渠道3跳转优先选择销量排名的店铺购买</span><br><span style="color:red">如果发现渠道1，渠道2链接下架，刷新页面，刷新页面</span><br><span style="color:#1983c8">会重新匹配新的店铺! ! !</span><br><span style="color:red">如果发现渠道1渠道2消失，请直接选择渠道3购买！</span><br></b>';

        layer.alert(str, {icon: 1});


        /*layer.prompt({
            btn:['确定提交'],
            closeBtn :0,
            title: '请输入你付款账号的名字',
            formType: 0

        }, function (value, index) {

            $.post("/api/gateway/setName", {
                tradeNo: orderNo,
                name:value
            },
            function(data){
                console.log(data);
                if(data.code==1){
                    layer.close(index);
                    layer.msg(data.msg, {time: 2000}, function () {

                    });


                }else{
                    layer.msg(data.msg, {time: 1500, anim: 6});
                }
            });



        });*/

    })

    //二维码对象
    var objQrCode;
    //检查订单定时器
    var checkOrderInterval;
    //倒计时定时器
    var countDownInterval;

    var amount = '100';

    var payUrl = '';
    //订单编号
    var orderNo = '202401276074860647077';
    var orderId = '29330620';
    var time = '1710';
    var click_data = '';
    var isClick = true;
    var tb_good_id1 = '745172411809';
    var tb_good_id2 = '';

    //设置二维码超时
    var setQrCodeTimeOut = function (message) { //二维码超时则停止显示二维码
        /*$(".qrcode-img-area .expired").removeClass("hidden");
        $(".btn-pay").removeClass("btn-alipay");
        $(".btn-pay").addClass("btn-expired");
        $(".btn-pay")[0].innerHTML = '111';
        $(".btn-pay")[0].disabled = true;*/

        $('#hour_show').html('<s id="h"></s>' + '00' + '时');
        $('#minute_show').html('<s></s>' + '00' + '分');
        $('#second_show').html('<s></s>' + '00' + '秒');
        $("#expiredDiv").attr("style", "display:block;");
        $("#paybtn").hide();

        clearInterval(checkOrderInterval);
        clearInterval(countDownInterval);
    }

    //定时检测订单支付情况
    var countDown = function (intDiff) {
        countDownInterval = window.setInterval(function () {
            var day = 0,
                hour = 0,
                minute = 0,
                second = 0;//时间默认值
            if (intDiff > 0) {
                day = Math.floor(intDiff / (60 * 60 * 24));
                hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
                minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
                second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
            }

            if (hour <= 9)
                hour = '0' + hour;
            if (minute <= 9)
                minute = '0' + minute;
            if (second <= 9)
                second = '0' + second;
            $('#hour_show').html('<s id="h"></s>' + hour + '时');
            $('#minute_show').html('<s></s>' + minute + '分');
            $('#second_show').html('<s></s>' + second + '秒');
            if (hour <= 0 && minute <= 0 && second <= 0) {
                setQrCodeTimeOut("订单已过期，请重新提交订单")
            }
            intDiff--;
        }, 1000);
    }

    var checkOrder = function () {
        $.get("/api/gateway/checkorder", {tradeNo: orderNo, t: Math.random()}, function (result) {
            if (result.data.status == "1") {
                setQrCodeTimeOut("支付成功");
                layer.alert("支付成功", {icon: 1, closeBtn: 0}, function (index) {
                    //closeWindow();
                    layer.close(index)

                    location.reload();

                });

            } else if (result.data.status == "3") {

                layer.alert("支付时间已过期", {icon: 2, closeBtn: 0}, function (index) {
                    //closeWindow();
                    layer.close(index)
                    //location.reload();
                });

                setQrCodeTimeOut("订单超时");
            }
        });
    }

    $(document).ready(function () {

        if (tb_good_id1.length < 1) {
            $("#totbpay1").hide();
        }

        if (tb_good_id2.length < 1) {
            $("#totbpay2").hide();
        }

        $('#orderDetail .arrow').click(function (event) {
            if ($('#orderDetail').hasClass('detail-open')) {
                $('#orderDetail .detail-ct').slideUp(500, function () {
                    $('#orderDetail').removeClass('detail-open');
                });
            } else {
                $('#orderDetail .detail-ct').slideDown(500, function () {
                    $('#orderDetail').addClass('detail-open');
                });
            }
        });

        //检查订单
        //checkOrderInterval = setInterval(checkOrder, 1000);

        //执行倒计时
        //countDown(time);


    });

    //识别
    $("#getorderinfo").click(function () {
        var orderinfo = $("#orderinfo").val();

        if (orderinfo.length < 1) {
            layer.msg('请输入订单信息再点击识别', {time: 1500, anim: 6});
            return;
        }

        $.post("/api/index/getOrderCard", "content=" + orderinfo, function (data) {
            if (data.code == 1) {

                layer.msg(data.msg, {time: 2500, anim: 6});

                $("#cardno").val(data.data.cardno);
                $("#cardpwd").val(data.data.cardpwd);
            } else {
                layer.msg(data.msg, {time: 2500, anim: 6});
            }


        })

    });

    $("#tojdpay").click(function () {
        var jd_link = "https://so.m.jd.com/ware/search.action?keyword=聚力一卡通" + amount;
        var url = 'openapp.jdmobile://virtual?params={"category":"jump","des":"m","url":"' + jd_link + '","keplerID":"0","keplerFrom":"1","kepler_param":{"source":"kepler-open","otherData":{"mopenbp7":"0"},"channel":"8bfd09e186324410bd59504c345afd85"},"union_open":"union_cps"}';
        window.location.href = url;
    });

    $("#topddpay").click(function () {
        var pdd_link = "https://mobile.yangkeduo.com/search_result.html?sort_type=_sales&search_key=聚力一卡通" + amount;
        var url = 'pddopen://?h5Url=' + encodeURIComponent(pdd_link);
        window.location.href = url;
    });

    /*$("#totbpay").click(function() {

      var url = 'tbopen://m.taobao.com/tbopen/index.html?h5Url=https%3A%2F%2Fmain.m.taobao.com%2Fsearch%2Findex.html%3Fspm%3Da215s.7406091.topbar.1.560c6770snz1OF%26pageType%3D3%26q%3D%E6%99%BA%E9%80%89%E4%B8%80%E5%8D%A1%E9%80%9A'+ amount;
      window.location.href = url;
    });*/

    $("#totbpay").click(function () {

        var tb_link = "https://main.m.taobao.com/search/index.html?spm=a215s.7406091.topbar.1.560c6770snz1OF&pageType=3&q=%E9%AA%8F%E5%8D%A1%E8%81%9A%E5%8A%9B%E4%B8%80%E5%8D%A1%E9%80%9A" + amount;
        var url = 'tbopen://m.taobao.com/tbopen/index.html?h5Url=' + encodeURIComponent(tb_link);
        window.location.href = url;

    });

    $("#totbpay1").click(function () {

        url = 'taobao://item.taobao.com/item.htm?id=' + tb_good_id1;
        window.location.href = url;
    });

    $("#totbpay2").click(function () {
        url = 'taobao://item.taobao.com/item.htm?id=' + tb_good_id2;
        window.location.href = url;
    });

    //支付宝
    $("#toalipay").click(function () {
        window.location.href = 'alipays://platformapi/startapp?appId=20000067&url=https%3A%2F%2Fmain.m.taobao.com%2Fsearch%2Findex.html%3Fspm%3Da215s.7406091.topbar.1.560c6770snz1OF%26pageType%3D3%26q%3D%E6%99%BA%E9%80%89%E4%B8%80%E5%8D%A1%E9%80%9A' + amount
        //新店
    });

    //复制金额
    $('#copybtn').click(function () {
        var copy1 = new copyFunc('copybtn');
    })

    $("#alipay").click(function () {

        var amt = amount

        window.location.href = 'alipays://platformapi/startapp?appId=2018052460226391&page=%2Fpages%2Fdetail%2Fdetail%3Fid%3Dc0de5227-1ad0-4441-8397-8e5a86c71202%26_um_ssrc%3DSYcUjDL0Nb6ae%2FSqvU%2By26xtgPz3ifbCtdMEZ%2BI00ec%3D%26_um_sts%3D1669489220470&enbsv=0.2.2211091422.15&chInfo=ch_share__chsub_CopyLink&apshareid=9B5937B5-8A38-4B11-926E-7792E0A42900&shareBizType=H5App_XCX&fxzjshareChinfo=ch_share__chsub_CopyLink&launchKey=023f60c8-15c9-414b-a564-ecba4c34eff4-1669489571133'
    });

    function copyFunc(id) {
        var clip = new ClipboardJS('#' + id);

        clip.on('success', function (e) {
            console.log(33);
            layer.msg('复制成功!', {time: 1000});
            console.log(e);
            //打印动作信息（copy或者cut）
            console.info('Action:', e.action);
            //打印复制的文本
            console.info('Text:', e.text);
            //打印trigger
            console.info('Trigger:', e.trigger);
        });

        clip.on('error', function (e) {
            console.log(44);
            layer.msg('复制失败!', {time: 1500, anim: 6});
        });
    }

    $("#subAcount").click(function () {

        var cardno = $("#cardno").val();
        var cardpwd = $("#cardpwd").val();
        cardno = cardno.trim();
        cardpwd = cardpwd.trim();

        if (cardno.length != 16 || cardpwd.length != 16) {
            layer.msg('请输入正确的16位卡号和密码', {time: 1500, anim: 6});
            return;
        }

        if (isClick) {

            isClick = false;
            setTimeout(function () {
                isClick = true;
            }, 3000);//3秒内不能重复点击

            $.post("/api/gateway/subJwCard", "tradeNo=" + orderNo + "&cardno=" + cardno + "&cardpwd=" + cardpwd, function (data) {

                if (data.code == 1) {
                    layer.alert(data.msg, {icon: 1, closeBtn: 0}, function (index) {

                        layer.close(index)
                        location.reload();
                    });
                } else {
                    layer.msg(data.msg, {time: 2500, anim: 6});
                }
            })

        } else {

            layer.msg('请勿重复点击', {time: 1500, anim: 6});
        }


    })


</script>

</body>
</html>