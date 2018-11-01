<?php
namespace app\admin\model;
use think\Model;
use think\Db;

class Region extends Model
{
    //获取所有的数据
    public static function GetAll()
    {
    	$result = Region::where('status',1)->order('id', 'asc')->paginate(15);
    	return $result;
    }

    //接口数据
    public static function GetWechatAll($userid)
    {
        $result = Region::all(['userid'=>$userid]);
        return $result;
    }
    //查找一条数据
    public static function GetOne($id)
    {
        $result = Region::find($id);
        return $result;
    }
    //多条件查询一条数据
    public static function GetCondOne($where)
    {
        $result = Region::where($where)->find();
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {

        $Region = New Region;

        $result = $Region->validate('RegionValidate')->save($data);

        if ($result === false) {
            return  json_encode(["code"=>0,"meg"=>$Region->getError()]);
        }else{
            return json_encode(["code"=>1,"meg"=>"添加成功"]);
        }
        return $result;
    }
    //执行修改
    public static function UpdateData($id,$data)
    {

        $Region = New Region;

        $validate = validate('RegionValidate');

        // if ($validate->check($data)) {
        	$result = $Region->validate('RegionValidate')->save($data,['id'=>$id]);
        // }

        if ($result === false) {
            return  json_encode(["code"=>0,"meg"=>$Region->getError()]);
        }else{
            return json_encode(["code"=>1,"meg"=>"修改成功"]);
        }

        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Region::where('id',$id)->update(['status'=>0]);

        if ($result === false) {
            return  json_encode(["code"=>0,"meg"=>$Region->getError()]);
        }else{
            return json_encode(["code"=>1,"meg"=>"操作成功"]);
        }   
        return $result;
    }
}