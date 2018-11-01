<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Search as SearchModel;
/**
* 热搜分类
*/
class Search extends Base
{
	/**
	 * 热搜展示页面
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function index()
	{
		parent::CheckAdminLogin();
		$data = SearchModel::GetAll();
		return $this->fetch('Search/index',[
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
		return $this->fetch('Search/add');
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
		$result = SearchModel::AddData($data);
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
		$data = SearchModel::GetOne($id);
		return $this->fetch('Search/update',[
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

		$result = SearchModel::UpdateData($id,$data);
		
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
        $result = SearchModel::DeleteData($id);
        return $result;
	}



}