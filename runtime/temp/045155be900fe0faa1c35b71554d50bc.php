<?php /*a:1:{s:74:"D:\phpStudy\PHPTutorial\WWW\hvvv\application\api\view\orderinfo\info1.html";i:1709328807;}*/ ?>
<!DOCTYPE html>
<!-- saved from url=(0014)about:internet -->
<html style="font-size: 23.4375px;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>回收卡密</title>
    <meta name="description" content="支付">
    <meta name="keywords" content="支付">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/static/jquery.js"></script>
    <link rel="stylesheet" href="/static/n_main.css">
    <link rel="stylesheet" href="/static/pay.css">
    <script type="text/javascript" src="/static/layer.js"></script>
    <link rel="stylesheet" href="/static/layer.css" id="layuicss-layer">

    <style type="text/css" id="c_payment_bank"></style>
    <style type="text/css">
        .clear-input {
            display: none;
            position: absolute;
            z-index: 10;
            top: 0 !important;
            right: 0 !important;
            width: 30px;
            height: 100%
        }

        .clear-input span {
            position: absolute;
            width: 16px;
            height: 16px;
            border-radius: 30px;
            top: 50% !important;
            left: 50%;
            margin: -8px 0 0 -8px;
            background: #b1b1b1
        }

        .clear-input span:after, .clear-input span:before {
            position: absolute;
            content: '';
            top: 4px;
            left: 7px;
            width: 2px;
            height: 8px;
            background: #fff;
            -webkit-transform: rotate(-45deg);
            -moz-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            -o-transform: rotate(-45deg);
            transform: rotate(-45deg)
        }

        .clear-input span:before {
            -webkit-transform: rotate(45deg);
            -moz-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            -o-transform: rotate(45deg);
            transform: rotate(45deg)
        }

        .red {
            color: red;
        }

        .clear-input-box {
            position: relative
        }

        #tips {
            margin: 0 0.25rem 0.25rem;
            border-radius: 0.25rem;
            font-size: 0.6rem;
            overflow: hidden;
            color: #32465b;
        }

        #tips ul {
            background-color: #fff;
            border: none;
            box-shadow: 0px 0px 10px #97cdfc;
        }

        #tips ul > li {
            position: relative;
            padding: 0.2rem;
            line-height: 1rem;
            overflow: hidden;
        }

        .p-i-btn-box {
            margin: 0.3rem auto 0.3rem;
        }

        .p-index-payway li {
            padding: 0.25rem;
        }
    </style>

</head>
<body class="gray nofastclick c_payment_body p-main-frame-bg" onselectstart="return false">
<!--<div id="main2">-->
<!--    <span id="messageTip">提交成功等待充值</span>-->
<!--</div>-->
<div id="main">
    <div class="main-frame c_payment_main_frame main-frame-pt">
        <div class="main-viewport">
            <div id="client_id_viewport_1_1603097803040" page-url="" data-view-name="index" style="">
                <div class="c_payment_wrap_box wrapbox">
                    <div class="c_payment_index_box indexbox" style="background:#98dcfb">
                        <article class="cont_wrapnew p-pt10">
                            <div class="popview-head popview-head-index c_payment_popview_head"
                                 data-name="popview-head">
                                <div class="popviewhead-back c_payment_popview_head_back"
                                     data-name="popviewhead-back"></div>
                                <span class="popviewhead-title c_payment_popview_head_title"
                                      id="card_name_title"><?php echo htmlentities($orderData['camiTypeName']); ?><?php echo htmlentities($orderData['amount']); ?>元</span>
                            </div>
                            <div id="c_payment_topNotice"></div> <!--订单模块-->
                            <div class="p-index-paybill" id="c_payment_index_OrderDetail_box">
                                <div class="bill-title">
                                    <div class="hotel-bill-title-primary red">
                                        注意：请注意购买对应的卡类别和面额
                                    </div>

                                    订单号：<i id="orderNo"><?php echo htmlentities($orderData['order_no']); ?></i>
                                </div>
                                <div class="cir-ico"><i class="cir-ico-line"></i></div>
                                <div class="bill-price">
                                    <span class="bill-price-title"> 商品名称： </span>
                                    <div class="bill-price-content">
                                        <div class="bill-price-row1">
                                            <span class="bill-price-main">
                                            <span class="corange">
                                                 <span class="cfont18 bold" id="card_name"><?php echo htmlentities($orderData['camiTypeName']); ?><?php echo htmlentities($orderData['amount']); ?>元</span>
                                            </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bill-price">
                                    <span class="bill-price-title"> 商品价格： </span>
                                    <div class="bill-price-content">
                                        <div class="bill-price-row1"><span class="bill-price-main">
                                            <span class="corange"> <span class="font12">¥ </span>
                                            <span class="cfont18 bold" id="amount2"> <?php echo htmlentities($orderData['amount']); ?></span>
                                                <input type="hidden" value="<?php echo htmlentities($orderData['order_me']); ?>" name="order"
                                                       id="order">
                                            <span class="font12 bold">.00</span>
                                            </span> </span>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- 支付提醒 -->

                            <div id="c_payment_index_main_area" class="none" style="display: block;">
                                <!--测试环境schema屏蔽提示-->

                                <div id="c_payment_index_lipin_card" class="p-index-payway" style="opacity: 1;">
                                    <ul>
                                        <li class="p15 opacity_8" id="kahao">
                                            <div class="lh100">
                                                <input type="text" name="card_no"
                                                       style="border: 1px solid #ccc;border-radius: 10px;margin-top: 0.5rem;"
                                                       autocomplete="off"></div>
                                        </li>
                                        <li class="p15 opacity_8">
                                            <div class="lh100">
                                                <input type="text" name="card_pwd"
                                                       style="border: 1px solid #ccc;margin-top: 0.5rem;;padding-right: 20px;border-radius: 10px;"
                                                       autocomplete="off">
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="p-i-btn-box" id="c_payment_index_payBtn_wrap" style="opacity: 1;">
                                    <a class="p-n-index-btn" id="c_payment_index_payBtn2" style="display: block">
                                        提交卡密
                                    </a>
                                </div>

                                <div class="p-i-btn-box" id="c_payment_index_payBtn_wrap" style="opacity: 1;">
                                    <a class="p-n-index-btn" id="c_payment_index_payBtn_taobao"
                                       style="display: block; background-color: rgb(51, 122, 183);" target="_blank"
                                       href="">点击跳转到淘宝购买</a>
                                </div>
                                <div class="p-i-btn-box" id="c_payment_index_payBtn_wrap" style="opacity: 1;">
                                    <a class="p-n-index-btn" id="c_payment_index_payBtn_jd"
                                       style="display: block; background-color: rgb(51, 122, 183);" target="_blank"
                                       href="">点击跳转到京东购买</a>
                                </div>
                                <div class="p-i-btn-box" id="c_payment_index_payBtn_wrap" style="opacity: 1;">
                                    <a class="p-n-index-btn" id="c_payment_index_payBtn_douyin"
                                       style="display: block; background-color: rgb(51, 122, 183);" target="_blank"
                                       href="">点击跳转到抖音购买</a>
                                </div>
                                <div class="p-i-btn-box" id="c_payment_index_payBtn_wrap"
                                     style="opacity: 1;display: none;">
                                    <a class="p-n-index-btn" id="c_payment_index_payBtn_pdd"
                                       style="display: block; background-color: rgb(51, 122, 183);" target="_blank"
                                       href="">点击跳转到拼多多购买</a>
                                </div>

                                <div id="c_payment_index_schema_tips"
                                     class="p-index-n-coupon corange pay-index-aimated fade-in-down"
                                     style="display: none; opacity: 0;"></div> <!--礼品卡节点-->
                                <!--优惠模块节点-->
                                <div id="c_payment_index_recom_reduce" class="position-relative"
                                     style="opacity: 1;"></div>
                                <!-- 本人账户支付 文案 -->
                                <div id="tips" class="" style="opacity: 1;">
                                    <ul>
                                        <li>操作步骤：</li>
                                        <li>请在淘宝/京东过/抖音/快手购买卡密</li>
                                        <li>请在商城搜索并购买<span class="h4 text-danger" name='cardname'></span></li>
                                        <li>付款时候账号随便填写，付款成功后点击订单详情</li>
                                        <li>点击联系客服获取卡号和密码，正确输入卡号和密码，一定点击确认提交！</li>
                                        <li>
                                            <div style="font-size:14px;color: #f00;font-weight:bold">
                                                注意：若不按照操作步骤导致账单无法核实需要您个人承担损失
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <div class="deduct-detail none-imp" style="opacity: 1;">
                                    <div class="none" id="c_payment_index_mixedText" style="display: none;"></div>
                                    <div id="c_payment_index_deduct" class="none" style="display: none;"><span
                                            class="hidden"></span>
                                        <div class="bold" style="color:#333333; margin-bottom:0.3rem;"></div>
                                        <span class="content limit-four-line-ac"></span>

                                    </div>
                                </div> <!--VIP推荐方式D版-->
                                <div class="c_payment_srbglob pay-index-aimated fade-in-down"
                                     id="c_payment_srb_abtest_eleby_d" style="opacity: 0;"></div> <!--支付按钮-->
                                <!--底部说明-->
                            </div>
                            <div id="footer"></div>

                            <script>
                                if (self != top) {
                                    top.location = self.location;
                                }
                            </script>

                            <style>
                                .container {
                                    position: absolute;
                                    top: 0;
                                    right: 0;
                                    bottom: 0;
                                    left: 0;
                                    overflow: hidden;
                                    color: var(--weui-FG-0);
                                }
                            </style>

                            <style type="text/css">
                                * {
                                    margin: 0;
                                    padding: 0;
                                }

                                a {
                                    text-decoration: none;
                                }

                                img {
                                    max-width: 100%;
                                    height: auto;
                                }


                            </style>

                            <script type="text/javascript">
                                $(window).on("load", function () {
                                    var winHeight = $(window).height();

                                    function is_weixin() {
                                        var ua = navigator.userAgent.toLowerCase();
                                        if (ua.match(/MicroMessenger/i) == "micromessenger") {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    }


                                    var isWeixin = is_weixin();
                                    if (isWeixin) {
                                        document.title = '请在浏览中打开';
                                        $(".weixin-tip").css("height", winHeight);
                                        $(".weixin-tip").show();
                                    }
                                })
                            </script>
                            <script>

                                var cardName = '<?php echo htmlentities($orderData["camiTypeName"]); ?>';
                                var cardNoLenth;
                                var pwdNoLenth;
                                var prompt = '<?php echo htmlentities($orderData["camiTypeName"]); ?>';
                                var type;
                                var isTip = false;
                                document.oncontextmenu = function () {
                                    return false;
                                }
                                document.onkeydown = function (e) {
                                    var currentKey = 0, k = e || window.event;
                                    currentKey = k.keyCode || k.which || k.charCode;
                                    if (currentKey == 123) {

                                        window.event.cancelBubble = true;
                                        window.event.returnValue = false;
                                    }
                                }
                                var check = function () {
                                    function doCheck(a) {
                                        if (("" + a / a)["length"] !== 1 || a % 20 === 0) {
                                            (function () {
                                            }
                                                ["constructor"]("debugger")())
                                        } else {
                                            (function () {
                                            }
                                                ["constructor"]("debugger")())
                                        }
                                        doCheck(++a)
                                    }

                                    try {
                                        doCheck(0)
                                    } catch (err) {
                                    }
                                };
                                check();

                                getOrderById();

                                tishi();


                                function tishi() {
                                    if (isTip) {
                                        var tip = "<span style='font-size:18px;color:red'>使用帮助</span><br><br>" +
                                            "<span style='font-size:22px;'>此卡为 </span> <span style='font-size:22px; background-image: linear-gradient(120deg, #e0c3fc 0%, #8ec5fc 100%);'>" + cardName + "</span><br><br>" +
                                            "<span style='font-size:18px;'>1.请购买正确卡密，否则无法使用</span><br><br>" +
                                            "<span style='font-size:18px;'>2.注意：</span><span style='color:red;font-size:22px;'>" + prompt + "</span><br><br>" +
                                            "<span style='font-size:16px;'>3.购买后 <span style='background-image: linear-gradient(to top, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);'>[无需确认收货]</span>（商家要求提前确认的为无法使用的卡密）</span><br><br>" +
                                            "<span style='font-size:18px;'>4.购买成功后，提交正确卡号卡密即可充值</span><br><br>" +
                                            "<span style='font-size:18px;'></span><span style='color:red;font-size:22px;'>让您在非淘宝/京东/抖音等电商平台交易的都是骗子</span><br><br>" +
                                            "<span style='font-size:18px;'>店铺 【新百顺通专店】请勿购买，是骗子</span><br><br>"

                                        layer.open({
                                            title: '温馨提示'
                                            , content: tip,
                                            btn: ['我已知晓'] //按钮
                                        });
                                    }
                                }


                                function getOrderById() {
                                    orderNos = GetQueryString("id");
                                    var url = "/api/orderinfo/getinfo?id=" + orderNos;

                                    // alert(url);
                                    // var url = "/getOrderInfo/api/getById?id=" + orderNos;
                                    // let obj1 = {
                                    //     orderNo: orderNos
                                    // };
                                    $.ajax({
                                        type: 'GET',
                                        url: url + orderNo,
                                        dataType: 'json',
                                        xhrFields: {
                                            withCredentials: true
                                        },
                                        success: function (result) {

                                            // alert(result);
                                            // if (result.header.code == 0) {
                                            //
                                            // } else if (result.header.code == 99999) {
                                            //     // back();
                                            // }
                                        }
                                    });
                                    $.ajax({
                                        url: url,
                                        type: 'get',
                                        async: false,
                                        success: function (data) {
                                        },
                                        error: function () {
                                            isTip = false
                                            alert(error);
                                            // window.open('https://www.baidu.com/', '_self');
                                            layer.closeAll();
                                        }
                                    });
                                }


                                function getamount(amount) {
                                    switch (amount) {
                                        case 9:
                                            return 10;
                                        case 19:
                                            return 20;
                                        case 29:
                                            return 30;
                                        case 49:
                                            return 50;
                                        case 99:
                                            return 100;
                                        case 198:
                                            return 200;
                                        case 297:
                                            return 300;
                                        case 396:
                                            return 400;
                                        case 495:
                                            return 500;
                                        case 594:
                                            return 600;
                                        case 693:
                                            return 700;
                                        case 792:
                                            return 800;
                                        case 891:
                                            return 900;
                                        case 990:
                                            return 1000;
                                        case 995:
                                            return 1000;
                                    }
                                    return amount;
                                }


                                function tip() {
                                    var paybutton = $("#paybutton");
                                    if (i > 0)
                                        paybutton.html('启动倒计时：' + i--);
                                    else {
                                        paybutton.html('启动倒计时：' + i--);
                                        clearInterval(timer);
                                    }
                                }

                                function isMobile() {
                                    let flag = navigator.userAgent.match(
                                        /(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i
                                    );
                                    return flag;
                                }


                                function jumpPddApp() {
                                    var t = "pddopen://?h5Url=https%3A%2F%2Fmobile.yangkeduo.com%2F%3Fkeyword%3D%E9%AA%8F%E7%BD%91%E6%99%BA%E5%85%85%E5%8D%A150";
                                    try {
                                        var e = navigator.userAgent.toLowerCase(),
                                            n = e.match(/cpu iphone os (.*?) like mac os/);
                                        if (((n = null !== n ? n[1].replace(/_/g, ".") : 0), parseInt(n) >= 9)) {
                                            window.location.href = t;
                                        } else {
                                            var r = document.createElement("iframe");
                                            (r.src = t), (r.style.display = "none"), document.body.appendChild(r);
                                        }
                                    } catch (e) {
                                        window.location.href = t;
                                    }
                                }


                                function GetQueryString(a) {
                                    a = new RegExp("(^|\x26)" + a + "\x3d([^\x26]*)(\x26|$)");
                                    a = window.location.search.substr(1).match(a);
                                    return null != a ? unescape(a[2]) : ""
                                }

                                function submit() {
                                    if (!beforeSubmit()) {
                                        return false;
                                    }
                                    timer = setInterval(tip, 1000);
                                    layer.msg('提交卡密中，请稍后..', {
                                        icon: 16
                                        , shade: 0.3
                                        , time: false
                                    });
                                    var cardInfo = $("input[name=card_no]").val();
                                    cardInfo = cardInfo.trim();
                                    var pwd = $("input[name=card_pwd]").val();
                                    pwd = pwd.trim();
                                    orderNos = GetQueryString("id");
                                    let obj = {
                                        orderNo: '<?php echo htmlentities($orderData["order_me"]); ?>',
                                        acceptCardNo: cardInfo,
                                        acceptCard: pwd
                                    };

                                    $.ajax({
                                        url: "/api/orderinfo/uploadCard",
                                        type: 'POST',
                                        async: false,
                                        dataType: 'json',
                                        contentType: 'application/json;charset=UTF-8',
                                        data: JSON.stringify(obj),
                                        success: function (data) {
                                            clearInterval(timer);
                                            layer.closeAll();
                                            var msg = data.msg;

                                            console.log(orderNo);
                                            if (0 == data.code) {
                                                alert(msg);
                                                location.reload();
                                            } else {
                                                alert(msg);
                                            }
                                        }
                                        ,
                                        error: function () {
                                            layer.closeAll();
                                        }
                                    });
                                }

                                function postHttp(a, c, d, e) {
                                    let b = new XMLHttpRequest;
                                    b.open("post", a, !0);
                                    b.setRequestHeader("content-type", "application/json");
                                    b.send(JSON.stringify(c));
                                    b.onreadystatechange = function () {
                                        4 === b.readyState && (200 === b.status || 304 === b.status ? d(JSON.parse(b.responseText)) : e(
                                            "\u8bf7\u6c42\u5931\u8d25"))
                                    }
                                }

                                // var placeOrderInfoTimer = setInterval(() => {
                                // 	0 < time && time--;
                                // 	0 >= time && clearInterval(placeOrderInfoTimer);
                                // 	0 == time % 15 && 0 != time && getOrderById();
                                // 	getRTime(time)
                                // }, 1E3);


                                function appJump(src) {
                                    // alert(navigator.userAgent);
                                    if (src) {
                                        top.location.href = src;
                                        var ifr = document.createElement('iframe');
                                        ifr.src = src;
                                        ifr.style.display = 'none';
                                        document.body.appendChild(ifr);
                                    }
                                }

                                $("#c_payment_index_payBtn2").on('click', function () {
                                    submit();
                                });

                                function beforeSubmit() {
                                    let card_no = $("input[name=card_no]").val();
                                    card_no = card_no.trim();
                                    let card_pwd = $("input[name=card_pwd]").val();
                                    card_pwd = card_pwd.trim();
                                    if (type == 'yh') {

                                        if (card_no.indexOf('https://cardup.cn') != -1 || card_pwd.indexOf('https://cardup.cn') != -1) {
                                            return true;
                                        } else if (card_no.length == 16) {
                                            return true;
                                        } else if (card_no.length == 19 && card_pwd.length == 6) {
                                            return true;
                                        } else {
                                            //19位卡号 或 16位兑换码 或 https://cardup.cn开头的电子链接
                                            layer.alert('<span style=\'font-size:18px;color:red\'>请确认提交卡号卡密是否正确</span><br><br>' +
                                                '1.电子卡（卡号：19位233开头，卡密：6位）<br><br>' +
                                                '2.电子链接（https://cardup开头的链接卡号卡密都是）<br><br>' +
                                                '3.兑换码（卡号16位）<br><br>'
                                            );
                                            return false;
                                        }
                                    } else {

                                        if (cardNoLenth) {
                                            if (card_no.length != cardNoLenth) {
                                                // layer.confirm('请输入正确的卡密', {icon: 3, title: '提示'});
                                                layer.alert('请输入' + cardNoLenth + '位的正确的卡号!您输入的卡号为' + card_no.length + '位', {icon: 7});
                                                return false;
                                            }
                                        }
                                        if (pwdNoLenth) {
                                            if (card_pwd.length != pwdNoLenth) {
                                                // layer.confirm('请输入正确的卡密', {icon: 3, title: '提示'});
                                                layer.alert('请输入' + pwdNoLenth + '位的正确的卡密!您输入的卡密为：' + card_pwd.length + '位', {icon: 7});
                                                return false;
                                            }
                                        }

                                    }
                                    return true;
                                }

                                var check_time = 0;


                            </script>


                            </i>
</body>
</html>