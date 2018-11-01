<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Region as RegionModel;
use app\admin\model\Address as AddressModel;
/**
* 商品分类
*/
class Region extends Base
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

		$list = RegionModel::GetAll();
		return $this->fetch('Region/index',[
			'list'=>$list,

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
		$list = AddressModel::GetAllAddress();

		return $this->fetch('Region/add',['list'=>$list]);
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
		$result = RegionModel::AddData($data);
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
		$list = AddressModel::where('status', 1)->order('id', 'asc')->select();
		$data = RegionModel::GetOne($id);
		return $this->fetch('Region/update',[
			'list'   => $list,
			'update' => $data,
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

		$result = RegionModel::UpdateData($id,$data);
		
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
        $result = RegionModel::DeleteData($id);
        echo $result;
	}

}