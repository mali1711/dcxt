<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\Category as CategoryModel;
/**
* 商品分类
*/
class Category extends Base
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
		$data = CategoryModel::GetAll();
		return $this->fetch('Category/index',[
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
		return $this->fetch('Category/add');
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
		$data['is_type'] = 1;
		 // if (request()->file('img')) {
   //          $data['img'] = parent::UploadTirImg('img');
   //      }else{
   //          echo json_encode(["code"=>2,"meg"=>"请选择要上传的图片"]);   
   //          die;
   //      }
   		// var_dump($data);die();
		$data['create_time'] = time();
		$data['update_time'] = time();
		$result = CategoryModel::AddData($data);
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
		$data = CategoryModel::GetOne($id);
		return $this->fetch('Category/update',[
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
		// $data['is_type'] = 1;
		// if (request()->file('img')) {
  //           $data['img'] = parent::UploadTirImg('img');
  //       }
		$data['update_time'] = time();
		$result = CategoryModel::UpdateData($id,$data);
		
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
        $result = CategoryModel::DeleteData($id);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
	}

	public function order()
	{
		$id = $_GET['id'];
		$order = $_GET['order'];
		$data = [];
		$data['order'] = $order;
        $result = CategoryModel::UpdateData($id,$data);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
	}


}