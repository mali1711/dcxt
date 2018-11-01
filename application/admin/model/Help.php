<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Help extends Model
{
    //获取所有的数据
    public static function GetAll($type)
    {
    	$result = Help::where('type',$type)->order('id', 'desc')->paginate(15);
    	return $result;
    }
    /**
     * 接口数据
     * @Author   CarLos(wang)
     * @DateTime 2018-06-05
     * @Email    carlos0608@163.com
     */
    public static function GetAllAPi()
    {
        $result = Help::all();
        return $result;
    }
    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id)
    {
        $result = Help::find($id);
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {
        $Help = New Help;
        $result = $Help->validate('HelpValidate')->save($data);
        if ($result === false) {
            return  json_encode(["code"=>1,"meg"=>$Help->getError()]);
        }else{
            return json_encode(["code"=>0,"meg"=>"操作成功"]);
        }
    }
    //执行修改
    public static function UpdateData($id,$data)
    {
        $Help = New Help;
        $result = $Help->save($data,['id'=>$id]);
        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Help::destroy($id);
        return $result;
    }

    public static function getMessageList($type,$uid)
    {
        $wheres = [];
        if ($type == 2) {

            $where = 'type = 2 or type = 3 or type = 4';

        } else {
            $where = [];
            $where['type'] = $type;
        }

       
        $result = Db::name('Help')->where($where)->where($wheres)->order('id desc')->select();

        if ($result) {
            foreach ($result as $k => $value){

               $result[$k]['xia'] = $k;
               $result[$k]['nian'] = date('Y-m-d',$value['create_time']);
               $result[$k]['shi'] = date('H:i:s',$value['create_time']);

                if ($uid) {
                    if ($value['type'] == 3 || $value['type'] == 4 || $value['type'] == 5){
                        if ($uid != $value['uid']) {
                            unset($result[$k]);
                        }
                    }
               }

            }
        }
        
        return $result;
    }

   public static function MessageNumber($uid)
    {

        $wheres = [];

        $wheres['status'] = 0;

        $where = 'type = 2 or type = 3 or type = 4';
       
        $result = Db::name('Help')->where($where)->where($wheres)->order('id desc')->select();

        if ($result) {
            foreach ($result as $k => $value){

               $result[$k]['xia'] = $k;
               $result[$k]['nian'] = date('Y-m-d',$value['create_time']);
               $result[$k]['shi'] = date('H:i:s',$value['create_time']);

                if ($uid) {
                    if ($value['type'] == 3 || $value['type'] == 4 || $value['type'] == 5){
                        if ($uid != $value['uid']) {
                            unset($result[$k]);
                        }
                    }
               }

            }
        }
        $data = 0;

        if ($result) {
            $data = count($result);
        }
       

        return $data;
    }

}