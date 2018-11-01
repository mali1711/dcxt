<?php
namespace app\admin\validate;
use think\Validate;
class ColumnPostValidate extends Validate
{
    protected $rule = [
        'name'  =>  'require',
        'icon'  =>  'require',
        'url'  =>  'require',
    ];
    
    protected $message = [
        'name.require'  =>  '用户名必须填写',
        'icon.require'  =>  '图标必须填写',
        'url.require'  =>  '地址必须填写',
    ];
}