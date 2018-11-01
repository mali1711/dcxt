<?php
namespace app\admin\validate;
use think\Validate;
class InteractiveValidate extends Validate
{
    protected $rule = [
        'title'  =>  'require',
        'content'  =>  'require',
    ];
    
    protected $message = [
        'title.require'  =>  '标题必须添加',
        'content.require'  =>  '内容必须填写',
    ];
}