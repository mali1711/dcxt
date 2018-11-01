<?php
namespace app\admin\controller;
use app\admin\controller\Base;
/**
* 登录
*/
use think\Db;
class Login extends Base
{
	/**
	 * 显示登录页面
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function login()
	{

		return $this->fetch("Login/login");
	}
	/**
	 * 执行登录
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 */
	public function DoLogin()
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		if (empty($username)) {
			echo json_encode(["code"=>1,"meg"=>"用户名不能为空"]);
			die;
		}
		if (empty($password)) {
			echo json_encode(["code"=>1,"meg"=>"请填写密码"]);
			die;
		}
		$res = Db::name('admin_users')->where(["username"=>$username])->find();
		if ($res['status'] != 1) {
			echo json_encode(["code"=>1,"meg"=>"很抱歉您的账号已经被停用了"]);
		}else{
			if ($res['password'] != md5($password)) {
				echo json_encode(["code"=>1,"meg"=>"密码不正确"]);
				die;
			}else{
				cookie('AdminUserId',$res['id']);
 
				echo json_encode(["code"=>0,"meg"=>"登录成功"]);
			}
		}
	}

	public function LoginOut()
	{
		cookie('AdminUserId',null);
		$this->redirect('/Admin/Login');
	}
}