<?php
namespace app\admin\model;
use think\Model;
use think\Db;

class Search extends Model
{
    //获取所有的数据
    public static function GetAll()
    {
    	$order  = ['order' => 'desc','id' => 'desc'];
    	$result = Search::where('status', 1)->order($order)->paginate(15);
    	return $result;
    }

    //接口数据
    public static function GetWechatAll($userid)
    {
        $result = Search::all(['userid'=>$userid]);
        return $result;
    }
    //查找一条数据
    public static function GetOne($id)
    {
        $result = Search::find($id);
        return $result;
    }
    //多条件查询一条数据
    public static function GetCondOne($where)
    {
        $result = Search::where($where)->find();
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {

        if ($data['title'] && isset($data['title'])) {
            
            if (trim($data['title']) == '') {
                return json_encode(["code"=>0,"meg"=>"添加失败,内容不可为空"]);
            }

        } else {
            
                return json_encode(["code"=>0,"meg"=>"添加失败,内容不可为空"]);
        }

        $Search = New Search;
        $result = $Search->save($data);

        if ($result === false) {
            return  json_encode(["code"=>0,"meg"=>$Category->getError()]);
        }else{
            return json_encode(["code"=>1,"meg"=>"添加成功"]);
        }
        return $result;
    }
    //执行修改
    public static function UpdateData($id,$data)
    {

         if ($data['title'] && isset($data['title'])) {
            
            if (trim($data['title']) == '') {
                return json_encode(["code"=>0,"meg"=>"修改失败,内容不可为空"]);
            }

        } else {
            
                return json_encode(["code"=>0,"meg"=>"修改失败,内容不可为空"]);

        }


        $Search = New Search;
        $result = $Search->save($data,['id'=>$id]);

        if ($result === false) {
            return  json_encode(["code"=>0,"meg"=>$Search->getError()]);
        }else{
            return json_encode(["code"=>1,"meg"=>"修改成功"]);
        }

        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Search::where('id',$id)->update(['status'=>0]);

        if ($result === false) {
            return  json_encode(["code"=>0,"meg"=>$Search->getError()]);
        }else{
            return json_encode(["code"=>1,"meg"=>"操作成功"]);
        }   
        return $result;
    }
}