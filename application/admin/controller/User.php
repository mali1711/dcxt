<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\User as UserModel;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Salary as SalaryModel;
use app\admin\model\Profit as ProfitModel;
use app\admin\model\Region as RegionModel;
use app\service\WechatQrcode;

class User extends Base
{
    /**
     * 商品展示
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function index($id)
    {	
    	parent::CheckAdminLogin();
    	$data = UserModel::GetAll($id);

        $u = '';

        switch ($id) {
                case '1':
                    $u = 'User/index';
                    break;
                case '2':
                    $u = 'User/Shang_index';
                    break; 
                case '3':
                    $u = 'User/Song_index';
                    break; 
            }

        
    	return $this->fetch($u,[
    		'list'=>$data,
    	]);
        


    }

    // 送水员薪资记录
        public function xinzi($id)
    {   
        parent::CheckAdminLogin();
        $data = SalaryModel::GetAll($id);
        return $this->fetch('User/xinzi',[
            'list'=>$data,
        ]);
    }

    // 送水员薪资记录
        public function profit($id)
    {   
        parent::CheckAdminLogin();
        $data = ProfitModel::GetAll($id);
        return $this->fetch('User/profit',[
            'list'=>$data,
        ]);
    }



    /**
     * 商品添加页面
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     */
    public function add($id)
    {
        $u = '';
        $list = [];
        switch ($id) {
                case '1':
                    $u = 'User/Shang_add';
                    break;
                case '2':
                    $u    = 'User/Song_add';
                    $list = RegionModel::getAll();
                    break; 
            }
    	return $this->fetch($u, ['list'=>$list]);
    }
    /**
     * 上传连图
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     */
    public function AddGoodsImg()
    {
        if (request()->file('file')) {
            $data = parent::UploadTirImg('file');
            echo $data;
        }else{
            echo json_encode(["code"=>1,"meg"=>"您还没有选择图片"]);
        }
    }
    /**
     * 执行添加
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     */
    public function DoAdd()
    {
        $data = $_POST;
        $data['create_time'] = time();
        $data['update_time'] = time();
        $result = UserModel::AddData($data);
        echo $result;
    }
    /**
     * 显示修改
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function update($id)
    {
        $update = UserModel::GetOne($id);
        
        $u = '';
        $list = [];
        switch ($update['type']) {
                case '1':
                    $u = 'User/update';
                    break;
                case '2':
                    $u = 'User/Shang_update';
                    break; 
                case '3':
                    $u = 'User/Song_update';
                    $list = RegionModel::getAll();
                    break; 
            }
        return $this->fetch($u,[
            'update'=>$update,'list'=>$list
        ]);
    }

    /**
     * 执行修改
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     */
    public function DoUpdate()
    {
        $id = $_GET['id'];
        $data = $_POST;
        if (request()->file('photo')) {
            $data['photo'] = parent::UploadTirImg('photo');
        }

        $data['update_time'] = time();
        // dump($data);dump($id);die;
        $result = UserModel::UpdateData($id,$data);
        
        echo $result;
    }

    /**
     * 显示重置支付密码
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function update_pay($id)
    {   

        $update = UserModel::GetOne($id);
        return $this->fetch('User/update_pay',[
            'update'=>$update,
        ]);
    }

    /**
     * 执行重置支付密码
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function Do_Update_pay($id)
    {
        $id = $_GET['id'];
        $data = $_POST;

        $result = UserModel::do_updata_pay($id,$data);
        
        echo $result;
    }

    /**
     * 显示重置密码
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function update_pwd($id)
    {
        $update = UserModel::GetOne($id);
        return $this->fetch('User/update_pwd',[
            'update'=>$update,
        ]);
    }

    /**
     * 执行重置密码
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function Do_Update_pwd()
    {

        $id = $_GET['id'];
        $data = $_POST;

        $result = UserModel::do_updata_pwd($id,$data);
        
        echo $result;
    }


    /**
     * 执行删除
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function delete()
    {
        // dump($_GET);die;
        $id     = $_GET['id'];
        $status = $_GET['status'];
        
        if ($status == 1) {
            $data['status'] = 0;
        } else {
             $data['status'] = 1;
        }

        $result = UserModel::UpdateData($id,$data);

        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
    }
    /**
     * 展示查看
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function show($id)
    {
        // $id = $_GET;
        
        $update = UserModel::GetOne($id);
        return $this->fetch('User/show',[
            'update'=>$update,
        ]); 
    }

    /**
     * 商家展示查看
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function Shang_show($id)
    {
        // $id = $_GET;
        
        $update = UserModel::GetOne($id);
        return $this->fetch('User/Shang_show',[
            'update'=>$update,
        ]); 
    }

    /**
     * 送水员展示查看
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function song_show($id)
    {
        // $id = $_GET;
        
        $update = UserModel::GetOne($id);
        return $this->fetch('User/Song_show',[
            'update'=>$update,
        ]); 
    }

    /**
     * 商家展示信息
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function info_show($id)
    {
       
        $update = UserModel::GetInfoOne($id);

        $title  = UserModel::where('id',$id)->value('nickname');
        $status = 1;
        if (!$update) {
            $status = 1;
        }

        return $this->fetch('User/info_show',['update'=>$update,'title' => $title,'status' => $status, 'uid' => $id]); 
    }

    /**
     * 修改商家展示信息
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function update_info_show($id)
    {
        $id = $_GET['id'];

        $data = $_POST;
       
        // dump($data);
        
        if (request()->file('file')) {
            $data = parent::UploadTirImg('file');
            echo $data;
        }
        if (request()->file('pic')) {
            $data['pic'] = parent::UploadTirImg('pic');
        }

      
        $result = UserModel::update_info_show($id,$data);
        
        echo $result;
        
    }



    // 分配片区
    public function pianqu($id) 
    {
        $list   = RegionModel::where('status',1)->order('id', 'asc')->select();

        $update = UserModel::GetOne($id);

        return $this->fetch('User/pianqu',[
            'list'=>$list,'update'=>$update
        ]); 
    }

    // 执行分配片区
    public function Dopianqu()
    {
        $id = $_GET['id'];
        $data = $_POST;

        $result = UserModel::UpdateData($id,$data);
        
        echo $result;
    }


    public function getWchatQrcode()
    {
        $id = $_GET['id'];

        // 生成二维码 返回二维码路径
        $WechatQrcode = new WechatQrcode();

        $url = 'https://jijikeji.cn/api/v1/GetShangUserInfo?id='.$id;

        $user_id = $id;

        $Qrcode = WechatQrcode::getWchatQrcode($url,$user_id,$data=[],$level=3,$size=4);

        $data = [];

        $data['Qrcode'] = '/'.$Qrcode;

        $result = UserModel::UpdateData($id,$data);

        if ($result) {
            return ['code'=>1001,'meg'=>'生成专属二维码成功','path'=>$Qrcode]; 
        }
        return ['code'=>1025,'meg'=>'生成专属二维码失败','path'=>null];  
    }

    public function setFlag()
    {

        $id   = $_GET['id'];

        $flag = $_GET['flag'];

        if ($flag == 1) {
            $data['flag'] = 0;
        } else {
             $data['flag'] = 1;
        }

        $result = Db::name('info_show')->where('uid',$id)->find();

        $data['uid'] = $id;

        if (!$result) {
            return ["code"=>1025,"meg"=>"操作失败,该商家展示信息不存在"];
        }


        $res = Db::name('info_show')->where('uid',$id)->update($data);

        if ($res) {
            return ["code"=>1001,"meg"=>"操作成功"];
        }else{
            return ["code"=>1025,"meg"=>"操作失败"];
        }

    }
}