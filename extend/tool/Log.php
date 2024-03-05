<?php
/**
 * Created by PhpStorm.
 * User: NickBai
 * Email: 876337011@qq.com
 * Date: 2019/10/30
 * Time: 8:19 PM
 */

namespace tool;

use app\admin\model\Operate;
use app\common\model\OrderoperateLog;

class Log
{
    public static function write($content)
    {
        $controller = lcfirst(request()->controller());
        $action = request()->action();
        $checkInput = $controller . '/' . $action;

        $logModel = new Operate();
        $logModel->writeLog([
            'operator' => session('admin_user_name'),
            'operator_ip' => request()->ip(),
            'operate_method' => $checkInput,
            'operate_desc' => $content,
            'operate_time' => date('Y-m-d H:i:s')
        ]);
    }

    //订单日志表
    public static function OrderLog($user, $orderNo, $content)
    {
        $controller = lcfirst(request()->controller());
        $action = request()->action();
        $checkInput = $controller . '/' . $action;

        $logModel = new OrderoperateLog();
        $logModel->writeLog([
            'operator' => $user,
            'operator_ip' => request()->ip(),
            'order_no' => $orderNo,
            'operate_method' => $checkInput,
            'operate_desc' => $content,
            'operate_time' => date('Y-m-d H:i:s')
        ]);
    }
}