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

class CamitypeValidate extends Validate
{
    protected $rule =   [
        'cami_type_sign'  => 'require',
        'cami_type_username'  => 'require',
    ];

    protected $message  =   [
        'cami_type_sign.require' => '请输入卡密标识',
        'cami_type_username.require'   => '请输入卡种名称',
    ];
}