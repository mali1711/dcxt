<?php
namespace app\index\controller;
use think\Loader;
use think\Db;
use app\index\controller\Base;
use app\index\model\User as UserModel;
use app\admin\model\Order as OrderModel;
class User extends Base
{

    public function index()
    {

    	// $uid      = session('uid');
		return $this->fetch('songshuiyuan/my');
    }

    public function Xinzi()
    {

    	// parent::Check();
    	// parent::CheckAdminLogin();
		// $data = AddressModel::GetAll();
        $uid = session('uid');

        if (isset($_GET['times']) && $_GET['times']) {
            $times = $_GET['times'];
        } else {
            $times = strtotime(date('Y-m'),time());
        }  

        $data = OrderModel::getOrderLists($uid,$times);
            
        
		return $this->fetch('songshuiyuan/salary',[
			'list'=>$data,
		]);
    }

    public function XinziList()
    {

        // parent::Check();
        // parent::CheckAdminLogin();
        // $data = AddressModel::GetAll();
        $uid = session('uid');
        
        $list = OrderModel::getXinziList($uid);

        return $this->fetch('songshuiyuan/payroll_records',[
            'list'=>$list,   
        ]);
    }

    public function Suser()
    {
        // parent::Check();
        // parent::CheckAdminLogin();
        // $data = AddressModel::GetAll();
        return $this->fetch('merchants/my',[
            'list'=>null,   
        ]);
    }

    public function updatePass()
    {
        return $this->fetch('songshuiyuan/updatePass',[
            'list'=>null,   
        ]);
    }

    public function Password()
    {
        return $this->fetch('songshuiyuan/password',[
            'list'=>null,   
        ]);
    }


    public function bandPhone()
    {
        return $this->fetch('songshuiyuan/phone',[
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
        return $this->fetch('songshuiyuan/head',[
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






}
