<?php

namespace app\shell;

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
                ->where('order_limit_time', '>', time())    //没有超时得
                ->where('check_times', '<', 3)     //查询次数低于3次
                ->where('check_status', '=', 0)    //查询状态为可查询状态
                ->where('upload_status', '=', 1)    //查询上传成功
                ->limit(30)
                ->select();
            logs(json_encode(['$orderData' => $orderData, 'time' => date("Y-m-d H:i:s", time())]), 'checkorder_first_log');

            $totalNum = count($orderData);
            if ($totalNum > 0) {
                $closeStatusData = array();
                foreach ($orderData as $key => $val) {
                    $closeStatusData[$key] = $val['id'];
                }
                //修改所查询数据并修改其check_status
                $updateOrderCheckStatusData['check_status'] = 1;
                $db::table("bsa_order")->where('id', 'in', $closeStatusData)
                    ->update($updateOrderCheckStatusData);
                foreach ($orderData as $k => $v) {
                    $updateCheckWhere['order_no'] = $v['order_no'];
                    $lock = $db::table("bsa_order")->where($updateCheckWhere)->find();
                    $updateCheckData['next_check_time'] = time() + 20;
                    $db::table("bsa_order")->where($updateCheckWhere)
                        ->update($updateCheckData);
                    //修改订单查询状态为查询中 end
                    $appKey = "qG4UnbXxzgxdI6VU";

                    $url = "http://114.67.177.36:38088/queryCard?uploadId=" . $v['account'];  //uploadId
                    $headers = array("appKey: {$appKey}");
                    $options = array('http' => array('method' => 'get', 'header' => implode("\r\n", $headers)));


                    $response = httpGET2($url, $headers);

                    logs(json_encode(['orderNo' => $v['order_no'], 'uploadId' => $v['account'], 'time' => date("Y-m-d H:i:s", time()), 'response' => $response]), 'checkorder_xc1_log');

                    $responseData = json_decode($response, true);

                    Log::OrderLog('订单查询', $v['order_no'], var_export($responseData, true));
                    logs(json_encode(['orderNo' => $v['order_no'], 'uploadId' => $v['account'], 'time' => date("Y-m-d H:i:s", time()), 'response' => $responseData]), 'checkorder_xc_log');
//                            {
//                                "code": 200,
//                                "data": [
//                                    {
//                                        "cardName": "534856728592",
//                                        "state": "充值失败",
//                                        "result": "已领用",
//                                        "amount": ""
//                                    }
//                                ],
//                                "msg": "请求成功",
//                                "traceId": "b77491c876c94418b8ecc583f0b569bf"
//                            }
                    /**
                     * 查询失败
                     */
                    //查询失败
                    $updateCheckData['check_result'] = var_export($responseData, true);
                    if ($responseData['code'] != 200 || empty($responseData['data']) || !isset($responseData['data']['state'])) {
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
                    if (isset($cardDta['cardName']) && $cardDta['cardName'] != $val['cami_account']) {
                        logs(json_encode(['orderNo' => $v['order_no'],
                            'uploadId' => $v['account'],
                            'data' => $responseData['data'],
                            'time' => date("Y-m-d H:i:s", time()),
                            'response' => var_export($responseData, true)
                        ]), 'checkorder_cami_different_log');
                    }
                    //待充值, 充值中  是可再查询状态
                    if (isset($cardDta['state']) && ($cardDta['state'] == '待充值' || $cardDta['state'] == '充值中')) {
                        $updateCheckData['check_times'] = $val['check_times'] + 1;  //查询次数加一
                    }
                    //充值失败
                    //查询状态变更为不可查询状态   check_status =2
                    if (isset($cardDta['state']) && $cardDta['state'] == '充值失败') {
                        $updateCheckData['check_times'] = $val['check_times'] + 1;  //查询次数加一
                    }

                    //充值成功
                    $db::startTrans();
                    if (isset($cardDta['state']) && $cardDta['state'] == '充值成功') {
                        //
                        //修改订单状态  == = = = 成功
                        $updateCheckData['check_times'] = $val['check_times'] + 1;  //查询次数加一
                        $updateCheckData['order_desc'] = "支付成功-等候回调";  //支付成功状态
                        $updateCheckData['order_status'] = 1;  //支付成功状态
                        $updateCheckData['pay_status'] = 1;  //支付成功状态
                        //更新订单
                        //修改核销商金额
                        $bsaWriteOffData = $db::table("bsa_write_off")
                            ->where('write_off_sign', '=', $v['write_off_sign'])
                            ->lock(true)
                            ->find();
                        if (!$bsaWriteOffData) {
                            $db::rollback();
                            logs(json_encode([
                                'order_no' => $val['order_no'],
                                'errorMessage' => "pay_success_lock_write_off_fail",
                                'last_sql' => $db::table("bsa_write_off")->getLastSql()
                            ]), 'checkOrderLockWriteFail');
                        }

                        $freezeAmount = ($v['amount'] * (1 - $v['rate']));
                        $updateWriteOff = $db::table("bsa_write_off")
                            ->execute("UPDATE bsa_write_off  SET  freeze_amount = freeze_amount - " . (number_format($freezeAmount, 3)) . "  WHERE  write_off_sign = " . $v['write_off_sign']);

                        if ($updateWriteOff != 1) {
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
                        logs(json_encode([+

                        'action' => 'updateMatch',
                            'updateOrderWhere' => $updateCheckWhere,
                            'updateCheckData' => $updateCheckData,
                            'updateSql' => $db::table("bsa_order")->getLastSql(),
                            'updateOrderSuccessRes' => $updateOrderStatus,
                        ]), 'checkOrderUpdateOrderStatus');
                        $db::rollback();
                    }
                    $db::commit();
                }

            }
            $output->writeln("Checkorder:订单总数" . $totalNum);
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(), 'line' => $exception->getLine(), 'errorMessage' => $exception->getMessage()]), 'Checkorder_exception');
            $output->writeln("Checkorder:exception" . $exception->getMessage());
        } catch (\Error $error) {
            logs(json_encode(['file' => $error->getFile(), 'line' => $error->getLine(), 'errorMessage' => $error->getMessage()]), 'Checkorder_error');
            $output->writeln("Checkorder:error");
        }

    }
}