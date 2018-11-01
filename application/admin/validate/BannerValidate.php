<?php
namespace app\admin\validate;
use think\Validate;
class BannerValidate extends Validate
{
    protected $rule = [
        'banner_name'  =>  'require',
        'banner_img'  =>  'require',
    ];
    
    protected $message = [
        'banner_name.require'  =>  '礼盒名称必须填写',
        'banner_img.require'  =>  '礼盒单价必须填写',
    ];
}