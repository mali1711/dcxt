<?php
namespace app\admin\model;
use think\Model;
class Banner extends Model
{
    //获取所有的数据
    public static function GetAll()
    {
    	$result = Banner::order('id', 'desc')->paginate(15);
    	return $result;
    }

    //接口数据
    public static function GetAllApi()
    {
        $result = Banner::all(['is_type'=>0]);
        return $result;
    }
    //交流互动接口
    public static function GetProAllApi()
    {
        $result = Banner::all(['is_type'=>1]);
        return $result;
    }
    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id)
    {
        $result = Banner::find($id);
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {
        $Banner = New Banner;
        $result = $Banner->validate('BannerValidate')->save($data);
        if ($result === false) {
            return  json_encode(["code"=>1,"meg"=>$Banner->getError()]);
        }else{
            return json_encode(["code"=>0,"meg"=>"操作成功"]);
        }
    }
    //执行修改
    public static function UpdateData($id,$data)
    {
        $Banner = New Banner;
        $result = $Banner->save($data,['id'=>$id]);
        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Banner::destroy($id);
        return $result;
    }
}