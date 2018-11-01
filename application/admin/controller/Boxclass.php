<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\Classification as ClassificationModel;
class Boxclass extends Base
{
    /**
     * 礼盒分类列表
     * @Author   CarLos(wang)
     * @DateTime 2018-06-15
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function index()
    {	
    	parent::CheckAdminLogin();
    	$data = ClassificationModel::GetBoxAll();
    	return $this->fetch('Boxclass/index',[
    		'list'=>$data,
    	]);
    }
    /**
     * 礼盒列表添加展示
     * @Author   CarLos(wang)
     * @DateTime 2018-06-15
     * @Email    carlos0608@163.com
     */
    public function add()
    {
    	return $this->fetch('Boxclass/add');
    }
    /**
     * 执行添加
     * @Author   CarLos(wang)
     * @DateTime 2018-06-15
     * @Email    carlos0608@163.com
     */
    public function DoAdd()
    {
        $data = $_POST;
        if (request()->file('img')) {
            $data['img'] = parent::UploadTirImg('img');
        }else{
            echo json_encode(["code"=>2,"meg"=>"请选择要上传的图片"]);   
            die;
        }
        $data['is_type'] = 0;
        $data['create_time'] = time();
        $data['update_time'] = time();
        $result = ClassificationModel::AddData($data);
        echo  $result;
    }
    /**
     * 显示修改
     * @Author   CarLos(wang)
     * @DateTime 2018-06-19
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function update($id)
    {
        $data = ClassificationModel::GetOne($id);
        return $this->fetch('Boxclass/update',[
            'update'=>$data,
        ]);
    }
    /**
     * 执行修改
     * @Author   CarLos(wang)
     * @DateTime 2018-06-19
     * @Email    carlos0608@163.com
     */
    public function DoUpdate()
    {
        $id = $_GET['id'];
        $data = $_POST;
        if (request()->file('img')) {
            $data['img'] = parent::UploadTirImg('img');
        }
        $data['update_time'] = time();
        $result = ClassificationModel::UpdateData($id,$data);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
    }
    /**
     * 执行删除
     * @Author   CarLos(wang)
     * @DateTime 2018-06-19
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function delete()
    {
        $id = $_GET['id'];
        $result = ClassificationModel::DeleteData($id);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"删除成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"删除失败"]);
        }
    }
}
