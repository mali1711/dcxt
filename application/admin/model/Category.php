<?php
namespace app\admin\model;
use think\Model;
class Category extends Model
{
    //获取所有的数据
    public static function GetAll()
    {
    	$result = Category::where('is_type',1)->order(['order'=>'desc', 'id'=>'desc'])->paginate(15);
    	return $result;
    }
     //礼盒分类的所有数据
    public static function GetBoxAll()
    {
        $result = Category::where('is_type',0)->order('id', 'desc')->paginate(15);
        return $result;
    }
    //数据包
    public static function GetDataAll()
    {
        $result = Category::all(['is_type'=>1]);
        return $result;
    }
    //礼盒分类数据包
    public static function GetBoxDataAll()
    {
        $result = Category::all(['is_type'=>0]);
        return $result;
    }
    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id)
    {
        $result = Category::find($id);
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {
        $Category = New Category;

        $result = $Category->validate('CategoryValidate')->save($data);
        if ($result === false) {
            return  json_encode(["code"=>0,"meg"=>$Category->getError()]);
        }else{
            return json_encode(["code"=>1,"meg"=>"操作成功"]);
        }
    }
    //执行修改
    public static function UpdateData($id,$data)
    {
        $Category = New Category;

        $result = $Category->validate('CategoryValidate')->save($data,['id'=>$id]);
        // halt($Category->getLastSql());
        if ($result) {
            return json_encode(["code"=>1,"meg"=>"操作成功"]);
        }else{
            return json_encode(["code"=>0,"meg"=>"操作失败"]);
        }


    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Category::destroy($id);
        return $result;
    }
}