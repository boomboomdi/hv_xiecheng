<?php
/**
 * Created by PhpStorm.
 * User: NickBai
 * Time: 4:48 PM
 */
namespace app\admin\model;

use think\Db;
use think\Model;
use tool\Log;

class CamiTypeModel extends Model
{
    protected $table = 'bsa_cami_type';

    /**
     * 获取卡密类型
     * @param $limit
     * @param $where
     * @return array
     */
    public function getIndex($limit, $where)
    {
        $prefix = config('database.prefix');
        try {
            $res = $this->field($prefix . 'cami_type.*' )->where($where)
                ->order('cami_type_id', 'desc')->paginate($limit);
        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }
        return modelReMsg(0, $res, 'ok');
    }



    /**
     * 增加卡种
     * @param $merchant
     * @return array
     */
    public function add($writeoff)
    {
        try {

            $has = $this
//                ->where('cami_type_username', $writeoff['cami_type_username'])
                ->where('cami_type_sign', $writeoff['cami_type_sign'])
                ->findOrEmpty()->toArray();
            if(!empty($has)) {
                return modelReMsg(-2, '', '卡种已存在');
            }

            $writeoff['add_time'] = date("Y-m-d H:i:s",time());
            $this->insert($writeoff);
        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, '', '添加卡种成功');
    }

    /**
     * 查询卡种标识或者名称是否存在
     * @param $data
     * @return array
     */
    public function getWriteoffData($data)
    {
        try {

            if(!isset($data['cami_type_sign'])||empty($data['cami_type_sign'])){
                return modelReMsg(-2, '', "卡密标识不能为空");
            }
            if(!isset($data['cami_type_username'])||empty($data['cami_type_username'])){
                return modelReMsg(-3, '', "卡种名称不能为空");
            }

            $info = $this->where('cami_type_sign')->whereOr()->findOrEmpty()->toArray();
        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, $info, 'ok');
    }

    /**
     * 编辑卡种
     * @param $camitype
     * @return array
     */
    public function edit($camitype)
    {
        try {

            $hasCamiTypeUsername = $this->where('cami_type_username', $camitype['cami_type_username'])->where('cami_type_id', '<>', $camitype['cami_type_id'])
                ->findOrEmpty()->toArray();

            if(!empty($hasCamiTypeUsername)) {
                return modelReMsg(-2, '', '商户名已经存在');
            }
            $hasCamiTypeSign = $this->where('cami_type_sign', $camitype['cami_type_sign'])->where('cami_type_id', '<>', $camitype['cami_type_id'])
                ->findOrEmpty()->toArray();
            if(!empty($hasCamiTypeUsername)) {
                return modelReMsg(-2, '', '商户名已经存在');
            }
            $camitype['last_update_time'] = date("Y-m-d H:i:s",time());
            $this->save($camitype, ['cami_type_id' => $camitype['cami_type_id']]);
        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, '', '编辑卡种成功');
    }

    /**
     * 删除
     * @param $camiTypeId
     * @return array
     */
    public function del($camiTypeId)
    {
        try {
            if (1 == $camiTypeId) {
                return modelReMsg(-2, '', '测试卡种不可删除');
            }

            $this->where('cami_type_id', $camiTypeId)->delete();
        } catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        Log::write("管理员" . session('admin_user_name') . "删除卡种：" . $camiTypeId);
        return modelReMsg(0, '', '删除成功');
    }

    /**
     * 获取核销商信息
     * @param $writeoffId
     * @return array
     */
    public function getWriteOffBySign($writeoffSign)
    {
        try {

            $info = $this->where('write_off_sign', $writeoffSign)->findOrEmpty()->toArray();
        }catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, $info, 'ok');
    }
}