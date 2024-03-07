<?php

namespace app\shell;

use app\common\model\OrderModel;
use think\console\Command;
use think\console\Input;
use think\console\Output;

use think\Db;
use tool\Log;

class Checkceshir extends Command
{
    protected function configure()
    {
        $this->setName('Checkceshir')->setDescription('定时查询绑定结果!');
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

        $aaa = array();
        for ($i = 0; $i < 3; ++$i) {
            $aaa[] = $i;
        }
        foreach ($aaa as $k => $v){
            if($v<1){
                $output->writeln("Checkorder:Checkceshir :" . $v . "--[" . date("Y-m-d H:i:s", time()) . "] ");
                break;
            }
//else{
//
//            }

            $output->writeln($v);
//            $output->writeln("5 ");
        }

    }
}