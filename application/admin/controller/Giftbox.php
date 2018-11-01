<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\GiftBox as GiftBoxModel;
class Giftbox extends Base
{
    /**
     * 礼盒专区列表
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function index()
    {	
    	parent::CheckAdminLogin();
    	$data = GiftBoxModel::GetAll();
    	return $this->fetch('Giftbox/index',[
    		'list'=>$data,
    	]);
    }
    /**
     * 显示添加
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     */
    public function add()
    {
    	return $this->fetch('Giftbox/add');
    }
    /**
     * 执行添加
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     */
    public function DoAdd()
    {
        
        if (request()->file('box_img')) {
            $data = parent::UploadImg('box_img');
        }else{
            echo json_encode(["code"=>1,"meg"=>"您还没有选择图片"]);
        }
        $data['box_name'] = $_POST['box_name'];
        $data['box_pic'] = $_POST['box_pic'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $result = GiftBoxModel::AddData($data);
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
        $data = GiftBoxModel::GetOne($id);
        return $this->fetch('Giftbox/update',[
            'update'=>$data,
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
        if (request()->file('box_img')) {
            $data = parent::UploadImg('box_img');
        }
        $data['box_name'] = $_POST['box_name'];
        $data['box_pic'] = $_POST['box_pic'];
        $data['update_time'] = time();
        $result = GiftBoxModel::UpdateData($id,$data);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
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
        $id = $_GET['id'];
        $result = GiftBoxModel::DeleteData($id);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
    }
}
