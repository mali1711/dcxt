<?php
namespace app\admin\model;
use think\Model;
class Praise extends Model
{
    //获取所有的数据
    public static function GetAll()
    {
    	$result = Praise::order('id', 'desc')->paginate(15);
    	return $result;
    }

    /**
     *  接口数据
     * @Author   CarLos(翟)
     * @DateTime 2018-06-05
     * @Email    carlos0608@163.com
     */
    public static function GetAllAPi()
    {
        $result = Praise::all();
        return $result;
    }


    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id)
    {
        $result = Praise::find($id);
        return $result;
    }

    /**
     * @param 多件查找
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function GetWhereOne($where)
    {
        $result = Praise::where($where)->find();
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {
        $Praise = New Praise;
        $result = $Praise->save($data);
        return $result;
    }
    //执行修改
    public static function UpdateData($id,$data)
    {
        $Praise = New Praise;
        $result = $Praise->save($data,['id'=>$id]);
        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Praise::destroy($id);
        return $result;
    }
}