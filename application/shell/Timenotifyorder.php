<?php

namespace app\shell;

use app\common\model\OrderModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;

use app\common\model\SystemConfigModel;
use app\common\model\NotifylogModel;
use think\Db;

class Timenotifyorder extends Command
{
    protected function configure()
    {
        $this->setName('Timenotifyorder')->setDescription('回调四方！');
    }

    /**
     * 定时回调四方
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
                ->where('order_status', '=', 1)
                ->where('pay_status', '=', 1)
                ->where('notify_status', '<>', 1)
                ->where('do_notify', '=', 0)
                ->where('notify_times', '<', 10)
                ->select();
            $totalNum = count($orderData);

            if ($totalNum > 0) {
                logs(json_encode(['orderData' => $orderData, 'time' => date("Y-m-d H:i:s", time())]), 'timenotifyorder_first_log');
                $closeStatusData = array();
                foreach ($orderData as $key => $val) {
                    $closeStatusData[$key] = $val['id'];
                }
                //暂时全部变更未正在回调状态  ==防止重复拿到做回调请求
                $updateOrderCheckStatusData['do_notify'] = 1;
                $closeOrderData = $db::table("bsa_order")->where('id', 'in', $closeStatusData)
                    ->update($updateOrderCheckStatusData);

                $doChangNotifyStatus = true;  //下次继续回调  -=--》修改订单     notify_status = 2
                //修改订单查询状态为查询中 end
                if (!$closeOrderData) {
                    logs(json_encode(['$orderData' => $orderData, 'time' => date("Y-m-d H:i:s", time())]), 'timenotifyorder_closeOrderFail_log');
                    $output->writeln("timenotifyorder:处理closeOrderFail  " . $totalNum . "--[" . date("Y-m-d H:i:s", time()) . "] ");
                }else{
                    foreach ($orderData as $k => $v) {
                        $orderWhere['order_no'] = $v['order_no'];
                        $notifyOrderRes = $orderModel->orderNotify($v);
                        if (!isset($notifyOrderRes['code']) || $notifyOrderRes['code'] != 1000) {

                            $notifying['do_notify'] = 0;
                            logs(json_encode(['orderData' => $v,
                                "time" => date("Y-m-d H:i:s", time()),
                                "notifyRes" => json_encode($notifyOrderRes),
                            ]), 'ADONTDELETENotifyOrderFail');
                        }
                        $notifying['do_notify'] = 0;
                        $db::table('bsa_order')->where($orderWhere)->update($notifying);
                    }
                }
            }
            $output->writeln("Timenotifyorder:订单总数" . $totalNum);
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(), 'line' => $exception->getLine(), 'errorMessage' => $exception->getMessage()]), 'Timenotifyorder_exception');
            $output->writeln("Timenotifyorder:exception");
        } catch (\Error $error) {
            logs(json_encode(['file' => $error->getFile(), 'line' => $error->getLine(), 'errorMessage' => $error->getMessage()]), 'Timenotifyorder_error');
            $output->writeln("Timenotifyorder:error");
        }

    }
}