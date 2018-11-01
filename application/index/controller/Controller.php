<?php
namespace app\index\controller;
use think\Loader;
use think\Db;
use think\Session;
class Controller extends \think\Controller
{
 

     public function _initialize(){
         // Session::delete('uid');
        $uid = session('uid');

        $identity = Db::name('user')->where('id',$uid)->value('identity');

        if (2 !== $identity) {
            $this->error('账号异常，请重新登陆','Slogin/Slogin');
        }

        if($uid == null){
            $this->error('请先登陆后操作','Slogin/Slogin');
        } else {
          
          $userInfo = Db::name('user')->find($uid);

          $this->assign('userInfo', $userInfo);
        }
    }

     protected function Check()
     {
     	try {
     		if(true){
                throw new \Exception("这是一个演示", 2);
     		}
     	} catch (\Exception $e) {
     		echo $e->getMessage();
     	}
     }


}
