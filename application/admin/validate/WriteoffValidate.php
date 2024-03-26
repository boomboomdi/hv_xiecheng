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

class WriteoffValidate extends Validate
{
    protected $rule =   [
        'write_off_sign'  => 'require',
    ];

    protected $message  =   [
        'write_off_sign.require' => '请输入核销商标识',
    ];
}