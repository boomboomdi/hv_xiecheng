<?php

namespace app\api\validate;

use think\Validate;

class NotifycardValidate extends Validate
{
    protected $rule = [
        'uploadId' => 'require',
        'cardList' => 'require',
        'sign' => 'require',
    ];

    protected $message = [
        'uploadId.require' => 'uploadId require ',
        'cardList.require' => 'cardList require',
        'sign.require' => 'sign require',
    ];
    protected $scene = [
        'notify' => ['merchant_sign', 'order_no', 'payment'],
    ];
}