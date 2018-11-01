<?php
namespace app\admin\validate;
use think\Validate;
class GoodsValidate extends Validate
{
    protected $rule = [
        'goods_pic'   =>  'require',
        'goods_info'  =>  'require',
        'goods_name'  =>  'require|unique:goods'
        // 'serival'  =>  'require',
    ];
    
    protected $message = [
        'goods_name.require' =>  '商品名称必须填写',
        'goods_pic.require'  =>  '商品单价必须填写',
        'goods_info.require' =>  '商品详情必须填写',
        'goods_name.unique'  =>  '商品名称已存在',
        // 'serival.require'  =>  '编号',
    ];
}