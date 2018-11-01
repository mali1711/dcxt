<?php
namespace app\admin\model;
use think\Model;
class Interactive extends Model
{
    //获取所有的数据
    public static function GetAll()
    {
    	$result = Interactive::order('id', 'desc')->paginate(15);
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
        $result = Interactive::all();
        return $result;
    }


    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id)
    {
        $result = Interactive::find($id);
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {
        $Interactive = New Interactive;
        $result = $Interactive->validate('InteractiveValidate')->save($data);
        if ($result === false) {
            return  json_encode(["code"=>1,"meg"=>$Interactive->getError()]);
        }else{
            return json_encode(["code"=>0,"meg"=>"操作成功"]);
        }
    }
    //执行修改
    public static function UpdateData($id,$data)
    {
        $Goods = New Interactive;
        $result = $Goods->save($data,['id'=>$id]);
        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Interactive::destroy($id);
        return $result;
    }
}