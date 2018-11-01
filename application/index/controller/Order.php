<?php
namespace app\index\controller;
use think\Loader;
use think\Db;
use app\index\controller\Base;
use app\admin\model\Order as OrderModel;
class Order extends Base
{

    public function index()
    {
    	// parent::Check();
    	// parent::CheckAdminLogin();
		// $data = AddressModel::GetAll();
		return $this->fetch('songshuiyuan/index',[
			'list'=>null,	
		]);
    }

    public function OrderList()
    {
        

        $uid = session('uid');

        $status = Db::name('user')->where('id', $uid)->value('status');
        if (!$status) {
            return $this->fetch('songshuiyuan/index',[
            'list'=>null]);
        }


        $type = 'orderStatus = 1';

        $cate = 1;

        //代发货
        $list = OrderModel::getList($uid,$type,$cate);

        $type = 'orderStatus = 2';
        // 已发货
        $Yfhlist = OrderModel::getList($uid,$type,$cate);

        $type = 'orderStatus = 3';
        // 已送达
        $Ysdlist = OrderModel::getList($uid,$type,$cate);

        $type = 'orderStatus = 4';
        // 已完成
        $Ywclist = OrderModel::getList($uid,$type,$cate);

        $type = 'orderStatus = 5';
        // 已取消
        $Yqxlist = OrderModel::getList($uid,$type,$cate);

        $this->assign('Yfhlist', $Yfhlist);

        $this->assign('Ysdlist', $Ysdlist);

        $this->assign('Ywclist', $Ywclist);

        $this->assign('Yqxlist', $Yqxlist);
		
        return $this->fetch('songshuiyuan/s_order',[
			'list'=>$list,	
		]);
    }


    public function TOrderList()
    {

        $uid = session('uid');
       
        $status = Db::name('user')->where('id', $uid)->value('status');
        if (!$status) {
            return $this->fetch('songshuiyuan/index',[
            'list'=>null]);
        }


        $type = 'orderStatus = 1';

        $cate = 3;

        //代发货
        $list = OrderModel::getList($uid,$type,$cate);

        $type = 'orderStatus = 2';
        // 已发货
        $Yfhlist = OrderModel::getList($uid,$type,$cate);

        $type = 'orderStatus = 3';
        // 已送达
        $Ysdlist = OrderModel::getList($uid,$type,$cate);

        $type = 'orderStatus = 4';
        // 已完成
        $Ywclist = OrderModel::getList($uid,$type,$cate);

        $type = 'orderStatus = 5';
        // 已取消
        $Yqxlist = OrderModel::getList($uid,$type,$cate);

        $this->assign('Yfhlist', $Yfhlist);

        $this->assign('Ysdlist', $Ysdlist);

        $this->assign('Ywclist', $Ywclist);

        $this->assign('Yqxlist', $Yqxlist);

		return $this->fetch('songshuiyuan/t_order',[
			'list'=>$list,	
		]);
    }

    public function SOrderList()
    {

        $uid = session('uid');

        $cate = 1;

        $type = 'orderStatus = 2 Or orderStatus = 3 Or orderStatus = 4 Or orderStatus = 5 ';

        //代发货   
        $list = OrderModel::getList($uid,$type,$cate);

        return $this->fetch('songshuiyuan/s_q_order',[
            'list'=>$list,   
        ]);
        
    }

    public function TOrderLists()
    {   
        $uid = session('uid');

        $cate = 3;

        $type = 'orderStatus = 2 Or orderStatus = 3 Or orderStatus = 4 Or orderStatus = 5 ';

        //代发货   
        $list = OrderModel::getList($uid,$type,$cate);

        return $this->fetch('songshuiyuan/t_q_order',[
            'list'=>$list,   
        ]);
    }

    // 送水员接单
    public function Receipt()
    {
            
        $orderId = $_GET['orderId'];

        $uid     = session('uid');

        $result = OrderModel::Receipt($orderId, $uid);

        return json_decode($result);
    }
    

    // 送水员退回订单
    public function giveUp()
    {
        $orderId = $_GET['orderId'];

        $uid     = session('uid');    

        $result = OrderModel::giveUp($orderId, $uid);

        return json_decode($result);
    }

    // 已完成
    public function giveTo()
    {
        $orderId = $_GET['orderId'];

        $uid     = session('uid');    

        $result = OrderModel::giveTo($orderId, $uid);

        return json_decode($result);
    }


    // 送水员确认收桶
    public function Buckets()
    {
        $orderId = $_GET['orderId'];

        $uid     = session('uid');    

        $result  = OrderModel::Buckets($orderId, $uid);

        return json_decode($result);
    }
    
    
}
