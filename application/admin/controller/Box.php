<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Classification as ClassificationModel;
class Box extends Base
{
    /**
     * 商品展示
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function index()
    {	
    	parent::CheckAdminLogin();
    	// $data = GoodsModel::GetAll();
        $data = Db::table('water_goods')
                    ->alias('g')
                    ->join('classification c','c.id = g.cid')
                    ->order('water_goods.id','desc')
                    ->where('g.is_type=0')
                    ->field('g.*,c.name')
                    ->paginate(15);
    	return $this->fetch('Box/index',[
    		'list'=>$data,
    	]);
    }
    /**
     * 商品添加页面
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     */
    public function add()
    {
        $data = ClassificationModel::GetBoxDataAll();
    	return $this->fetch('Box/add',[
            'list'=>$data,
        ]);
    }
    /**
     * 上传连图
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     */
    public function AddGoodsImg()
    {
        if (request()->file('file')) {
            $data = parent::UploadTirImg('file');
            echo $data;
        }else{
            echo json_encode(["code"=>1,"meg"=>"您还没有选择图片"]);
        }
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
        if (request()->file('thumb_img')) {
            $data['thumb_img'] = parent::UploadTirImg('thumb_img');
        }else{
            echo json_encode(["code"=>2,"meg"=>"请选择要上传的图片"]);   
            die;
        }
        $data['is_type'] = 0;
        $data['create_time'] = time();
        $data['update_time'] = time();
        $result = GoodsModel::AddData($data);
        echo $result;
    }
    /**
     * 显示修改
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function update($id)
    {
        $data = ClassificationModel::GetBoxDataAll();
        $update = GoodsModel::GetOne($id);
        return $this->fetch('Box/update',[
            'list'=>$data,
            'update'=>$update,
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
        if (request()->file('file')) {
            $data = parent::UploadTirImg('file');
            echo $data;
        }
        if (request()->file('thumb_img')) {
            $data['thumb_img'] = parent::UploadTirImg('thumb_img');
        }
        $data['is_type'] = 0;
        $data['update_time'] = time();
        $result = GoodsModel::UpdateData($id,$data);
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
        $result = GoodsModel::DeleteData($id);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
    }
    /**
     * 展示查看
     * @Author   CarLos(wang)
     * @DateTime 2018-06-04
     * @Email    carlos0608@163.com
     * @param    [type]             $id [description]
     * @return   [type]                 [description]
     */
    public function show($id)
    {
        $data = ClassificationModel::GetDataAll();;
        $update = GoodsModel::GetOne($id);
        return $this->fetch('Box/show',[
            'list'=>$data,
            'update'=>$update,
        ]); 
    }
    /**
     * 推荐
     * @Author   CarLos(wang)
     * @DateTime 2018-06-05
     * @Email    carlos0608@163.com
     */
    public function Recommed()
    {
        $id = $_GET['id'];
        $data['is_recom'] = 1;
        $data['recommend_time'] = time();
        $result = GoodsModel::UpdateData($id,$data);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
    }

    /**
     * 取消推荐
     * @Author   CarLos(wang)
     * @DateTime 2018-06-05
     * @Email    carlos0608@163.com
     */
    public function UnRecommed()
    {
        $id = $_GET['id'];
        $data['is_recom'] = 0;
        $data['recommend_time'] = '';
        $result = GoodsModel::UpdateData($id,$data);
        if ($result) {
            echo json_encode(["code"=>0,"meg"=>"操作成功"]);
        }else{
            echo json_encode(["code"=>1,"meg"=>"操作失败"]);
        }
    }
}
