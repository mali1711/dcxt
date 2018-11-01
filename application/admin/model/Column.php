<?php
namespace app\admin\model;
use think\Model;
use app\admin\validate\ColumnPostValidate;
class Column extends Model
{
    //获取所有的数据
    public static function GetAll()
    {
    	$result = Column::all();
    	return $result;
    }
    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id)
    {
        $result = Column::find($id);
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {
        $Column = New Column;
        $result = $Column->validate('ColumnPostValidate')->save($data);
        if ($result === false) {
            return  json_encode(["code"=>0,"meg"=>$Column->getError()]);
        }else{
            return json_encode(["code"=>1,"meg"=>"操作成功"]);
        }
    }
    //执行修改
    public static function UpdateData($id,$data)
    {
        $Column = New Column;
        $result = $Column->save($data,['id'=>$id]);
        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Column::destroy($id);
        return $result;
    }
}