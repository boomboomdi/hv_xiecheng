<?php

namespace app\api\controller;


use app\admin\model\WriteoffModel;
use Couchbase\IndexFailureException;
use think\Controller;
use think\Db;
use app\common\model\CamiChannelModel;
use app\common\model\OrderhexiaoModel;
use app\common\model\OrderModel;
use app\api\validate\OrderinfoValidate;
use app\api\validate\NotifycardValidate;
use app\api\validate\NotifyzhoucardValidate;
use app\api\validate\CheckPhoneAmountNotifyValidate;
use think\Request;
use app\common\model\SystemConfigModel;
use app\common\model\OrderoperateLog;
use phpseclib\Crypt\AES;

use tool\Log;
use think\Validate;
use app\common\Redis;

header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Credentials:true");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept,Authorization");
header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS,PATCH');

class Cardinfo extends Controller
{

    /**
     * 携程卡密上传回调
     * @param Request $request
     * @return void
     */
    public function cardUploadNotify(Request $request)
    {

        $data = @file_get_contents('php://input');
        $message = json_decode($data, true);//获取 调用信息
//
        $appKey = "qG4UnbXxzgxdI6VU";
        $secret = "X5WwO3OlrGNFTXn35Dut2MBqJFZLl9NU";
        $encryptPassword = "VhClL3zB55pfCN8mdIJpt9B3VwLNCRMd";
        $headers = $request->header();

//        var_dump($request->param());exit;

        logs(json_encode([
            'data' => $message,
            "message" => $message,
            "param" => $request->param()
        ]), 'cardUploadNotify_first');
        if (!isset($headers['appkey']) || empty($headers['appkey'])) {
            return apiJsonReturn(-2, "appKey  require！");
        }
        if ($appKey != $headers['appkey']) {
            return apiJsonReturn(-3, "appKey  error！");
        }
//        if ($request->ip() != '8.129.63.100') {
//            return apiJsonReturn(-4, "  error ip");
//        }
        $db = new Db();
        try {
            logs(json_encode(['data' => $message, "message" => $message]), 'cardUploadNotify_first');
            $validate = new NotifycardValidate();
            if (!$validate->check($message)) {
                return apiJsonReturn(-1, '', $validate->getError());
            }

            $objectMap = $message;
            unset ($objectMap['sign']);
            // 对字典进行排序并转换为JSON字符串
            if (is_array($objectMap['cardList'])) {
                foreach ($objectMap['cardList'] as $k => $v) {
                    ksort($objectMap['cardList'][$k]);
                }
            }

            ksort($objectMap);
            $jsonStr = json_encode($objectMap, JSON_UNESCAPED_SLASHES);
            // 拼接签名字符串并计算MD5
            $textStr = "{$appKey}{$jsonStr}{$secret}";
            $sign = md5($textStr);
            if ($sign != $message['sign']) {
                logs(json_encode([
                    'data' => $message,
                    "message" => $message,
                    "param" => $request->param(),
                    "sign" => $sign
                ]), 'cardUploadNotifySignError');

//                return apiJsonReturn(-4, "sign  error！");
            }


            logs(json_encode(['data' => $message, "message" => $message]), 'cardUploadNotify_two');
            $orderData = $db::table('bsa_order')->where('account', '=', $message['uploadId'])->find();
            if (empty($orderData)) {

                logs(json_encode([
                    'data' => $message,
                    "lastSql" => $db::table('bsa_order')->getLastSql()
                ]), 'cardUploadNotify_5');
                return apiJsonReturn(-5, "该订单号不存在！");
            }

            if ($orderData['pay_status'] == 1) {
                echo "success";
                return apiJsonReturn(-5, "该订单号已支付成功！");
            }
            $cardDta = $message['cardList'][0];
            if (isset($cardDta['cardName']) && $cardDta['cardName'] != $orderData['cami_account'] || !isset($cardDta['state'])) {
                logs(json_encode(
                    [
                        'data' => $message,
                        "message" => $message,
                        "param" => $request->param(),
                        'orderNo' => $orderData['order_no'],
                        'uploadId' => $orderData['account'],
                        'notifyresponse' => $cardDta,
                        'time' => date("Y-m-d H:i:s", time()),
                    ]
                ), 'cardUploadNotify_differentcardName_log');

                return apiJsonReturn(-6, "cardName different！");
            }
            //充值成功
            if (isset($cardDta['state']) && $cardDta['state'] == '充值成功') {
                $updateCheckData['check_result'] = var_export($message, true);
                $db::startTrans();

                //更新订单  START
                //1、lock order start
                $orderFind = $db::table('bsa_order')->where('id', '=', $orderData['id'])->lock(true)->find();
                if (!$orderFind) {
                    logs(json_encode(
                        [
                            'data' => $message,
                            "message" => $message,
                            "param" => $request->param(),
                            'orderNo' => $orderData['order_no'],
                            'uploadId' => $orderData['account'],
                            'notifyresponse' => $cardDta,
                            'time' => date("Y-m-d H:i:s", time()),
                        ]
                    ), 'cardUploadNotifyLockOrderFailLog');
                    $db::rollback();
                    return apiJsonReturn(-102, "exception ！");
                }
                //1、lock order end
                //2、修改订单状态  == = = = 成功  start
                $updateCheckData['order_desc'] = "通道异步回调：充值成功，订单状态：等候回调";  //支付成功状态
                $updateCheckData['order_status'] = 1;  //支付成功状态
                $updateCheckData['pay_status'] = 1;  //支付成功状态
                $updateCheckData['pay_time'] = time();  //支付成功状态
                $updateCheckData['actual_amount'] = $cardDta['amount'];  //支付绑定金额
                //如果订单金额与卡密金额不符合  ====
                if ($cardDta['amount'] != $orderFind['amount']) {
                    $updateCheckData['order_desc'] = "通道异步回调：充值成功，订单状态：差额拒回";  //支付成功状态
                    $updateCheckData['do_notify'] = 2;  //拒绝回调
                    $updateCheckData['notify_status'] = 2;  //拒绝回调
                }
                //如果订单金额与卡密金额不符合  ====  end
                $updateCheckWhere['order_no'] = $orderFind['order_no'];
                $updateOrderStatus = $db::table("bsa_order")->where($updateCheckWhere)
                    ->update($updateCheckData);
                if (!$updateOrderStatus) {
                    $db::rollback();
                    logs(json_encode([+
                    'action' => 'updateMatch',
                        'updateOrderWhere' => $updateCheckWhere,
                        'updateCheckData' => $updateCheckData,
                        'updateSql' => $db::table("bsa_order")->getLastSql(),
                        'updateOrderSuccessRes' => $updateOrderStatus,
                    ]), 'checkOrderUpdateOrderStatus');
                    return apiJsonReturn(-103, "exception ！");
                }
                //更新订单  END
                //修改核销商金额
                $bsaWriteOffData = $db::table("bsa_write_off")
                    ->where('write_off_sign', '=', $orderFind['write_off_sign'])
                    ->lock(true)
                    ->find();
                if (!$bsaWriteOffData) {
                    $db::rollback();
                    logs(json_encode([
                        'order_no' => $orderFind['order_no'],
                        'errorMessage' => "pay_success_lock_write_off_fail",
                        'last_sql' => $db::table("bsa_write_off")->getLastSql()
                    ]), 'notifyOrderLockWriteFail2');

                    return apiJsonReturn(-104, "exception！");
                }

                $rateWhere['write_off_sign'] = $orderFind['write_off_sign'];
                $rateWhere['cami_type_sign'] = $orderFind['operator'];
                $rete = $db::table("bsa_cami_write")->where($rateWhere)->find()['rate'];
                $freezeAmount = ($orderFind['amount'] * (1 - $rete));

                //支付成功 核销商上压金额增加
                $updateWriteData['freeze_amount'] = $bsaWriteOffData['freeze_amount'] - number_format((float)$freezeAmount, 3);
                $updateWriteData['write_off_deposit'] = $bsaWriteOffData['write_off_deposit'] - number_format((float)$freezeAmount, 3);
//                                    $updateWriteOff = $db::table("bsa_write_off")
//                                        ->where('write_off_sign', '=', $v['write_off_sign'])
//                                        ->update($updateWriteData);
                $updateWriteOff = $db::table("bsa_write_off")
                    ->execute("UPDATE bsa_write_off  SET 
                                            freeze_amount = freeze_amount - " . (number_format($freezeAmount, 3)) . " ,
                                            write_off_deposit = write_off_deposit - " . (number_format($freezeAmount, 3)) . " 
                                            WHERE  write_off_id = " . $bsaWriteOffData['write_off_id']);
//                                    execute("UPDATE bsa_write_off  SET
//                                                   freeze_amount = freeze_amount - " . (number_format((float)$freezeAmount, 3)) . " , write_off_deposit - " . (number_format((float)$freezeAmount, 3)) . " WHERE  write_off_id = " . $bsaWriteOffData['write_off_id']);

                if ($updateWriteOff != 1) {

                    logs(json_encode([
                        'updateCamiChannelWhere' => $orderFind['write_off_sign'],
                        'updateSql' => $db::table("bsa_write_off")->getLastSql(),
                        'updateMatchSuccessRes' => $updateWriteOff,
                    ]), 'notifyOrderUpdateWriteOffStatus');
                    $db::rollback();

                    return apiJsonReturn(-105, "exception！");
                }
                $db::commit();
                echo "success";
                exit;
            }


            return json(msg(111, "error"));
        } catch (\Error $error) {

            logs(json_encode(['file' => $error->getFile(),
                'line' => $error->getLine(), 'errorMessage' => $error->getMessage()
            ]), 'cardUploadNotifyError');
            return json(msg(-22, '', "接口异常!-22"));
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage(),
                'lastSql' => $db::table('bsa_order')->getLastSql(),
            ]), 'cardUploadNotifyException');
            return json(msg(-11, '', "接口异常!-11"));
        }
    }


    /**
     * 沃尔玛 - 特斯拉
     * @param Request $request
     * @return void
     */
    public function cardNotify(Request $request)
    {
        //162.209.166.42

        $data = @file_get_contents('php://input');
        $message = json_decode($data, true);//获取 调用信息

        logs(json_encode(['data' => $message, "message" => $message]), 'TslcardUploadNotify_first1`');
//
        if (!isset($message['sign']) || empty($message['sign'])) {
            return apiJsonReturn(-1, 'require sign', '');
        }
        if (!isset($message['data']) || empty($message['data'])) {
            return apiJsonReturn(-1, 'require data');
        }
        $validate = new NotifycardValidate();
        if (!$validate->check($message['data'])) {
            return apiJsonReturn(-1, $validate->getError());
        }

        logs(json_encode(['data' => $message, "message" => $message]), 'TslcardUploadNotify_first1`');

        $db = new Db();
        try {
            $secret = 'af6faf8c38294ef9bc10878b9947ca68b937a5437c8f4e9daf3b84e68a49f367';
            logs(json_encode(['data' => $message, "message" => $message]), 'TslcardUploadNotify_first');
            $param = $message['data'];
            unset($param['message']);
            ksort($param);
            $sign = urldecode(http_build_query($param,"&"));
            $sign = str_replace(' ','',$sign);
            $sign = md5($sign . "&secret=" . $secret);

            if ($message['sign'] != $sign) {
                return apiJsonReturn(-2, 'sign error', $validate->getError());
            }
            $orderData = $db::table('bsa_order')->where('order_me', '=', $param['bizOrderNo'])->find();
            if (empty($orderData)) {
                logs(json_encode([
                    'data' => $message,
                    "lastSql" => $db::table('bsa_order')->getLastSql()
                ]), 'cardUploadNotify_5');
                return apiJsonReturn(-5, "order does not exist！");
            }

            if ($orderData['pay_status'] == 1) {
                echo "success";
                return apiJsonReturn(-5, "该订单号已支付成功！");
            }

            //充值成功
            //200	绑定成功
            //201	绑定成功 （金额与订单不符）
            //511	已被绑定过的卡密
            //500	失败状态（其他失败原因
            if (isset($param['bindState']) && ($param['bindState'] == '200'||$param['bindState']=='201')) {
                $updateCheckData['check_result'] = var_export($message, true);
                $db::startTrans();

                //更新订单  START
                //1、lock order start
                $orderFind = $db::table('bsa_order')->where('id', '=', $orderData['id'])->lock(true)->find();
                if (!$orderFind) {
                    logs(json_encode(
                        [
                            'data' => $message,
                            "message" => $message,
                            "param" => $request->param(),
                            'orderNo' => $orderData['order_no'],
                            'notifyresponse' => $param,
                            'time' => date("Y-m-d H:i:s", time()),
                        ]
                    ), 'cardUploadNotifyLockOrderFailLog');
                    $db::rollback();
                    return apiJsonReturn(-102, "exception 11！");
                }
                //1、lock order end
                //2、修改订单状态  == = = = 成功  start
                $updateCheckData['order_desc'] = "通道异步回调：充值成功，订单状态：等候回调";  //支付成功状态
                $updateCheckData['order_status'] = 1;  //支付成功状态
                $updateCheckData['pay_status'] = 1;  //支付成功状态
                $updateCheckData['pay_time'] = time();  //支付成功状态
                $updateCheckData['actual_amount'] = $param['amount'];  //支付绑定金额
                //如果订单金额与卡密金额不符合  ====
                if ($param['bindState']=='201') {
                    $updateCheckData['order_desc'] = "通道异步回调：充值成功，订单状态：差额拒回";  //支付成功状态
                    $updateCheckData['do_notify'] = 2;  //拒绝回调
                    $updateCheckData['notify_status'] = 2;  //拒绝回调
                }
                //如果订单金额与卡密金额不符合  ====  end
                $updateCheckWhere['order_no'] = $orderFind['order_no'];
                $updateOrderStatus = $db::table("bsa_order")->where($updateCheckWhere)
                    ->update($updateCheckData);
                if (!$updateOrderStatus) {
                    $db::rollback();
                    logs(json_encode([+
                    'action' => 'updateMatch',
                        'updateOrderWhere' => $updateCheckWhere,
                        'updateCheckData' => $updateCheckData,
                        'updateSql' => $db::table("bsa_order")->getLastSql(),
                        'updateOrderSuccessRes' => $updateOrderStatus,
                    ]), 'checkOrderUpdateOrderStatus');
                    return apiJsonReturn(-103, "exception ！");
                }
                //更新订单  END
                //修改核销商金额
                $bsaWriteOffData = $db::table("bsa_write_off")
                    ->where('write_off_sign', '=', $orderFind['write_off_sign'])
                    ->lock(true)
                    ->find();
                if (!$bsaWriteOffData) {
                    $db::rollback();
                    logs(json_encode([
                        'order_no' => $orderFind['order_no'],
                        'errorMessage' => "pay_success_lock_write_off_fail",
                        'last_sql' => $db::table("bsa_write_off")->getLastSql()
                    ]), 'notifyOrderLockWriteFail2');

                    return apiJsonReturn(-104, "exception！");
                }

                $rateWhere['write_off_sign'] = $orderFind['write_off_sign'];
                $rateWhere['cami_type_sign'] = $orderFind['operator'];
                $rete = $db::table("bsa_cami_write")->where($rateWhere)->find()['rate'];
                $freezeAmount = ($orderFind['amount'] * (1 - $rete));

                //支付成功 核销商上压金额增加
                $updateWriteData['freeze_amount'] = $bsaWriteOffData['freeze_amount'] - number_format((float)$freezeAmount, 3);
                $updateWriteData['write_off_deposit'] = $bsaWriteOffData['write_off_deposit'] - number_format((float)$freezeAmount, 3);
//                                    $updateWriteOff = $db::table("bsa_write_off")
//                                        ->where('write_off_sign', '=', $v['write_off_sign'])
//                                        ->update($updateWriteData);
                $updateWriteOff = $db::table("bsa_write_off")
                    ->execute("UPDATE bsa_write_off  SET 
                                            freeze_amount = freeze_amount - " . (number_format($freezeAmount, 3)) . " ,
                                            write_off_deposit = write_off_deposit - " . (number_format($freezeAmount, 3)) . " 
                                            WHERE  write_off_id = " . $bsaWriteOffData['write_off_id']);
//                                    execute("UPDATE bsa_write_off  SET
//                                                   freeze_amount = freeze_amount - " . (number_format((float)$freezeAmount, 3)) . " , write_off_deposit - " . (number_format((float)$freezeAmount, 3)) . " WHERE  write_off_id = " . $bsaWriteOffData['write_off_id']);

                if ($updateWriteOff != 1) {

                    logs(json_encode([
                        'updateCamiChannelWhere' => $orderFind['write_off_sign'],
                        'updateSql' => $db::table("bsa_write_off")->getLastSql(),
                        'updateMatchSuccessRes' => $updateWriteOff,
                    ]), 'notifyOrderUpdateWriteOffStatus');
                    $db::rollback();

                    return apiJsonReturn(-105, "exception！");
                }
                $db::commit();
                echo "success";
                exit;
            }


            return json(msg(111, "error"));
        } catch (\Error $error) {

            logs(json_encode(['file' => $error->getFile(),
                'line' => $error->getLine(), 'errorMessage' => $error->getMessage()
            ]), 'cardUploadNotifyError');
            return json(msg(-22, '', "接口异常!-22"));
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage(),
                'lastSql' => $db::table('bsa_order')->getLastSql(),
            ]), 'cardUploadNotifyException');
            return json(msg(-11, '', "接口异常!-11"));
        }
    }

    /**
     * 玩家点击支付方式
     * @return \think\response\Json
     */
    public function addOrderPayType()
    {
        $data = @file_get_contents('php://input');
        $param = json_decode($data, true);
        logs(json_encode(['param' => $param]), 'addOrderPayType');
        if (!isset($param['payType']) || empty($param['payType'])) {
            return json(msg(-1, '', "payType error"));
        }
        if (!isset($param['order_no']) || empty($param['order_no'])) {
            return json(msg(-1, '', "order_no error"));
        }
        try {
            $orderShowTime = SystemConfigModel::getOrderShowTime();
            $db = new Db();
//            $orderModel = new OrderModel();
            $orderInfo = $db::table("bsa_order")
                ->where("order_no", "=", $param['order_no'])
                ->find();
            if (empty($orderInfo)) {
                return json(msg(-1, '', "order error"));
            }
            if (($orderInfo['add_time'] + $orderShowTime) < time()) {
                return json(msg(-2, '', "order time out"));
            }
            $updateData['user_ip'] = getLocationByIp(request()->ip());
            $updateData['click_time'] = time();
            $updateData['pay_name'] = htmlspecialchars($param['payType']);
            $db::table("bsa_order")
                ->where("order_no", "=", $param['order_no'])
                ->update($updateData);
            return json(msg(0, '', "success"));
        } catch (\Error $error) {
            logs(json_encode(['file' => $error->getFile(),
                'line' => $error->getLine(), 'errorMessage' => $error->getMessage()
            ]), 'addOrderPayTypeError');
            return json(msg(-22, '', "Error-22"));
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage(),
                'lastSql' => $db::table('bsa_order')->getLastSql(),
            ]), 'addOrderPayTypeException');
            return json(msg(-11, '', "Exception-11"));
        }
    }

    /**
     * 卡密引导页
     * @return void
     */
    public function info(Request $request)
    {

//        $imgUrl = $request->domain() . "/upload/weixin517.jpg";
        $data = @file_get_contents('php://input');
        $message = json_decode($data, true);
        $orderShowTime = SystemConfigModel::getOrderShowTime();

//       ['order_status'] = 4;  //下单成功-等待访问
        $db = new Db();
        $orderModel = new OrderModel();
        $orderInfo = $orderModel
//            ->where("order_no", $message['order'])
            ->find();

        if (empty($orderInfo)) {
            logs(json_encode([
                'action' => 'info',
                'message' => $message,
                'lockRes' => $orderInfo,
            ]), 'orderInfoFail');
            return json(msg(-2, '', '访问繁忙，重新下单！'));
        }
        //可支付状态
        if ($orderInfo['order_status'] != 4) {
            echo "请重新下单!!!!" . $orderInfo['order_status'];
            exit;
        }


//        $endTime = $orderInfo['add_time'] + $orderShowTime;
//        $now = time();
//
//        $countdownTime = $endTime - $now;
//        if ($countdownTime < 0) {
//            echo "订单超时，请重新下单！";
//            exit;
//        }
        $orderInfo['camiTypeName'] = $db::table("bsa_cami_type")->where('cami_type_sign', $orderInfo['operator'])->find()['cami_type_username'];
        $this->assign('orderData', $orderInfo);
//        $this->assign('countdownTime', $countdownTime);
        return $this->fetch('info1');
    }


    public function getinfo()
    {

    }

    /**
     * 引导页面查询订单状态
     */
    public function getOrderInfo(Request $request)
    {
        $data = @file_get_contents('php://input');
        $message = json_decode($data, true);

        $orderShowTime = SystemConfigModel::getOrderShowTime();
        logs(json_encode([
            'action' => 'getOrderInfo',
            'message' => $message
        ]), 'getOrderInfo');
        if (!isset($message['order_no']) || empty($message['order_no'])) {
            return json(msg(-1, '', '单号有误！'));
        }
        $db = new Db();
        $orderModel = new OrderModel();
//        $where['order_no'] = $message['order_no'];
        $orderInfo = $db::table("bsa_order")
            ->where("order_no", "=", $message['order_no'])->find();
        if (empty($orderInfo)) {
            logs(json_encode([
                'action' => 'lockFail',
                'message' => $message,
                'lockRes' => $orderInfo,
            ]), 'getOrderInfoFail');
            return json(msg(-2, '', '访问繁忙，重新下单！'));
        }
        try {
            if ($orderInfo['order_status'] == 7) {

                logs(json_encode([
                    'action' => 'doMatching',
                    'message' => $message,
                ]), 'getOrderInfodoMatching');
                for ($i = 0; $i < 5; $i++) {
                    sleep(3);
                    $orderInfo = $db::table("bsa_order")->where("order_no", "=", $orderInfo['order_no'])->find();
                    if ($orderInfo['order_status'] == 4) {
                        if (($orderInfo['add_time'] + $orderShowTime) < time()) {
                            return json(msg(-5, '', '订单超时，请重新下单'));
                            break;
                        }
                        $returnData['phone'] = $orderInfo['account'];
                        $returnData['amount'] = $orderInfo['amount'];
                        $limitTime = (($orderInfo['add_time'] + $orderShowTime) - time());
                        $returnData['limitTime'] = (int)($limitTime);
//                $imgUrl = "http://175.178.195.147:9090/upload/tengxun.jpg";

                        $imgUrl = $request->domain() . "/upload/weixin517.jpg";
//                $imgUrl = urlencode($imgUrl);
                        $returnData['imgUrl'] = $imgUrl;
                        return json(msg(0, $returnData, "success"));

                    } else if ($orderInfo['order_status'] == 3) {
                        return json(msg(-3, '', '匹配繁忙，重新下单！'));
                    } else {
                        sleep(1);
                        continue;
                    }
                }
                return json(msg(-9, "", "网络异常，请刷新页面"));
            }
            if ($orderInfo['order_status'] == 0) {

                //展示时间 getOrderShowTime    getAutoCheckOrderTime
                if (time() > ($orderInfo['add_time'] + $orderShowTime)) {
                    return json(msg(-5, '', '订单超时，请重新下单'));
                }
                $db::startTrans();
                $orderInfo = $db::table("bsa_order")
                    ->where("order_no", "=", $orderInfo['order_no'])
                    ->where("order_status", "=", 0)
                    ->lock(true)
                    ->find();

                $orderHxLockTime = SystemConfigModel::getOrderHxLockTime();
                if (!$orderInfo) {
                    logs(json_encode([
                        'action' => 'lockFail',
                        'message' => $message,
                        'lockRes' => $orderInfo,
                    ]), 'getOrderInfoFail');
                    $db::rollback();
                    return json(msg(-6, '', '请刷新或重新下单！-4'));
                }
                //更新为下当中状态
                $doMatch['order_status'] = 7;
                $db::table("bsa_order")->where("order_no", "=", $orderInfo['order_no'])->update($doMatch);
                $db::commit();
                //2、请求核销单
                $orderHXModel = new OrderhexiaoModel();
                $getUseHxOrderRes = $orderHXModel->getUseHxOrderNew($orderInfo);

                if (!isset($getUseHxOrderRes['code']) || $getUseHxOrderRes['code'] != 0) {
                    if (is_array($getUseHxOrderRes['data'])) {
                        $getUseHxOrderRes['data'] = json_encode($getUseHxOrderRes['data']);
                    }
                    logs(json_encode([
                        'action' => 'getUseHxOrderFail',
                        'insertOrderData' => $orderInfo,
                        'getUseHxOrderRes' => $getUseHxOrderRes
                    ]), 'getOrderInfoAmount_log');
                    //修改订单为下单失败状态。
                    $updateOrderStatus['order_status'] = 3;
                    $updateOrderStatus['last_use_time'] = time();
                    $updateOrderStatus['check_result'] = $getUseHxOrderRes['data'];
                    $updateOrderStatus['order_desc'] = "下单失败|-|" . $getUseHxOrderRes['msg'];
                    $updateMatchRes = $orderModel->where('order_no', $orderInfo['order_no'])->update($updateOrderStatus);
                    if (!$updateMatchRes) {
                        logs(json_encode([
                            'action' => 'updateMatchRes',
                            'message' => $message,
                            'updateMatchRes' => $updateMatchRes,
                        ]), 'getOrderInfoFail');
                        return json(msg(-5, '', '下单繁忙'));
                    }
                    return json(msg(-5, '', "下单繁忙，可重新下单！"));
                }
                //有没有之前没有支付的订单匹配的上金额  start
//                $hasPayOrderData = $orderModel
//                    ->where("account", '=', $orderInfo['account'])
//                    ->where("order_pay", '=', $orderInfo['order_pay'])
//                    ->where("order_no", '<>', $orderInfo['order_no'])
//                    ->where("order_status", '<>', 1)
//                    ->where("pay_status", '<>', 1)
//                    ->where("end_check_amount", '<', $getUseHxOrderRes['data']['last_check_amount'] + 20)
//                    ->where("add_time", '>', time() - ($orderHxLockTime + 3600))
//                    ->order('add_time desc')
//                    ->find();
//                if (!empty($hasPayOrderData)) {
//                    logs(json_encode([
//                        "nowTime" => date("Y-m-d H:i:s", time()),
//                        'findLoseOrder' => $hasPayOrderData['order_no'],
//                        'add_time' => $hasPayOrderData['add_time'],
//                        'account' => $hasPayOrderData['account'],
//
//                    ]), 'AfindHasPayOrderAndNotify');
//                    //当前订单更改为下单失败状态
//                    $updateOrderStatus['order_status'] = 3;
//                    $updateOrderStatus['last_use_time'] = time();
//                    $updateOrderStatus['check_result'] = "发现掉单此单失败" . json_encode($hasPayOrderData['order_no']);
//                    $updateOrderStatus['order_desc'] = "发现掉单|此单下单失败！";
//                    $updateMatchRes = $orderModel->where('order_no', $orderInfo['order_no'])->update($updateOrderStatus);
//                    if (!$updateMatchRes) {
//                        logs(json_encode([
//                            'action' => 'updateMatchRes',
//                            'message' => $message,
//                            'updateMatchRes' => $updateMatchRes,
//                        ]), 'hasPayOrderUpdateFail');
//                        return json(msg(-31, '', '下单失败,请重新下单！-31'));
//                    }
//                    //修改订单为下单失败状态。  end
//
//                    //给之前的订单回调 start
//                    $orderHXModel = new OrderhexiaoModel();
//                    $updateOrderWhere['order_no'] = $hasPayOrderData['order_no'];
//                    $updateOrderWhere['account'] = $hasPayOrderData['account'];
//                    //订单表
//                    $localUpdateRes = $orderHXModel->loseOrderLocalUpdateNew($hasPayOrderData, 3, $getUseHxOrderRes['data']['last_check_amount']);
//                    logs(json_encode([
//                        "time" => date("Y-m-d H:i:s", time()),
//                        'findLoseOrder' => $hasPayOrderData['order_no'],
//                        'updateOrderWhere' => $updateOrderWhere,
//                        'account' => $hasPayOrderData['account'],
//                        'localUpdateRes' => $localUpdateRes
//                    ]), 'AfindHasPayOrderAndNotify');
//                    if (!isset($localUpdate['code']) || $localUpdate['code'] != 0) {
//                        return json(msg(32, '', '下单失败，请重新下单2！'));
//                    }
//                    //给之前的订单回调 end
//                    return json(msg(32, '', '下单失败，请重新下单1'));
//                }
                //有没有之前没有支付的订单匹配的上金额  end

                $updateOrderStatus['order_status'] = 4;   //等待支付状态
                $updateOrderStatus['check_times'] = 1;   //下单成功就查询一次
                $updateOrderStatus['start_check_amount'] = $getUseHxOrderRes['data']['last_check_amount'];  //开单余额
                $updateOrderStatus['last_check_amount'] = $getUseHxOrderRes['data']['last_check_amount'];  //第一次查询余额
                $updateOrderStatus['end_check_amount'] = $getUseHxOrderRes['data']['last_check_amount'] + $orderInfo['amount'];  //应到余额
                $updateOrderStatus['order_desc'] = "下单成功,等待支付！";
                if ($orderInfo['operator'] == '移动') {
                    $updateOrderStatus['next_check_time'] = $orderInfo['next_check_time'] + 90;
                }
//                $url = "http://175.178.241.238/pay/#/huafei";
                $url = "http://175.178.241.238/pay/#/kindsRoll";
//                if (isset($orderInfo['payment']) && $orderInfo['payment'] == "alipay") {
//                    $url = "http://175.178.241.238/pay/#/huafeiNewZfb";
//                }
//            订单号order_id   金额 amount   手机号 phone  二维码链接 img_url    有效时间 limit_time 秒
//            $imgUrl = "http://175.178.195.147:9090/upload/huafei.jpg";
//                $imgUrl = "http://175.178.195.147:9090/upload/tengxun.jpg";
                $imgUrl = $request->domain() . "/upload/weixin517.jpg";
//                $imgUrl = urlencode($imgUrl);

                $limitTime = (($orderInfo['add_time'] + $orderShowTime) - time());
                $url = $url . "?order_id=" . $message['order_no'] . "&amount=" . $orderInfo['amount'] . "&phone=" . $orderInfo['account'] . "&img_url=" . $imgUrl . "&limit_time=" . $limitTime;
                $updateOrderStatus['qr_url'] = $url;   //支付订单
//            $localOrderUpdateRes = $orderModel->localUpdateOrder($updateWhere, $updateOrderStatus);
                $localOrderUpdateRes = $db::table("bsa_order")
                    ->where('id', '=', $orderInfo['id'])
                    ->where('order_no', '=', $orderInfo['order_no'])
                    ->update($updateOrderStatus);
                if (!$localOrderUpdateRes) {
                    logs(json_encode([
                        'action' => 'localOrderUpdate',
                        'message' => $message,
                        'localOrderUpdateRes' => $localOrderUpdateRes,
                    ]), 'getOrderInfoFail');
                    $updateOrderStatus['order_status'] = 3;
                    $updateOrderStatus['last_use_time'] = time();
                    $updateOrderStatus['order_desc'] = "下单失败|" . "localOrderUpdateFail";
                    $orderModel->where('order_no', $orderInfo['order_no'])->update($updateOrderStatus);
                    return json(msg(-7, '', '下单繁忙'));
                }
                $returnData['phone'] = $orderInfo['account'];
                $returnData['amount'] = $orderInfo['amount'];
                $returnData['limitTime'] = (int)($limitTime);
                $returnData['imgUrl'] = $imgUrl;
                return json(msg(0, $returnData, 'order_success'));
            } else {
                if (time() > ($orderInfo['add_time'] + $orderShowTime)) {
                    return json(msg(-5, '', '订单超时，请重新下单'));
                }
                if ($orderInfo['order_status'] != 4) {
                    return json(msg(-5, '', '订单状态有误，请重新下单！'));
                }

                $returnData['phone'] = $orderInfo['account'];
                $returnData['amount'] = $orderInfo['amount'];
                $limitTime = (($orderInfo['add_time'] + $orderShowTime) - time());
                $returnData['limitTime'] = (int)($limitTime);
//                $imgUrl = "http://175.178.195.147:9090/upload/tengxun.jpg";

                $imgUrl = $request->domain() . "/upload/weixin517.jpg";
//                $imgUrl = urlencode($imgUrl);
                $returnData['imgUrl'] = $imgUrl;
                return json(msg(0, $returnData, "success"));
            }
        } catch (\Exception $exception) {
            logs(json_encode(['param' => $message,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage()]), 'orderInfoException');
            return apiJsonReturn(-11, "orderInfo exception!" . $exception->getMessage());
        } catch (\Error $error) {
            logs(json_encode(['param' => $message,
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'errorMessage' => $error->getMessage()]), 'orderInfoError');
            return json(msg(-22, '', 'orderInfo error!' . $error->getMessage()));
        }
    }

    //结果回调
    public function checkPhoneAmountNotify0076(Request $request)
    {
        session_write_close();
        $data = @file_get_contents('php://input');
        $message = json_decode($data, true);
        try {
            logs(json_encode([
                'param' => $message,
                'ip' => $request->ip(),
                'startTime' => date("Y-m-d H:i:s", time())
            ]), 'checkPhoneAmountNotify0076');
            $validate = new CheckPhoneAmountNotifyValidate();
            if (!$validate->check($message)) {
                return apiJsonReturn(-1, '', $validate->getError());
            }
            $orderModel = new OrderModel();
            $orderWhere['order_no'] = $message['order_no'];  //四方单号
            $orderWhere['account'] = $message['phone'];   //订单匹配手机号
            $orderInfo = $orderModel->where($orderWhere)->find();


            if (empty($orderInfo)) {
                return json(msg(-2, '', '无此订单！'));
            }
            logs(json_encode([
                "time" => date("Y-m-d H:i:s", time()),
                "orderInfo" => $orderInfo,
                'param' => $message
            ]), 'MatchOrderFailCheckPhoneAmountNotify0076');
            if ($orderInfo['order_status'] == 1) {
                return json(msg(-3, '', '订单已支付！'));
            }
            $db = new Db();
            $checkResult = "第" . ($orderInfo['check_times'] + 1) . "次查询结果" . $message['amount'] . "(" . date("Y-m-d H:i:s") . ")";

            $nextCheckTime = $orderInfo['next_check_time'] + 300;  //设置第三次往后的查询时间
            $autoCheckOrderTime = SystemConfigModel::getAutoCheckOrderTime();
            if (is_int($autoCheckOrderTime)) {
                $nextCheckTime = $orderInfo['next_check_time'] + $autoCheckOrderTime;
            }
            if ($message['check_status'] != 1) {
                $updateCheckTimesRes = $db::table("bsa_order")->where($orderWhere)
                    ->update([
                        "check_status" => 0,  //查询结束
//                        "check_times" => $orderInfo['check_times'] + 1,
                        "next_check_time" => $orderInfo['next_check_time'] + 20,
                        "order_desc" => $checkResult,
                        "check_result" => $checkResult,
                    ]);
                logs(json_encode(['phone' => $orderInfo['account'],
                    "order_no" => $orderInfo['order_no'],
                    "notifyTime" => date("Y-m-d H:i:s", time()),
                    "updateCheckTimesRes" => $updateCheckTimesRes
                ]), '0076updateCheckPhoneAmountFail');
                return json(msg(1, '', '接收成功,更新成功1'));
            }
            //查询成功
            $orderWhere['order_no'] = $orderInfo['order_no'];
            $orderUpdate['check_times'] = $orderInfo['check_times'] + 1;
            $orderUpdate['check_status'] = 0;   //可在查询状态
            $orderUpdate['last_check_amount'] = $message['amount'];
            $orderUpdate['next_check_time'] = $nextCheckTime;
            $orderUpdate['check_result'] = $checkResult;
            $updateCheck = $db::table("bsa_order")->where($orderWhere)
                ->update($orderUpdate);
            if (!$updateCheck) {
                logs(json_encode(["time" => date("Y-m-d H:i:s", time()),
                    'action' => "checkNotifySuccess",
                    'message' => json_encode($message),
                    "updateCheck" => $updateCheck
                ]), '0076updateCheckPhoneAmountFail');
            }
            //1、支付到账
            if ($message['amount'] > ($orderInfo['end_check_amount'] - 20)) {
                //本地更新
                $orderHXModel = new OrderhexiaoModel();
                $updateOrderWhere['order_no'] = $orderInfo['order_no'];
                $updateOrderWhere['account'] = $orderInfo['account'];
                $orderHXData = $orderHXModel->where($orderWhere)->find();
                $localUpdateRes = $orderHXModel->orderLocalUpdate($orderInfo);
                logs(json_encode(["time" => date("Y-m-d H:i:s", time()),
                    'updateOrderWhere' => $updateOrderWhere,
                    'account' => $orderHXData['account'],
                    'localUpdateRes' => $localUpdateRes
                ]), '0076updateCheckPhoneAmountLocalUpdate');
                if (!isset($localUpdate['code']) || $localUpdate['code'] != 0) {
                    return json(msg(1, '', '接收成功,更新失败！'));
                }
                return json(msg(1, '', '接收成功,更新成功！'));
            }
            return json(msg(1, '', '接收成功,匹配失败！'));
        } catch (\Exception $exception) {
            logs(json_encode(['param' => $message,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage()]), 'checkPhoneAmountNotify0076Exception');
            return json(msg(-11, '', '接收异常！'));
        } catch (\Error $error) {
            logs(json_encode(['param' => $message,
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'errorMessage' => $error->getMessage()]), 'checkPhoneAmountNotify0076Error');
            return json(msg(-22, '', "接收错误！"));
        }
    }


}