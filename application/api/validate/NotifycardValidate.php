<?php

namespace app\api\validate;

use think\Validate;

class NotifycardValidate extends Validate
{
    protected $rule = [
        'uploadId' => 'require',
        'results' => 'require',
        'sign' => 'require',
    ];

    protected $message = [
        'uploadId.require' => 'require merchant_sign',
        'results.max' => 'merchant_sign format error',
        'sign.max' => 'sign format error',
    ];
    protected $scene = [
        'notify' => ['merchant_sign', 'order_no', 'payment'],
    ];
}