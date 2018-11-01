<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\Help as HelpModel;
class Help extends Base
{
	/**
	 * 帮助中心显示
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
    public function index($type)
    {	
    	parent::CheckAdminLogin();
    	$data = HelpModel::GetAll($type);
    	return $this->fetch('Help/index',[
    		'list'=>$data,'type'=>$type
    	]);
    }
    /**
     * 显示添加
     * @Author   CarLos(wang)
     * @DateTime 2018-06-05
     * @Email    carlos0608@163.com
     */
    public function add($type)
    {   

    	return $this->fetch('Help/add',['type'=>$type]);
    }
    /**
     * 执行添加
     * @Author   CarLos(wang)
     * @DateTime 2018-06-05
     * @Email    carlos0608@163.com
     */
    public function DoAdd()
    {
        $data = $_POST;
        $data['create_time'] = time();
        $data['update_time'] = time();

        $result = HelpModel::AddData($data);
        echo $result;
    }
    /**
     * 显示修改
     * @Author   CarLos(wang)
     * @DateTime 2018-06-05
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function update($id)
    {
        $data = HelpModel::GetOne($id);
    	return $this->fetch('Help/update',[
            'update'=>$data,
        ]);
    }
    /**
     * 执行修改
     * @Author   CarLos(wang)
     * @DateTime 2018-06-05
     * @Email    carlos0608@163.com
     */
    public function DoUpdate()
    {
        $id = $_GET['id'];
        $data = $_POST;
        $data['update_time'] = time();
        $result = HelpModel::UpdateData($id,$data);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
    }
    /**
     * 执行删除
     * @Author   CarLos(wang)
     * @DateTime 2018-06-05
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function delete()
    {
        $id = $_GET['id'];
        $result = HelpModel::DeleteData($id);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
    }
}
