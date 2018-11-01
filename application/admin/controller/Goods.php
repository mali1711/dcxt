<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Category as CategoryModel;
class Goods extends Base
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
        // $data = Db::table('water_goods')
        //             ->alias('g')
        //             ->join('Category c','c.id = g.cid')
        //             ->order('g.status desc','g.id desc')
        //             // ->where('g.is_type=1')
        //             ->field('g.*,c.name')
        //             ->paginate(15);
        $data = GoodsModel::GetAll();
        $yunfei = Db::name('config')->where('id',1)->value('freight');
        $this->assign('yunfei',$yunfei);
    	return $this->fetch('Goods/index',[
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
        $data = CategoryModel::GetDataAll();
    	return $this->fetch('Goods/add',[
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
        $data = CategoryModel::GetDataAll();
        $update = GoodsModel::GetOne($id);
        return $this->fetch('Goods/update',[
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

        $data['update_time'] = time();
        $result = GoodsModel::UpdateData($id,$data);
        
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
        $data = CategoryModel::GetDataAll();
        $update = GoodsModel::GetOne($id);
        return $this->fetch('Goods/show',[
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
        $result = GoodsModel::UpdateData($id,$data,1);
        echo $result;
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
        $result = GoodsModel::UpdateData($id,$data,1);
        echo $result;
    }
}
