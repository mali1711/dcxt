<?php
namespace app\admin\validate;
use think\Validate;
class GiftBoxValidate extends Validate
{
    protected $rule = [
        'box_name'  =>  'require',
        'box_pic'  =>  'require',
    ];
    
    protected $message = [
        'box_name.require'  =>  '礼盒名称必须填写',
        'box_pic.require'  =>  '礼盒单价必须填写',
    ];
}