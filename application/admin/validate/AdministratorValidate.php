<?php
namespace app\admin\validate;
use think\Validate;
class AdministratorValidate extends Validate
{
    protected $rule = [
        'username'  =>  'require',
        'password'  =>  'require',
        'nickname'  =>  'require',
    ];
    
    protected $message = [
        'username.require'  =>  '用户名必须填写',
        'password.require'  =>  '密码必须填写',
        'nickname.require'  =>  '昵称必须填写',
    ];
}