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
        'bizOrderNo.require' => 'bizOrderNo require ',
        'cardAmount.require' => 'cardAmount require ',
        'cardTypeName.require' => 'cardTypeName require',
        'cardType.require' => 'cardType require',
        'bindState.require' => 'bindState require',
        'message.require' => 'message require',
    ];
    protected $scene = [
    ];
}