<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\Config as ConfigModel;
class Config extends Base
{
	/**
	 * 系统配置
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-06
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
    public function index()
    {	
    	parent::CheckAdminLogin();
    	$data = ConfigModel::GetConAll();
    	return $this->fetch('Config/index',[
    		'update'=>$data
    	]);
    }

    /**
     * 关于我们
     * @Author   CarLos(wang)
     * @DateTime 2018-06-06
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function guanyu()
    {   
        parent::CheckAdminLogin();
        $data = ConfigModel::GetConAll();
        return $this->fetch('Config/guanyu',[
            'update'=>$data
        ]);
    }

    /**
     * 平台客服
     * @Author   CarLos(wang)
     * @DateTime 2018-06-06
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function kefu()
    {   
        parent::CheckAdminLogin();
        $data = ConfigModel::GetConAll();
        return $this->fetch('Config/kefu',[
            'update'=>$data
        ]);
    }

    /**
     * 执行修改
     * @Author   CarLos(wang)
     * @DateTime 2018-06-06
     * @Email    carlos0608@163.com
     */
    public function DoUpdate()
    {
        $data = $_POST;

        $result = ConfigModel::UpdateData($data);
        if ($result) {
            echo json_encode(["code"=>1,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>0,"meg"=>"操作失败"]);
        }
    }

} 
