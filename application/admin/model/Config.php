<?php
namespace app\admin\model;
use think\Model;
class Config extends Model
{
    //获取所有的数据
    public static function GetConAll()
    {
    	$result = Config::find();
    	return $result;
    }

    //接口数据
    public static function GetAllApi()
    {
        $result = Config::find();
        return $result;
    }

    //执行修改
    public static function UpdateData($data)
    {
        $Config = New Config;
        $result = $Config->save($data,['id'=>1]);
        return $result;
    }
}