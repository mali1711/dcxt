<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\Interactive as InteractiveModel;
class Interactive extends Base
{
	/**
	 * 展示信息
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-04
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
    public function index()
    {	
    	parent::CheckAdminLogin();
    	$data = Db::table('water_interactive')
                    ->alias('i')
                    ->join('admin_users as','as.id = i.uid')
                    ->field('i.*,as.nickname')
                    ->paginate(15);
    	return $this->fetch('Interactive/index',[
    		'list'=>$data,
    	]);
    }
    /**
     * 展示添加
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     */
    public function add()
    {
    	return $this->fetch('Interactive/add');
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
        if (request()->file('img')) {
            $data['img'] = parent::UploadTirImg('img');
        }else{
            echo json_encode(["code"=>2,"meg"=>"请选择要上传的图片"]);   
            die;
        }
        $data['uid'] = cookie('AdminUserId');
        $data['create_time'] = time();
        $data['update_time'] = time();
        $result = InteractiveModel::AddData($data);
        echo $result;
    }


    /**
     * 显示修改页面
     * @Author   CarLos(wang)
     * @DateTime 2018-06-05
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function update($id)
    {
        $update = InteractiveModel::GetOne($id);
        return $this->fetch('Interactive/update',[
            'update'=>$update,
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
        if (request()->file('img')) {
            $data['img'] = parent::UploadTirImg('img');
        }
        $data['update_time'] = time();
        $result = InteractiveModel::UpdateData($id,$data);
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
        $result = InteractiveModel::DeleteData($id);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"删除成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"删除失败"]);
        }
    }
    /**
     * 显示查看
     * @Author   CarLos(wang)
     * @DateTime 2018-06-05
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function show($id)
    {
        $update = InteractiveModel::GetOne($id);
        return $this->fetch('Interactive/show',[
            'update'=>$update,
        ]);
    }
}
