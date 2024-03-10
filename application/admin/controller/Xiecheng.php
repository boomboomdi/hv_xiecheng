<?php
/**
 * Created by PhpStorm.
 * User: NickBai
 * Email: 876337011@qq.com
 * Date: 2019/2/28
 * Time: 8:23 PM
 */

namespace app\admin\controller;

use app\admin\model\CamiTypeModel;
use app\admin\model\WriteoffModel;
use app\admin\model\CamiChannelModel;
use app\admin\validate\CamitypechannelValidate;
use app\common\model\OrderModel;
use app\common\model\SystemConfigModel;
use think\App;
use think\Db;
use tool\Log;

class Xiecheng extends Base
{

    protected $camiTypeId = 6; //卡种ID;
    protected $cardTypeSign = "XIECHENG"; //卡种;
    protected $cardTypeSignName = "携程电子卡"; //卡种名称;

    // 携程电子卡通道-（服务商列表）
    public function index()
    {
        //沃尔玛-核销商列表
        if (request()->isAjax()) {

            $limit = input('param.limit');

            $searchParam = input('param.');

            $where = [];
            if (isset($searchParam['write_off_sign']) && !empty($searchParam['write_off_sign'])) {
                $where['write_off_sign'] = $searchParam['write_off_sign'];
            }

            $camiChannelModel = new CamiChannelModel();

            $list = $camiChannelModel->getIndex2($limit, $where, $this->cardTypeSign);
            $data = empty($list['data']) ? array() : $list['data'];

            $list['data'] = $data;

            if (0 == $list['code']) {
                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            }

            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }
        return $this->fetch();
    }

    // 绑定对应核销商
    //卡种标识
    //卡种id
    //权重
    public function addCamitypechannel()
    {

        $db = new Db();
        if (request()->isPost()) {

            $param = input('post.');
            $validate = new CamitypechannelValidate();
            if (!$validate->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' => $validate->getError()];
            }

//            var_dump($param);exit;
            $writeOffIdData = $db::table('bsa_write_off')->where('write_off_id', '=', $param['write_off_id'])->find();
            if (empty($writeOffIdData)) {
                return ['code' => -2, 'data' => '', 'msg' => '无此核销服务商信息！'];
            }
//            var_dump($writeOffIdData);exit;
//            $param['write_off_username'] = $writeOffIdData['write_off_username'];
            $param['write_off_sign'] = $writeOffIdData['write_off_sign'];


            $camiChannelModel = new CamiChannelModel();
            $res = $camiChannelModel->add($param);


            $cardTypeSign = $this->cardTypeSign; //卡种
            Log::write("管理员" . session('admin_user_name') . "添加" . $cardTypeSign . $param['write_off_sign'] . "=>" . $res['msg']);

            return json($res);
        }

        $writeOffData = $db::table('bsa_write_off')->select();

        //cami_type_id   cami_type_username  cami_type_sign
        $camiChannelData['cami_type_id'] = 1; //卡种
        $camiChannelData['cami_type_username'] = $this->cardTypeSignName; //卡种名称
        $camiChannelData['cami_type_sign'] = $this->cardTypeSign; //卡种
        $this->assign([
            'writeOffData' => $writeOffData,
            'camiChannelData' => $camiChannelData,
        ]);
        return $this->fetch('add');
    }

    // 编辑卡密绑定核销商
    public function editCamitypechannel()
    {

        $camiChannelModel = new CamiChannelModel();
        if (request()->isAjax()) {

            $param = input('post.');

//            var_dump($param);exit;
            $validate = new CamitypechannelValidate();
            if (!$validate->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' => $validate->getError()];
            }

            $res = $camiChannelModel->edit($param);

            $cardTypeSign = $this->cardTypeSign; //卡种
            $logData = var_export($param, true);
            Log::write("管理员" . session('admin_user_name') . "编辑卡密绑定关系" . $cardTypeSign . $logData . "=>" . $res['msg']);

            return json($res);
        }
        $writeOffId = input('param.write_off_id');
        $id = input('param.id');
//        var_dump($id);exit;
        $writeOffModel = new WriteoffModel();
        //'writeOffData', $writeOffData
        $db = new Db();
        $writeOffData = $db::table('bsa_write_off')->select();
        $this->assign([
            'writeOffData' => $writeOffData,
            'camiChannelData' => $camiChannelModel->getById($id)['data'],
        ]);

        return $this->fetch('edit');
    }

    /**
     * 删除核销
     * @return \think\response\Json
     */
    public function delWriteoff()
    {
        if (request()->isAjax()) {

            $writeOffId = input('param.write_off_id');

            $writeOffModel = new WriteoffModel();
            $res = $writeOffModel->delWriteoff($writeOffId);

            Log::write("删除核销：" . $writeOffId);

            return json($res);
        }
    }


    public function stopOrderHx()
    {
        if (request()->isPost()) {
            $param = input('post.');
            $where = [];
            if (empty(input('post.write_off_sign'))) {
                return json(['code' => -1, 'msg' => '核销商必填']);
            }
            if (empty(input('post.operator'))) {
                return json(['code' => -1, 'msg' => '核销商必填']);
            }
            if (empty(input('post.endTime'))) {
                return json(['code' => -2, 'msg' => '截至时间必选']);
            }
            $where[] = ['write_off_sign', "=", input('post.write_off_sign')];
            $where[] = ['operator', "=", input('post.operator')];
            $where[] = ['add_time', "<", strtotime(input('post.endTime'))];
            $where[] = ['order_limit_time', "=", 0];
            $where[] = ['order_status', "<>", 1];
            $where[] = ['notify_status', "<>", 1];
            $where[] = ['pay_status', "<>", 1];
//            $where[] = ['order_me', "=", null];

//            var_dump($where);
//            exit;
            Db::startTrans();
            $stopOrderAccount = Db::table("bsa_order_hexiao")->where($where)->lock(true)->count();
//            var_dump($stopOrderAccount);
//            var_dump(Db::table("bsa_order_hexiao")->getLastSql());
//            Db::rollback();
//            exit;
            if ($stopOrderAccount == 0) {
                Db::rollback();
                return json(['code' => 0, 'msg' => '支付核销单为零']);
            }

            $update['limit_time'] = time();
            $update['status'] = 2;
            $update['order_status'] = 2;
            $update['order_desc'] = "止付" . date("Y-m-d H:i:s");
            logs(json_encode(['param' => $param,
                "time" => date("Y-m-d H:i:s", time()),
                "where" => $where,
            ]), 'stopOrderHx');

            $updateData = Db::table("bsa_order_hexiao")->where($where)->update($update);
//            var_dump();
            $addLog['action_result'] = "处理成功！";
            if (!$updateData) {
                $addLog['action_result'] = "处理失败！";
                Db::rollback();
            }
            Db::commit();
            $addLog['user'] = session("admin_user_name");
            $addLog['order_type'] = "核销单号";
            $addLog['time'] = time();
            $addLog['action'] = "止付核销单";
            $addLog['content'] = serialize($where) . "共计" . $stopOrderAccount . "单";
            $addLog['desc'] = "止付核销商:" . input('post.write_off_sign') .
                ",(" . input('post.operator') . "),截至:" . input('post.endTime') . "止,共计" . $stopOrderAccount . "单)";
            Db::table("bsa_order_exception")->create($addLog);

            return json(['code' => 0, 'msg' => $addLog['action_result']]);
        }
        $writeOffId = input('param.write_off_id');
        $writeOffModel = new WriteoffModel();
        $this->assign([
            'writeOff' => $writeOffModel->getWriteoffById($writeOffId)['data'],
        ]);
        return $this->fetch('stoporderhx');
    }
}