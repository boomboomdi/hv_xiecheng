<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return [
    'app\shell\Checkceshir',  //测试
    'app\shell\Timenotifyorder',  //测试
    'app\shell\Timecheckorder',  //查单订单表等待付款订单
    'app\shell\Checkorder',  //查单订单表等待付款订单
    'app\shell\Notifynopayorder',  //查单订单表等待付款订单
    'app\shell\Notifynopayhx',  //定时回调核销 支付失败
];
