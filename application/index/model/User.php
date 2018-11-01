<?php 
namespace app\index\model;
use think\Model;
use think\Db;
use think\Session;
class User extends Model
{
	//根据openid查找数据
	public static function getByOpenID($openid)
    {
        $user = User::where('openid', '=', $openid)
            ->find();
        return $user;
    }
    //获取所有的数据
    public static function GetUserAll(){
    	$GetUser = User::all();
    	return $GetUser;
    }
    /**
     * 查找用户的单个数据
     */
    public static function GetUserOne($id){
        $result = User::find($id);
        return $result;
    }

    /**
     * 查找用户的单个数据  送水员端
     */
    public static function DoLogin(){

        // $userName = 1;
        $data = [];
        
        if (isset($_POST['userName']) && $_POST['userName']) {
            $data['username'] = $_POST['userName'];
        } else {
            $data['userName'] = '';
        }

        if (isset($_POST['password']) && $_POST['password']) {
            $data['password'] = md5($_POST['password']);
        } else {
            $data['password'] = '';
        }

        if (isset($_POST['identity']) && $_POST['identity']) {
            $data['identity'] = $_POST['identity'];
        } else {
            $data['identity'] = '';
        }

        $result = User::where($data)->find();

        if ($result) {
            Session::set('uid',$result->id);
        }

        return $result;
        // $result = User::find($id);
        // return $result;
    }

    // 获取收益记录
    public static function getProfitDetailList($uid,$times)
    {
        $where = [];

        $where['status']      = 1;    

        $where['uid']         = $uid;

        $where['times']       = $times;

        $orderList = Db::name('profit')->where($where)->order('createTime desc')->select();

        $userMoney = Db::name('user')->where('id',$uid)->value('money');

        if($orderList) {

            foreach ($orderList as $key => $value) {
                    $orderList[$key]['createTime']  = date('Y-m-d H:i:s',$value['createTime']);
                    $orderList[$key]['userMoney']   = $userMoney;
                    $orderList[$key]['userName'] = User::where('id',$value['userId'])->value('nickName');

            }
            
        }
        
        return $orderList;
    }

    // 收益列表
    public static function getProfitList($uid)
    {
        $where = [];

        $where['status']      = 1;    

        $where['uid']         = $uid;

        $orderList = Db::name('profit')->where($where)->group('times')->order('times desc')->select();

        if ($orderList) {
            foreach ($orderList as $key => $value) {
                $orderList[$key]['totalMoney'] = Db::name('profit')->where(['uid'=>$uid,'status'=>1,'times'=>$value['times']])->sum('money');
            }
        }

        return $orderList;
    }




}
 ?>