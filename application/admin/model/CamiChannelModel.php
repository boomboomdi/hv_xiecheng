<?php
/**
 * Created by PhpStorm.
 * User: NickBai
 * Email: 876337011@qq.com
 * Date: 2019/3/17
 * Time: 4:48 PM
 */

namespace app\admin\model;

use think\Db;
use think\Model;

class CamiChannelModel extends Model
{
    protected $table = 'bsa_cami_write';


    /**
     * 获取卡密服务商
     * @param $limit2
     * @param $where
     * @param $cardTypeSign
     * @return array
     */
    public function getIndex2($limit, $where, $cardTypeSign)
    {
        $prefix = config('database.prefix');
        try {
            $res = $this
                ->field($prefix . 'write_off.*,' . $prefix . 'cami_write.amount as channelAmount,' . $prefix . 'cami_write.id,' . $prefix . 'cami_write.rate,'.$prefix . 'cami_write.status as CamiStatus')
                ->where('cami_type_sign','=',$cardTypeSign)
                ->leftjoin($prefix . 'write_off', $prefix . 'write_off.write_off_id = ' . $prefix . 'cami_write.write_off_id')
                ->paginate($limit);

        } catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, $res, 'ok');
    }

    /**
     * 增加卡密绑定核销商服务商
     * @param $param
     * @return array
     */
    public function add($param)
    {
        try {

            $has = $this
                ->where('write_off_id', $param['write_off_id'])
                ->where('cami_type_id', $param['cami_type_id'])
                ->findOrEmpty()->toArray();
            if (!empty($has)) {
                return modelReMsg(-2, '', '已经存在绑定关系');
            }

            $param['add_time'] = date("Y-m-d H:i:s", time());
            $this->insert($param);
        } catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, '', '添加卡密绑定服务商成功');
    }

    /**
     * 获取卡密绑定核销商服务商信息
     * @param $id
     * @return array
     */
    public function getById($id)
    {
        try {

            $info = $this->where('id', $id)->findOrEmpty()->toArray();
        } catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, $info, 'ok');
    }

    /**
     * 编辑卡密绑定核销商服务商
     * @param $param
     * @return array
     */
    public function edit($param)
    {
        try {
            $has = $this
                ->where('write_off_id', '=', $param['write_off_id'])
                ->where('cami_type_id', '=', $param['cami_type_id'])
                ->where('id', '<>', $param['id'])
                ->findOrEmpty()->toArray();
            if (!empty($has)) {
                return modelReMsg(-2, '', '已经存在绑定关系');
            }

            $param['last_update_time'] = date("Y-m-d H:i:s", time());


            $this->save($param, ['id' => $param['id']]);
        } catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, '', '编辑卡密绑定核销商服务商成功');
    }

    /**
     * 删除商户
     * @param $merchantId
     * @return array
     */
    public function delWriteoff($writeOffId)
    {
        try {
            if (1 == $writeOffId) {
                return modelReMsg(-2, '', '测试核销商户不可删除');
            }

            $this->where('write_off_id', $writeOffId)->delete();
        } catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

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
        } catch (\Exception $e) {

            return modelReMsg(-1, '', $e->getMessage());
        }

        return modelReMsg(0, $info, 'ok');
    }
}