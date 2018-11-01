<?php
namespace app\admin\validate;
use think\Validate;
class HelpValidate extends Validate
{
    protected $rule = [
        'problem'   =>  'unique:help',
        'answer'    =>  'require',
        'type'      =>  'require',
    ];
    
    protected $message = [
    	'type.require'     =>  '类型必须选择',
        'problem.unique'   =>  '标题重复',
        'answer.require'   =>  '内容必须填写',
    ];
}