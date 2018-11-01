<?php
namespace app\admin\validate;
use think\Validate;
class RegionValidate extends Validate
{
    protected $rule = [
        'title'  =>  'require|unique:region',
        // 'serival'  =>  'require',
    ];
    
    protected $message = [
        'title.require'  =>  '片区名称必须填写',
        'title.unique'   =>  '片区名称已存在',
        // 'serival.require'  =>  '编号',
    ];

    protected $scene = [
	'DoUpdate' => ['title.unique'=>'require'],
	'DoAdd'    => ['title'],
	];
}