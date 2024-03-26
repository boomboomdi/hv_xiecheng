<?php

namespace app\api\validate;

use think\Validate;

class NotifyzhoucardValidate extends Validate
{
    protected $rule = [
        'bizOrderNo' => 'require',
        'cardAmount' => 'require',
        'cardTypeName' => 'require',
        'cardType' => 'require',
        'bindState' => 'require',
        'message' => 'require',
    ];

    protected $message = [
        'bizOrderNo.require' => 'uploadId require ',
        'cardAmount.require' => 'uploadId require ',
        'cardTypeName.require' => 'cardList require',
        'cardType.require' => 'cardList require',
        'bindState.require' => 'cardList require',
        'message.require' => 'cardList require',
    ];
    protected $scene = [
    ];
}