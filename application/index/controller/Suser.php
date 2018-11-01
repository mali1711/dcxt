<?php
namespace app\index\controller;
use think\Loader;
use think\Db;
use think\Session;
use app\index\controller\Controller;
use app\index\model\User as UserModel;
class Suser extends Controller
{

    public function index()
    { 
        // Session::delete('uid');
        // dump(session('uid'));
        $uid = session('uid');

        $where = [];
        $where['uid'] = $uid;
        // $where['putStatus'] = 1;

        $list = UserModel::getProfitList($uid);

		return $this->fetch('merchants/index',['list'=>$list]);
    }

    public function Suser()
    {

        return $this->fetch('merchants/my');
    }
    public function Erweima()
    {

        $Qrcode = Db::name('user')->where('id',session('uid'))->value('Qrcode');
        return $this->fetch('merchants/erweima', ['Qrcode'=>$Qrcode]);
    }

    public function updatePass()
    {
        return $this->fetch('merchants/updatePass',[
            'list'=>null,   
        ]);
    }

    public function Password()
    {
        return $this->fetch('merchants/password',[
            'list'=>null,   
        ]);
    }


    public function bandPhone()
    {
        return $this->fetch('merchants/phone',[
            'list'=>null,   
        ]);
    }

    // public function DoBandPhone()
    // {
    //     return $this->fetch('songshuiyuan/phone',[
    //         'list'=>null,   
    //     ]);
    // }

    public function updateImage()
    {
        return $this->fetch('merchants/head',[
            'list'=>null,   
        ]);
    }


    public function DoupdateImage()
    {     
        $uid = session('uid');
        // dump($_POST['photo']);die();
        $img = $_POST['photo'];
        list($type,$data) = explode(',', $img);  
        if(strstr($type,'image/jpeg')!==''){  
            $ext = '.jpg';  
        }elseif(strstr($type,'image/gif')!==''){  
            $ext = '.gif';  
        }elseif(strstr($type,'image/png')!==''){  
            $ext = '.png';  
        }  
        $time=time().rand(000,999).$ext;

        $path=ROOT_PATH . 'public' . DS . 'uploads' . DS . $time;

        //网站根目录绝对路径(ROOT_PATH)
        //define('ROOT_PATH', str_replace("\\","/",realpath(dirname(__FILE__).'/../')));
        //网站http相对根目录
        define('HTTP_ROOT_PATH', str_replace(str_replace('\\', '/', (strrpos($_SERVER['DOCUMENT_ROOT'], '/'))==strlen($_SERVER['DOCUMENT_ROOT'])-1)?substr($_SERVER['DOCUMENT_ROOT'], 0, strlen($_SERVER['DOCUMENT_ROOT'])-1):($_SERVER['DOCUMENT_ROOT']), '', ROOT_PATH));
        $res=file_put_contents($path,base64_decode($data),true);
         if($res!=false){//文件写入成功
            //your DB action
        }
        
        // $realpath=HTTP_ROOT_PATH.'/groups/'.$time;//相对路径
        $ret = array('img'=>$path);

        $result = '/uploads/' . $time;
        // dump($result);die();
        // echo json_encode($ret);
        if (!$result) {
            return ['code'=>1025,'msg'=>'图片上传失败'];
        }

        if (!$uid) {
            return ['code'=>1025,'msg'=>'用户标识不存在'];
        }  

        $ress = Db::name('user')->where('id',$uid)->update(['photo'=>$result]);

        if ($ress) {
            return ['code'=>1001,'msg'=>'修改成功'];
        } else {
            return ['code'=>1025,'msg'=>'修改失败'];
        }
     
    }

    public function yuE()
    {
        return $this->fetch('merchants/yu-e',[
            'list'=>null,   
        ]);
    }

    public function Record()
    {   

        $uid = session('uid');

        $where = [];
        $where['uid'] = $uid;
        // $where['putStatus'] = 1;

        $data = Db::name('put_forward')->where($where)->order('id desc')->select();

        // dump($data);die();
        // $this->assign('list',$data);
        return $this->fetch('merchants/record',['list'=>$data]);
    }

    public function Phone()
    {
        return $this->fetch('merchants/phone',[
            'list'=>null,   
        ]);
    }

    public function Head()
    {
        return $this->fetch('merchants/head',[
            'list'=>null,   
        ]);
    }
    public function Withdrawal()
    {
        return $this->fetch('merchants/withdrawal',[
            'list'=>null,   
        ]);
    }

    public function Earnings()
    {   

        $uid = session('uid');

        $times = $_GET['times'];

        $list = UserModel::getProfitDetailList($uid,$times);
         // halt($list);
         $times = date('Y-m-d',$times);
        return $this->fetch('merchants/earnings',[
            'list'=>$list,'times'=>$times
        ]);
    }

    public function Tixian()
    {
        $uid = session('uid');
        if (!$uid) {
            return ['code'=>1025,'msg'=>'缺少用户标识'];
        }

        // 验证金额
        if (isset($_GET['NewMoney']) && $_GET['NewMoney']) {
            
            //正则验证
            
            if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', trim($_GET['NewMoney']))) {
                return ['code'=>1025,'msg'=>'提现金额格式错误'];
            }

            if ($_GET['NewMoney'] <= 0) {
                return ['code'=>1025,'msg'=>'请输入提现金额'];
            }

        } else {
            return ['code'=>1025,'msg'=>'请输入提现金额'];
        }
        if ($_GET['userName'] == null){
            return ['code'=>1025,'msg'=>'请输入姓名'];
            // throw new \Exception('请输入密码');
        } 
        if ($_GET['mobile'] == null){
            return ['code'=>1025,'msg'=>'请输入手机号'];
            // throw new \Exception('请输入手机号');
        }

        if ($_GET['recode'] == null){
            return ['code'=>1025,'msg'=>'请输入验证码'];
            // throw new \Exception('请输入验证码');
        }



        // 验证手机号码
        $resInfo    = Db::name('user')->where(['id'=>$uid,'mobile'=>$_GET['mobile']])->find();
            
        if (!$resInfo) {
             return ['code'=>1025,'msg'=>'该手机号不是您绑定的手机号码！'];
        }

        $money = Db::name('user')->where(['id'=>$uid])->value('money');

        if ($money < $_GET['NewMoney']) {
            return ['code'=>1025,'msg'=>'提现金额大于可提现余额，余额不足'];
        }

        $newsMoney = $money - $_GET['NewMoney'];

        // 提现扣除金额
        $result =  Db::name('user')->where(['id'=>$uid])->update(['money'=>$newsMoney]);

        if ($result) {
            $result = 1001;
        }
        $result = self::CheckRecode();

        if ($result == 1001) {
            $data = [];
            $data['username']    = $_GET['userName'];
            $data['mobile']      = $_GET['mobile'];
            $data['uid']         = $uid;
            $data['money']       = $_GET['NewMoney'];
            $data['createTime']  = time();
            // dump($data['mobile']);die();
            $res = Db::name('put_forward')->insert($data);
            // dump(Db::name('user')->GetLastSql());die();
            
            if ($res){
                return ['code'=>1001,'msg'=>'申请成功'];
            } else {
                return ['code'=>1025,'msg'=>'申请失败'];
            }
        }
        if ($result == 1025) {
            return ['code'=>1025,'msg'=>'验证码错误'];
        }
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
