<?php
namespace app\admin\validate;
use think\Validate;
class UserValidate extends Validate
{
    protected $rule = [
        'username'  =>  'unique:user',
    ];
    
    protected $message = [
        'username.unique'  =>  '用户名已存在',
    ];
}