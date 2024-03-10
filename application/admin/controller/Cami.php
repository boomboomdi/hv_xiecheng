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
use app\admin\validate\CamitypeValidate;
use think\Db;
use tool\Log;

class Cami extends Base
{
    // 卡密管理->卡种列表
    public function index()
    {
        if (request()->isAjax()) {

            $limit = input('param.limit');
            $camiTypeSign = input('param.cami_type_sign'); //卡种标识名称
            $camiTypeUsername = input('param.cami_type_username'); //卡种标识名称

            $where = [];
            if (!empty($writeOffSign)) {
                $where[] = ['cami_type_sign', 'like', $writeOffSign . '%'];
            }

            $writeOffModel = new CamiTypeModel();
            $list = $writeOffModel->getIndex($limit, $where);
            $data = empty($list['data']) ? array() : $list['data'];
//            foreach ($data as $key => $vo) {//推单数量（每个金额）
//
//            }
            $list['data'] = $data;
            if (0 == $list['code']) {
                return json(['code' => 0, 'msg' => 'ok', 'count' => $list['data']->total(), 'data' => $list['data']->all()]);
            }

            return json(['code' => 0, 'msg' => 'ok', 'count' => 0, 'data' => []]);
        }

        return $this->fetch();
    }

    // 添加卡种
    public function addcamitype()
    {
        if (request()->isPost()) {

            $param = input('post.');
            $validate = new CamitypeValidate();
            if (!$validate->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' => $validate->getError()."addcamitype"];
            }


            $writeOffModel = new CamiTypeModel();
            $res = $writeOffModel->add($param);

            Log::write("管理员" . session('admin_user_name') . "添加卡种：" . $param['cami_type_sign'] . ":" . $param['cami_type_username']);

            return json($res);
        }

        return $this->fetch('add');
    }

    // 编辑卡种
    public function editcamitype()
    {
        if (request()->isAjax()) {

            $param = input('post.');

            $validate = new CamitypeValidate();
            if (!$validate->check($param)) {
                return ['code' => -1, 'data' => '', 'msg' => $validate->getError()];
            }

            $writeOffModel = new CamiTypeModel();
            $res = $writeOffModel->edit($param);

            Log::write("管理员" . session('admin_user_name') . "编辑卡种：" . $param['cami_type_id'] . $param['cami_type_sign'] . ":" . $param['cami_type_username'].$res['msg']);

            return json($res);
        }
        $camiTypeId = input('param.cami_type_id');
        $camiTypeModel = new CamiTypeModel();
        $this->assign([
            'camiType' => $camiTypeModel->getWriteoffById($camiTypeId)['data'],
        ]);

        return $this->fetch('edit');
    }

    /**
     * 删除卡种
     * @return \think\response\Json
     */
    public function delCamitype()
    {
        if (request()->isAjax()) {
            return json(['code' => -1, 'msg' => '暂时不可删除！']);
            $camiTypeId = input('param.cami_type_id');

            $camiTypeModel = new CamiTypeModel();
            $res = $camiTypeModel->del($camiTypeId);
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