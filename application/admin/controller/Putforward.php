<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Putforward as PutForwardModel;
/**
* 商品分类
*/
class Putforward extends Base
{
	/**
	 * 提现管理展示页面
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function index()
	{
		parent::CheckAdminLogin();

		$where = ['u.identity' => 1];

		$data = PutForwardModel::GetAll($where);
		return $this->fetch('PutForward/index',[
			'list'=>$data,
		]);
	}

	/**
	 * 提现管理展示页面
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function shangTixain()
	{
		parent::CheckAdminLogin();

		$where = ['u.identity' => 2];

		$data = PutForwardModel::GetAll($where);
		return $this->fetch('PutForward/index',[
			'list'=>$data,
		]);
	}

	


	/**
	 * 显示修改页面
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 * @param    [type]             $id [description]
	 * @return   [type]                 [description]
	 */
	public function action($id)
	{
		$data = PutForwardModel::GetOne($id);
		return $this->fetch('PutForward/action',[
			'update'=>$data,
		]);
	}




	/**
	 * 执行修改
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 */
	public function doAction($id)
	{

		$id = $_GET['id'];	
		$putStatus = $_GET['putStatus'];

		$result = PutForwardModel::doAction($id,$putStatus);
		
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
		$id = $_GET['id'];
        $result = PutForwardModel::DeleteData($id);
        echo $result;
	}
}