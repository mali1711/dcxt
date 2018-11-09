<?php
namespace app\admin\model;
use think\Model;
class Cart extends Model
{
    protected $resultSetType = 'collection';
    //获取所有的数据
    public static function GetAll()
    {
    	$result = Cart::order('id', 'desc')->paginate(15);
    	return $result;
    }

    //接口数据
    public static function GetAllApi()
    {
        $result = Cart::all();
        return $result;
    }
    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id)
    {
        $result = Cart::find($id);
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {
        $Cart = New Cart;
        $result = $Cart->save($data);
        return $result;
    }
    //执行修改
    public static function UpdateData($id,$data)
    {
        $Cart = New Cart;
        $result = $Cart->save($data,['id'=>$id]);
        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Cart::destroy($id);
        return $result;
    }
}