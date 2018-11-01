<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\Banner as BannerModel;
class Banner extends Base
{
	/**
	 * 轮播显示列表
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-06
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
    public function index()
    {	
    	parent::CheckAdminLogin();
    	$data = BannerModel::GetAll();
    	return $this->fetch('Banner/index',[
    		'list'=>$data,
    	]);
    }
    /**
     * 添加轮播的页面
     * @Author   CarLos(wang)
     * @DateTime 2018-06-06
     * @Email    carlos0608@163.com
     */
    public function add()
    {
    	return $this->fetch('Banner/add');
    }
    /**
     * 执行添加
     * @Author   CarLos(wang)
     * @DateTime 2018-06-06
     * @Email    carlos0608@163.com
     */
    public function DoAdd()
    {
    	$data = $_POST;
    	if (request()->file('banner_img')) {
            $data['banner_img'] = parent::UploadTirImg('banner_img');
        }else{
        	echo json_encode(["code"=>2,"meg"=>"请选择要上传的图片"]);	
        	die;
        }
        $data['create_time'] = time();
        $data['update_time'] = time();
        $result = BannerModel::AddData($data);
        echo $result;
    }
    /**
     * 修改轮播的界面
     * @Author   CarLos(wang)
     * @DateTime 2018-06-06
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function update($id)
    {       
        $data = BannerModel::GetOne($id);
    	return $this->fetch('Banner/update',[
            'update'=>$data,
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
        $id = $_GET['id'];
        $data = $_POST;
        if (request()->file('banner_img')) {
            $data['banner_img'] = parent::UploadTirImg('banner_img');
        }
        $data['update_time'] = time();
        $result = BannerModel::UpdateData($id,$data);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
    }

    public function delete()
    {
        $id = $_GET['id'];
        $result = BannerModel::DeleteData($id);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"删除成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"删除失败"]);
        }
    }
} 
