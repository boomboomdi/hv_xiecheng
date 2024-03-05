<?php
/**
 * Created by PhpStorm.
 * User: bl
 * Email: bl@qq.com
 * Date: 2020/10/8
 * Time:  15:54
 */
namespace app\admin\validate;

use think\Validate;

class CamitypechannelValidate extends Validate
{
    protected $rule =   [
        'weight'  => 'require',
        'write_off_id'  => 'require',
        'cami_type_sign'  => 'require',
        'cami_type_id'  => 'require',
    ];

    protected $message  =   [
        'weight.require' => '请输入权重值',
        'cami_type_id.require' => '卡种不能为空',
        'cami_type_sign.require' => '卡种标识不能为空',
        'write_off_id.require' => '请选择核销服务商',
    ];
}