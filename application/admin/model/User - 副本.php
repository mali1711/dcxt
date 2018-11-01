<?php
namespace app\admin\model;
use think\Model;
class User extends Model
{
    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id)
    {
        $result = User::find($id);
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {
        $User = New User;
        $result = $User->save($data);
        return $User->id;
    }
    //执行修改
    public static function UpdateData($id,$data)
    {
        $User = New User;
        $result = $User->save($data,['id'=>$id]);
        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = User::destroy($id);
        return $result;
    }
}