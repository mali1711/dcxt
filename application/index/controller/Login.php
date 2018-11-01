<?php
namespace app\index\controller;
use think\Loader;
use think\Db;
use app\index\controller\Base;
use app\index\controller\Sms;
use app\index\model\User as UserModel;
class Login extends Base
{
	public function _initialize(){
        $uid = session('uid');
        // dump($uid);die();
        if($uid != null){
            $data = Db::name('user')->find($uid);
            if ($data) {
                $data['num'] = Db::name('order')->where(['status'=>1,'SuId'=>$uid,'orderStatus'=>4])->count();
            }
            $this->assign('userInfo',$data);
            $this->fetch('merchants/my');
            // $this->success('已登录','User/index');
        }
    }

    public function Login()
    {
        return $this->fetch('songshuiyuan/login');
    }

    public function updatePass()
    {
        return $this->fetch('songshuiyuan/updatePass',[
            'list'=>null,   
        ]);
    }

    public function SLogin()
    {
        return $this->fetch('merchants/slogin');
    }

    public function DoLogin()
    {

        $u   = new UserModel;
        
        $res = $u->DoLogin();

        if (!$res) {
        
            return $this->error('账号或密码输入错误');
        }

        return $this->success('登陆成功', 'User/index');
    }

    public function UpdatePhone()
    {
        $uid = session('uid');
        if (!$uid) {
            return ['code'=>1025,'msg'=>'缺少用户标识'];
        }
        $result = self::CheckRecode();

        if ($result == 1001) {
            
            $data['mobile'] = $_GET['mobile'];
            // dump($data['mobile']);die();
            $res = Db::name('user')->where(['id'=>$uid])->update($data);
            // dump(Db::name('user')->GetLastSql());die();
            if ($res){
                return ['code'=>1001,'msg'=>'绑定成功'];
            } else {
                return ['code'=>1025,'msg'=>'绑定失败'];
            }
        }
        if ($result == 1025) {
            return ['code'=>1025,'msg'=>'验证码错误'];
        }
        // dump($result);die;
    }


    public function DoUpdatePass()
    {
        $uid = session('uid');

        if ($uid == null || !intval($uid)){
            return ['code'=>1025,'msg'=>'用户身份无效'];
                // throw new \Exception('用户身份无效');
            }

            if ($_GET['mobile'] == null){
                return ['code'=>1025,'msg'=>'请输入手机号'];
                // throw new \Exception('请输入手机号');
            }
            if ($_GET['recode'] == null){
                return ['code'=>1025,'msg'=>'请输入验证码'];
                // throw new \Exception('请输入验证码');
            }
            if ($_POST['password'] == null){
                return ['code'=>1025,'msg'=>'请输入密码'];
                // throw new \Exception('请输入密码');
            }
            if ($_POST['password'] !== $_POST['repassword']){
                return ['code'=>1025,'msg'=>'两次密码不一致'];
                // throw new \Exception('两次密码不一致');
            }


            // 验证手机号码
            $resInfo    = Db::name('user')->where(['id'=>$uid,'mobile'=>$_POST['mobile']])->find();
                
            if (!$resInfo) {
                 return ['code'=>1025,'msg'=>'该手机号不是您绑定的手机号码！'];
            }



        $result = self::CheckRecode();




        if ($result == 1001) {
            
            $data['password'] = md5($_POST['password']);
            // dump($data['mobile']);die();
            $res = Db::name('user')->where(['id'=>$uid])->update($data);
            // dump(Db::name('user')->GetLastSql());die();
            if ($res){
                return ['code'=>1001,'msg'=>'修改成功'];
            } else {
                return ['code'=>1025,'msg'=>'修改失败'];
            }
        }
        if ($result == 1025) {
            return ['code'=>1025,'msg'=>'验证码错误'];
        }
        // dump($result);die;
    }
    
    /**
     * 验证短信验证码
     * @Author   CarLos(wang)
     * @DateTime 2018-06-08
     * @Email    carlos0608@163.com
     */
    public static function CheckRecode()
    {
        $mobile = $_GET['mobile'];
        $clent_recode = $_GET['recode'];
        $ReCode = cache('reCode'.$mobile);
        if ($ReCode != null && $clent_recode == $ReCode) {
            return 1001;
        }else{
            return 1025;
        }
    }



}
