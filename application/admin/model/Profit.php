<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use app\admin\model\User as UserModel;

class Profit extends Model
{
      // 收益列表
    public static function getAll($uid)
    {
        $where = [];

        $where['status']      = 1;    

        $where['uid']         = $uid;

        $userInfo = UserModel::GetOne($uid);

        $title    = Db::name('info_show')->where('uid', $uid)->value('title');

        $orderList = Profit::where($where)->group('times')->order('times desc')->paginate(15);

        if ($orderList) {
            foreach ($orderList as $key => $value) {
                $orderList[$key]['money'] = Db::name('profit')->where(['uid'=>$uid,'status'=>1,'times'=>$value['times']])->sum('money');
                $orderList[$key]['nickName'] = $userInfo['nickname'];
                $orderList[$key]['title']    = $title;
                $orderList[$key]['mobile']   = $userInfo['mobile'];
                $orderList[$key]['photo']    = $userInfo['photo'];
            }
        }

        return $orderList;
    }
}