<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Address as AddressModel;
/**
* 商品分类
*/
class Address extends Base
{
	/**
	 * 商品分类展示页面
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function index()
	{
		parent::CheckAdminLogin();
		$data = AddressModel::GetAll();
		return $this->fetch('Address/index',[
			'list'=>$data,
		]);
	}
	/**
	 * 显示添加页面
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 */
	public function add()
	{
		return $this->fetch('Address/add');
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
		$data['createTime'] = time();
		$result = AddressModel::AddData($data);
		// dump($result);die();
		echo $result;
	}
	/**
	 * 显示修改页面
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 * @param    [type]             $id [description]
	 * @return   [type]                 [description]
	 */
	public function update($id)
	{
		$data = AddressModel::GetOne($id);
		return $this->fetch('Address/update',[
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
		$data = $_POST;

		$result = AddressModel::UpdateData($id,$data);
		
		return $result;
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
        $result = AddressModel::DeleteData($id);
        return $result;
	}



}