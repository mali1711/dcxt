<?php
namespace app\admin\controller;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\AdminUsers as AdminUsersModel;
use app\admin\model\Column as ColumnModel;
/**
* 管理员
*/
class Administrator extends Base
{
	//管理员列表
	public function index()
	{
		parent::CheckAdminLogin();
		$data = AdminUsersModel::GetAll();
		return $this->fetch('Administrator/index',[
			'list'=>$data,
		]);
	}

	/**
	 * 管理员添加页面
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 */
	public function add()
	{
		$data = ColumnModel::GetAll();
		return $this->fetch('Administrator/add',[
			'list'=>$data,
		]);
	}

	public function DoAdd()
	{
		$data = $_POST;
		$data['create_time'] = time();
		$data['update_time'] = time();
		$result = AdminUsersModel::AddData($data);
		echo $result;
	}
	/**
	 * 显示修改
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function update($id)
	{
		$data = AdminUsersModel::GetOne($id);
		$list = ColumnModel::GetAll();
		return $this->fetch('Administrator/update',[
			'update'=>$data,
			'list'=>$list,
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
		$data['update_time'] = time();
		$result = AdminUsersModel::UpdateData($id,$data);
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
        $result = AdminUsersModel::DeleteData($id);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
	}
	/**
	 * 显示修改密码页面
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function password()
	{
		return $this->fetch('Administrator/password');
	}

	public function DoPassword()
	{
		$id = cookie('AdminUserId');
		$data['password'] = md5($_POST['password']);
		$result = AdminUsersModel::UpdateData($id,$data);
		if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
	}
	
}