<?php
namespace app\admin\validate;
use think\Validate;
class CategoryValidate extends Validate
{
    protected $rule = [
        'name'  =>  'require',
        'name'  =>  'unique:category'
        // 'description'  =>  'require',
    ];
    
    protected $message = [
        'name.require'  =>  '分类名称不能为空',
        'name.unique'  =>  '分类名称已存在',
        // 'description.require'  =>  '分类简述必须写',
    ];
}