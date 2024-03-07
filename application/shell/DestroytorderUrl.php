<?php

namespace app\shellOld;

use app\common\model\OrderModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;

use app\common\model\OrderdouyinModel;
use app\common\model\SystemConfigModel;
use think\Db;

class DestroytorderUrl extends Command
{
    protected function configure()
    {
        $this->setName('Destorytorderurl')->setDescription('销毁已拉单但失效推单！');
    }

    /**
     * 销毁已拉单未支付链接
     * @param Input $input
     * @param Output $output
     * @return int|null|void
     */
    protected function execute(Input $input, Output $output)
    {
        $limitTime = 180;
        $now = time();
        $totalNum = 0;
        $successNum = 0;
        $errorNum = 0;
        $lockLimit = $now - $limitTime;  //更新锁定时间
        $addLockTime = time() - 600;
        $orderdouyinModel = new OrderdouyinModel();
//        $LimitStartTime = time() - $limitTime;
        $db = new Db();
        try {
            $output->writeln("DestroyTOrderUrl:超时预产单处理成功" . "总失效单" . $totalNum . "成功处理:" . $successNum . "失败:" . $errorNum);

        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(), 'line' => $exception->getLine(), 'errorMessage' => $exception->getMessage()]), 'destroyTOrderUrl_log');

            $output->writeln("DestroyTOrderUrl:超时预产单处理exception：" . $exception->getMessage());
        } catch (\Error $error) {
            logs(json_encode(['file' => $error->getFile(), 'line' => $error->getLine(), 'errorMessage' => $error->getMessage()]), 'destroyTOrderUrl_log');

            $output->writeln("DestroyTOrderUrl:超时预产单处理error" . $error->getMessage());
        }

    }
}