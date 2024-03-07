<?php

namespace app\shell;

use app\common\model\OrderhexiaoModel;
use app\common\model\OrderModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;

use app\common\Redis;
use app\common\model\SystemConfigModel;
use app\common\model\NotifylogModel;
use think\Db;
use think\db\Where;

class Notifynopayorder extends Command
{
    protected function configure()
    {
        $this->setName('Notifynopayorder')->setDescription('处理未支付订单,回调核销！');
    }

    /**
     * 定时处理超时未上传或上传失败订单    解冻核销商金额
     * //下单成功 order_status  = 4
     * limit_time < now
     * pay_status != 1
     * write_off_sign != null
     * do_notify !=1
     * s !=1
     * @param Output $output
     * @return int|null|void
     * @todo
     */
    protected function execute(Input $input, Output $output)
    {
        try {
            $orderModel = new OrderModel();
            $orderData = $orderModel
                ->where('limit_time', '<', time())    //超时时间
                ->where('notify_status', '=', 0)   //回调状态为默认状态
                ->where('do_notify', '=', 0)       //没有正在处理回调
                ->where('upload_status', '=', 2)   //上传失败
                ->where('order_status', '=', 4)   //下单成功
                ->select();
            logs(json_encode(['Notifynopayorder' => $orderData, 'last_Sql' => $orderModel->getLastSql(), 'time' => date("Y-m-d H:i:s", time())]), 'Notifynopayorder_first_log');

            $db = new Db();
            $totalNum = count($orderData);
            if ($totalNum > 0) {

                $closeStatusData = array();
                foreach ($orderData as $key => $val) {
                    $closeStatusData[$key] = $val['id'];
                }
                //修改所查询数据并修改其  do_notify
                $updateOrderCheckStatusData['do_notify'] = 1;
                $closeOrderData = $db::table("bsa_order")->where('id', 'in', $closeStatusData)
                    ->update($updateOrderCheckStatusData);
                if (!$closeOrderData) {
                    logs(json_encode(['$orderData' => $orderData, 'time' => date("Y-m-d H:i:s", time())]), 'checkOrder_closeOrderFail_log');
                    $output->writeln("Notifynopayorder:处理closeOrderFail  " . $totalNum . "--[" . date("Y-m-d H:i:s", time()) . "] ");
                } else {
                    foreach ($orderData as $k => $v) {
                        $changeOrderDoNotify = false;
                        $db::startTrans();
                        $rateWhere['write_off_sign'] = $v['write_off_sign'];
                        $rateWhere['cami_type_sign'] = $v['operator'];
                        $rete = $db::table("bsa_cami_write")->where($rateWhere)->find()['rate'];
                        $freezeAmount = ($v['amount'] * (1 - $rete));
                        $writeOffData = $db::table("bsa_write_off")->where('write_off_sign', $v['write_off_sign'])->lock(true)->find();
                        if (!$writeOffData) {
                            $changeOrderDoNotify = true;
                            $db::rollback();
                            logs(json_encode(['$orderData' => $orderData, 'time' => date("Y-m-d H:i:s", time())]), 'nitifyNoPayOrder_LockOrderFail_log');
                            $output->writeln("Notifynopayorder:处理LockOrderFail ,orderNo:" . $v['order_no'] . "--[" . date("Y-m-d H:i:s", time()) . "] ");
                        } else {
                            //增加核销商可用金额
                            //减少核销商冻结金额
                            $updateWriteOff = $db::table("bsa_write_off")
                                ->execute("UPDATE bsa_write_off  
                                         SET use_amount = use_amount + " . (number_format($freezeAmount, 3)) . " ,
                                             freeze_amount = freeze_amount - " . (number_format($freezeAmount, 3)) . " 
                                         WHERE  write_off_id = " . $writeOffData['write_off_id']);

                            if (!$updateWriteOff) {
                                $changeOrderDoNotify = true;
                                $db::rollback();
                                logs(json_encode(['orderData' => $orderData, 'time' => date("Y-m-d H:i:s", time())]), 'checkOrder_closeOrderLockOrderFail_log');
                                $output->writeln("Notifynopayorder:处理lockOrderFail ,orderNo:" . $v['order_no'] . "--[" . date("Y-m-d H:i:s", time()) . "] ");
                            }
                        }
                        //修改订单状态  可再次处理
                        $changeOrderCheckStatusData['order_desc'] = "订单超时,回滚核销金额：" . date("Y-m-d H:i:s", time());
                        $changeOrderCheckStatusData['order_status'] = 2;
                        if ($changeOrderDoNotify) {
                            $changeOrderCheckStatusData['order_desc'] = "订单超时，回滚核销金额失败等待下次回滚：" . date("Y-m-d H:i:s", time());
                            $changeOrderCheckStatusData['do_notify'] = 0;
                        }
                        $db::table("bsa_order")->where('order_no', $v['order_no'])->update($changeOrderCheckStatusData);
                        $db::commit();
                        //修改订单状态为种植状态  --支付失败
                    }
                }
            }
            $output->writeln("Notifynopayorder:订单总数" . $totalNum . "，时间：" . date("Y-m-d H:i:s", time()));
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(), 'line' => $exception->getLine(), 'errorMessage' => $exception->getMessage()]), 'NotifynopayhxException');
            $output->writeln("Notifynopayorder:exception" . $exception->getMessage());
        } catch (\Error $error) {
            logs(json_encode(['file' => $error->getFile(), 'line' => $error->getLine(), 'errorMessage' => $error->getMessage()]), 'NotifynopayhxError');
            $output->writeln("Notifynopayorder:error" . $error->getMessage());
        }

    }
}