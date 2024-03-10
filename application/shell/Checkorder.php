<?php

namespace app\shell;

use app\admin\model\WriteoffModel;
use app\common\model\OrderModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;

use think\Db;
use tool\Log;

class Checkorder extends Command
{
    protected function configure()
    {
        $this->setName('Checkorder')->setDescription('定时查询绑定结果!');
    }

    /**
     * 定时查询绑定结果
     * state  充值成功, 充值失败 代表最终充值状态
     *        待充值, 充值中  是可再查询状态
     * @param Output $output
     * @return int|null|void
     * @todo
     */
    protected function execute(Input $input, Output $output)
    {

        $db = new Db();
        try {
            $orderModel = new OrderModel();
            $orderData = $orderModel
                ->where('order_status', '=', 4)    //待支付状态
                ->where('next_check_time', '<', time())     //当前时间在可以查询期间
//                ->where('order_limit_time', '>', time())    //没有超时得
                ->where('check_times', '<', 4)     //查询次数低于3次
                ->where('check_status', '=', 0)    //查询状态为可查询状态
                ->where('upload_status', '=', 1)    //查询上传成功
                ->limit(30)
                ->select();
            logs(json_encode(['$orderData' => $orderData, "sql" => $db::table("bsa_order")->getLastSql(), 'time' => date("Y-m-d H:i:s", time())]), 'checkorder_first_log');

            $totalNum = count($orderData);
            if ($totalNum > 0) {
                $closeStatusData = array();
                foreach ($orderData as $key => $val) {
                    $closeStatusData[$key] = $val['id'];
                }
                //修改所查询数据并修改其check_status
                $updateOrderCheckStatusData['check_status'] = 1;
                $closeOrderData = $db::table("bsa_order")->where('id', 'in', $closeStatusData)
                    ->update($updateOrderCheckStatusData);

                $doChangCheckStatus = false;  //下次不在继续查询
                //修改订单查询状态为查询中 end
                if (!$closeOrderData) {
                    logs(json_encode(['$orderData' => $orderData, 'time' => date("Y-m-d H:i:s", time())]), 'checkOrder_closeOrderFail_log');
                    $output->writeln("Checkorder:处理closeOrderFail  " . $totalNum . "--[" . date("Y-m-d H:i:s", time()) . "] ");
                } else {
                    foreach ($orderData as $k => $v) {
                        $db::startTrans();
                        $updateCheckWhere['order_no'] = $v['order_no'];
                        $lock = $db::table("bsa_order")->where($updateCheckWhere)->find();
                        if ($lock) {

                            $doChangCheckStatus = true;  //下次继续查询
                            $updateCheckData['next_check_time'] = $v['next_check_time'] + 30;
                            //修改订单下次查询时间
                            $updateOrderCheckTime = $db::table("bsa_order")->where($updateCheckWhere)
                                ->update($updateCheckData);

                            if ($updateOrderCheckTime) {
                                $appKey = "qG4UnbXxzgxdI6VU";
                                $url = "http://114.67.177.36:38088/queryCard?uploadId=" . $v['account'];  //uploadId
                                $headers = array("appKey: {$appKey}");
//                    $options = array('http' => array('method' => 'get', 'header' => implode("\r\n", $headers)));

                                $response = httpGET2($url, $headers);
                                $responseData = json_decode($response, true);

                                Log::OrderLog('订单查询', $v['order_no'], var_export($responseData, true));
                                logs(json_encode(['orderNo' => $v['order_no'], 'uploadId' => $v['account'], 'time' => date("Y-m-d H:i:s", time()), 'response' => $responseData]), 'checkorder_xc_log');

                                /**
                                 * 查询失败
                                 */
                                //查询失败
                                $updateCheckData['check_result'] = var_export($responseData, true);
                                if ($responseData['code'] != 200 || !isset($responseData['data']) || empty($responseData['data'])) {
                                    $doChangCheckStatus = true;  //下次继续查询
                                    $updateCheckData['check_status'] = 0;  //查询失败
                                    $updateCheckData['next_check_time'] = time() + 20;  //查询失败
                                }
                                /**
                                 * 查询成功 绑定状态   记录一下
                                 *         1、待充值, 充值中  是可再查询状态
                                 *         2、充值失败   是不可再查询状态
                                 *         3、充值成功   是不可再查询状态
                                 */

                                $cardDta = $responseData['data'][0];
                                if (isset($cardDta['cardName']) && $cardDta['cardName'] != $val['cami_account'] || !isset($cardDta['state'])) {
                                    logs(json_encode(
                                        [
                                            'orderNo' => $v['order_no'],
                                            'uploadId' => $v['account'],
                                            'response' => $cardDta,
                                            'time' => date("Y-m-d H:i:s", time()),
                                        ]
                                    ), 'checkorder_cami_different_log');
                                }

                                //待充值, 充值中  是可再查询状态
                                if (isset($cardDta['state']) && ($cardDta['state'] == '待充值' || $cardDta['state'] == '充值中')) {

                                    $doChangCheckStatus = true;  //下次继续查询
                                    $updateCheckData['check_times'] = $val['check_times'] + 1;  //查询次数加一
                                    $updateCheckData['check_status'] = 0;  //查询状态
                                }
                                //充值失败
                                //查询状态变更为不可查询状态   check_status =2

                                if ($cardDta['state'] == '充值失败') {
                                    $updateCheckData['check_times'] = $val['check_times'] + 1;  //查询次数加一
                                    $updateCheckData['order_status'] = 2;  //支付状态支付失败
                                    $updateCheckData['order_desc'] = "卡密充值失败";  //支付状态支付失败

                                    //支付失败 修改核销商金额
                                    $bsaWriteOffData = $db::table("bsa_write_off")
                                        ->where('write_off_sign', $v['write_off_sign'])
                                        ->lock(true)
                                        ->find();

                                    if (!$bsaWriteOffData) {
                                        $doChangCheckStatus = true;  //下次继续查询
                                        $db::rollback();
                                        logs(json_encode([
                                            'order_no' => $val['order_no'],
                                            'errorMessage' => "pay_fail_lock_write_off_fail",
                                            'last_sql' => $db::table("bsa_write_off")->getLastSql()
                                        ]), 'checkOrderLockWriteFail1');
                                    }

                                    $rateWhere['write_off_sign'] = $v['write_off_sign'];
                                    $rateWhere['cami_type_sign'] = $v['operator'];
                                    $rate = $db::table("bsa_cami_write")->where($rateWhere)->find()['rate'];

                                    $freezeAmount = ($v['amount'] * (1 - $rate));

                                    //支付失败 核销商可用金额增加
                                    //支付成功 核销商冻结金额减少
                                    //增加核销商可用金额
                                    //减少核销商冻结金额
                                    $writeOffModel = new WriteoffModel();
                                    $updateWriteOff = $db::table("bsa_write_off")
                                        ->execute("UPDATE bsa_write_off  SET 
                                            use_amount = use_amount + " . (number_format($freezeAmount, 3)) . " ,
                                            freeze_amount = freeze_amount - " . (number_format($freezeAmount, 3)) . " 
                                            WHERE  write_off_id = " . $bsaWriteOffData['write_off_id']);

                                    if ($updateWriteOff != 1) {

                                        $doChangCheckStatus = true;  //下次继续查询
                                        logs(json_encode([
                                            'updateCamiChannelWhere' => $v['write_off_sign'],
                                            'updateSql' => $db::table("bsa_write_off")->getLastSql(),
                                            'updateMatchSuccessRes' => $updateWriteOff,
                                        ]), 'checkOrderUpdateWriteOffStatus');
                                        $db::rollback();
                                    }
                                }

                                //充值成功
                                if (isset($cardDta['state']) && $cardDta['state'] == '充值成功') {

                                    //修改订单状态  == = = = 成功
                                    $updateCheckData['check_times'] = $val['check_times'] + 1;  //查询次数加一
                                    $updateCheckData['order_desc'] = "支付成功-等候回调";  //支付成功状态
                                    $updateCheckData['order_status'] = 1;  //支付成功状态
                                    $updateCheckData['pay_status'] = 1;  //支付成功状态
                                    $updateCheckData['actual_amount'] = $cardDta['amount'];  //支付绑定金额
                                    if ($cardDta['amount'] != $v['amount']) {
                                        $updateCheckData['order_desc'] = "支付成功-差额订单";  //支付成功状态
                                        $updateCheckData['do_notify'] = 2;  //拒绝回调
                                        $updateCheckData['notify_status'] = 2;  //拒绝回调
                                    }
                                    //更新订单
                                    //修改核销商金额
                                    $bsaWriteOffData = $db::table("bsa_write_off")
                                        ->where('write_off_sign', '=', $v['write_off_sign'])
                                        ->lock(true)
                                        ->find();
                                    if (!$bsaWriteOffData) {
                                        $doChangCheckStatus = true;  //下次继续查询
                                        $db::rollback();
                                        logs(json_encode([
                                            'order_no' => $val['order_no'],
                                            'errorMessage' => "pay_success_lock_write_off_fail",
                                            'last_sql' => $db::table("bsa_write_off")->getLastSql()
                                        ]), 'checkOrderLockWriteFail2');
                                    }

                                    $rateWhere['write_off_sign'] = $v['write_off_sign'];
                                    $rateWhere['cami_type_sign'] = $v['operator'];
                                    $rete = $db::table("bsa_cami_write")->where($rateWhere)->find()['rate'];
                                    $freezeAmount = ($v['amount'] * (1 - $rete));

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

                                        $doChangCheckStatus = true;  //下次继续查询
                                        logs(json_encode([
                                            'updateCamiChannelWhere' => $v['write_off_sign'],
                                            'updateSql' => $db::table("bsa_write_off")->getLastSql(),
                                            'updateMatchSuccessRes' => $updateWriteOff,
                                        ]), 'checkOrderUpdateWriteOffStatus');
                                        $db::rollback();
                                    }
                                }
                                //修改订单状态
                                $updateOrderStatus = $db::table("bsa_order")->where($updateCheckWhere)
                                    ->update($updateCheckData);
                                if (!$updateOrderStatus) {
                                    $doChangCheckStatus = true;  //下次继续查询
                                    logs(json_encode([+
                                    'action' => 'updateMatch',
                                        'updateOrderWhere' => $updateCheckWhere,
                                        'updateCheckData' => $updateCheckData,
                                        'updateSql' => $db::table("bsa_order")->getLastSql(),
                                        'updateOrderSuccessRes' => $updateOrderStatus,
                                    ]), 'checkOrderUpdateOrderStatus');
                                    $db::rollback();
                                }
                            } else {
                                $doChangCheckStatus = true;  //下次继续查询
                                $db::rollback();
                                logs(json_encode(['$orderData' => $orderData, 'time' => date("Y-m-d H:i:s", time())]), 'checkOrder_updateOrderCheckTimeFail_log');
                                $output->writeln("Checkorder:处理updateOrderCheckTimeFail ,orderNo:" . $v['order_no'] . "--[" . date("Y-m-d H:i:s", time()) . "] ");
                            }
                        } else {
                            $db::rollback();

                            logs(json_encode(['$orderData' => $orderData, 'time' => date("Y-m-d H:i:s", time())]), 'checkOrder_closeOrderLockOrderFail_log');
                            $output->writeln("Checkorder:处理lockOrderFail ,orderNo:" . $v['order_no'] . "--[" . date("Y-m-d H:i:s", time()) . "] ");
                        }

                        $db::commit();
                        //存在需要重新查询情况
                        if ($doChangCheckStatus) {
                            $openOrderCheckStatusData['last_check_time'] = time();
                            $openOrderCheckStatusData['next_check_time'] = time() + 10;
                            $openOrderCheckStatusData['check_status'] = 0;
                            $openOrderData = $db::table("bsa_order")->where('id', $v['id'])
                                ->update($openOrderCheckStatusData);
                            if (!$openOrderData) {
                                logs(json_encode([
                                    'orderData' => $orderData,
                                    'lastSql' => $db::table("bsa_order")->getLastSql(),
                                    'time' => date("Y-m-d H:i:s",
                                        time())]), 'checkOrder_openOrderCheckStatusFail_log');
                                $output->writeln("Checkorder:处理openOrderCheckStatusFail ,orderNo:" . $v['order_no'] . "--[" . date("Y-m-d H:i:s", time()) . "] ");
                            }
                        }
                    }
                }

            }

            $output->writeln("Checkorder:处理总数" . $totalNum . "--[" . date("Y-m-d H:i:s", time()) . "] ");
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(), 'line' => $exception->getLine(), 'errorMessage' => $exception->getMessage() . $db::table("bsa_write_off")->getLastSql()]), 'Checkorder_exception');
            $output->writeln("Checkorder:exception" . $exception->getMessage() . $exception->getLine() . $db::table("bsa_write_off")->getLastSql());
        } catch (\Error $error) {
            logs(json_encode(['file' => $error->getFile(), 'line' => $error->getLine(), 'errorMessage' => $error->getMessage()]), 'Checkorder_error');
            $output->writeln("Checkorder:error");
        }

    }
}