<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\Column as ColumnModel;
class Column extends Base
{
	/**
	 * 显示列表页
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-01
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
    public function index()
    {
        parent::CheckAdminLogin();
        $data = ColumnModel::GetAll();
        $tree = tree($data,$pid=0,$lev=1);
    	return $this->fetch('Column/index',[
            'list'=>$tree,//事例
        ]);
        
    }
    /**
     * 显示添加页面
     * @Author   CarLos(wang)
     * @DateTime 2018-06-01
     * @Email    carlos0608@163.com
     */
    public function add()
    {
        $data = ColumnModel::GetAll();
        $tree = tree($data,$pid=0,$lev=1);
    	return $this->fetch('Column/add',[
            'list'=>$tree,
        ]);
    }
    /**
     * 执行添加
     * @Author   CarLos(wang)
     * @DateTime 2018-06-01
     * @Email    carlos0608@163.com
     */
    public function DoAdd()
    {
        $data = $_POST;
        $data['create_time'] = time();
        $data['update_time'] = time();
        $result = ColumnModel::AddData($data);
        echo $result;
    }
    /**
     * 显示修改页面
     * @Author   CarLos(wang)
     * @DateTime 2018-06-01
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function update($id)
    {
        $data = ColumnModel::GetAll();
        $tree = tree($data,$pid=0,$lev=1);
        $dataOne = ColumnModel::GetOne($id);
    	return $this->fetch('Column/update',[
            'list'=>$tree,
            'update'=>$dataOne,
        ]);
    }
    /**
     * 执行修改
     * @Author   CarLos(wang)
     * @DateTime 2018-06-01
     * @Email    carlos0608@163.com
     */
    public function DoUpdate()
    {
        $id = $_GET['id'];
        $data = $_POST;
        $data['create_time'] = time();
        $data['update_time'] = time();
        $result = ColumnModel::UpdateData($id,$data);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
    }

    public function delete()
    {
        $id = $_GET['id'];
        $result = ColumnModel::DeleteData($id);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
    }
}
