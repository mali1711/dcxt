<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;
use app\admin\model\Order as OrderModel;
class Order extends Base
{
	/**
	 * 订单列表
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-06
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
    public function index($deliverType)
    {	
    	parent::CheckAdminLogin();

        $type = 1;
        $orderStatus = 0;
        $orderShowStatus = 0;
    	$data = OrderModel::GetAll($type,$orderStatus,$orderShowStatus,$deliverType);
    	return $this->fetch('Order/index',[
    		'list'=>$data,
    	]);
    }

    /**
     * 退款订单列表
     * @Author   CarLos(wang)
     * @DateTime 2018-06-06
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function orderTui()
    {   
        parent::CheckAdminLogin();
        $deliverType = 0;
        $type = 1;
        $orderStatus = 5;
        $orderShowStatus = 0;
        $data = OrderModel::GetAll($type, $orderStatus,$orderShowStatus,$deliverType);
        return $this->fetch('Order/orderTui',[
            'list'=>$data,
        ]);
    }

    /**
     * 退款订单列表
     * @Author   CarLos(wang)
     * @DateTime 2018-06-06
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function OrderShouHou()
    {   
        parent::CheckAdminLogin();
        $deliverType = 0;
        $type            = 1;
        $orderShowStatus = 1;
        $orderStatus     = 0;//5; 
        $data = OrderModel::GetAll($type, $orderStatus,$orderShowStatus,$deliverType);
        return $this->fetch('Order/orderShowHou',[
            'list'=>$data,
        ]);
    }
    
    /**
     * 押桶订单列表
     * @Author   CarLos(wang)
     * @DateTime 2018-06-06
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function bucketList()
    {   
        parent::CheckAdminLogin();
        $deliverType = 0;
        $type = 2;
        $orderStatus = 0;
        $orderShowStatus = 0;
        $data = OrderModel::GetAll($type,$orderStatus,$orderShowStatus,$deliverType);
        return $this->fetch('Order/bucketList',[
            'list'=>$data,
        ]);
    }

    /**
     * 退桶订单列表
     * @Author   CarLos(wang)
     * @DateTime 2018-06-06
     * @Email    carlos0608@163.com
     * @return   [type]             [description]
     */
    public function bucketListTui()
    {   
        parent::CheckAdminLogin();
        $deliverType = 0;
        $type = 3;
        $orderStatus = 0;
        $orderShowStatus = 0;
        $data = OrderModel::GetAll($type,$orderStatus,$orderShowStatus,$deliverType);
        return $this->fetch('Order/bucketListTui',[
            'list'=>$data,
        ]);
    }

    public function getInfo()
    {
        $info = OrderModel::GetOne($_GET['id']);
        return $this->fetch('Order/action',[
            'info'=>$info,
        ]);
    }

    public function getShouHouInfo()
    {
        $info = OrderModel::GetOne($_GET['id']);
        return $this->fetch('Order/shouAction',[
            'info'=>$info,
        ]);
    }

    public function getBucketInfo()
    {
        $info = OrderModel::GetOne($_GET['id']);
        return $this->fetch('Order/bucketAction',[
            'info'=>$info,
        ]);
    }

    public function orderTuiKuan()
    {
        
        if (!isset($_POST['type']) || !$_POST['type']) {
            return json_encode(["code"=>1,"meg"=>"操作失败，参数错误"]);
        }

        if (!isset($_POST['orderId']) || !$_POST['orderId']) {
            return json_encode(["code"=>1,"meg"=>"操作失败，参数错误"]);
        }

        $type = $_POST['type'];
        $orderId = $_POST['orderId'];
        
        $result = OrderModel::orderTuiKuan($type, $orderId);

        return $result;
    }

    public function completeOrderZiQu()
    {
        
        if (!isset($_POST['userId']) || !$_POST['userId']) {
            return json_encode(["code"=>1,"meg"=>"操作失败，参数错误"]);
        }

        if (!isset($_POST['orderId']) || !$_POST['orderId']) {
            return json_encode(["code"=>1,"meg"=>"操作失败，参数错误"]);
        }

        $userId  = $_POST['userId'];
        $orderId = $_POST['orderId'];
        
        $result = OrderModel::completeOrderDoZiQu($userId, $orderId);

        return json_encode(["code"=>0,"meg"=>"操作成功"]);
    }

    public function doShouHou()
    {
        if (!isset($_POST['orderId']) || !$_POST['orderId']) {
            return json_encode(["code"=>1,"meg"=>"操作失败，参数错误"]);
        }

        $orderId = $_POST['orderId'];
        
        $result = Db::name('order')->where('id',$orderId)->update(['orderShowStatus'=>2]);

        if($result){
            return json_encode(["code"=>0,"meg"=>"操作成功"]);
        }
        return json_encode(["code"=>1,"meg"=>"操作失败，参数错误"]);
    }

    public function orderTuiTong()
    {

        if (!isset($_POST['orderId']) || !$_POST['orderId']) {
            return json_encode(["code"=>1,"meg"=>"操作失败，参数错误"]);
        }

        $orderId = $_POST['orderId'];
        
        $result = OrderModel::orderTuiTong($orderId);

        return $result;
    }


} 
