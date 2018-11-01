<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use app\admin\model\User as UserModel;

class Salary extends Model
{
    //获取所有的数据
    public static function GetAll($id)
    {

        $where = [];

        $where['status']      = 1;    

        $where['uid']         = $id;

        $userInfo = UserModel::GetOne($id);
// halt($userInfo);

    	$result = Salary::where($where)->group('createTime')->order('createTime desc')->paginate(15);

        if ($result) {
            foreach ($result as $key => $value) {
                    $result[$key]['nickName'] = $userInfo['nickname'];
                    $result[$key]['mobile']   = $userInfo['mobile'];
                    $result[$key]['photo']    = $userInfo['photo'];

                    $result[$key]['xinzi']    = Salary::where(['uid'=>$id,'createTime'=>$value['createTime']])->sum('money') + $userInfo['price'];
            }
        }

    	return $result;
    }
}