<?php
namespace app\admin\model;
use think\Model;
class GiftBox extends Model
{
    //获取所有的数据
    public static function GetAll()
    {
    	$result = GiftBox::order('id', 'desc')->paginate(15);
    	return $result;
    }
    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id)
    {
        $result = GiftBox::find($id);
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {
        $GiftBox = New GiftBox;
        $result = $GiftBox->validate('GiftBoxValidate')->save($data);
        if ($result === false) {
            return  json_encode(["code"=>2,"meg"=>$GiftBox->getError()]);
        }else{
            return json_encode(["code"=>0,"meg"=>"操作成功"]);
        }
    }
    //执行修改
    public static function UpdateData($id,$data)
    {
        $GiftBox = New GiftBox;
        $result = $GiftBox->save($data,['id'=>$id]);
        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = GiftBox::destroy($id);
        return $result;
    }
}