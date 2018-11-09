<?php

namespace app\reservation\controller;

use app\reservation\model\Water_order;
use think\Controller;
use think\Request;

class Order extends Controller{

    protected $users_id=2;

    /**
     * 购物车添加
     */
    public function postsave()
    {
        $order = new Water_order();
        $data = input('post.');
        $data['userId'] = $this->users_id;
        $data['orderNo'] = time().rand(100000,999999);//订单号
        $data['createTime'] = date('Y-m-d H:i:s');//订单提交时间
        $res = $order->data($data)->save();
        if($res){
            $shopCart = new Shopcart();
            $shopCart->getempty();
            $result = array(
                'status'=>1,
                'msg'=>'订单提交成功',
                'data'=>$data
            );
        }else{
            $result = array(
                'status'=>0,
                'msg'=>'订单提交失败',
            );
        }

        return json_encode($result);
    }

    /*
     * 查看订单列表
     * -10:被删除 0:代付款 1:代发货 2:已发货 3:已收货 4:已完成
     * */
    public function getshow()
    {
        $order = new Water_order();
        $orderStatus = input('get.orderStatus');
        switch ($orderStatus){
            case -10://所有订单
                $list = $order->where('orderStatus','>=',-10)->select();
                break;
            case 0://代付款
                $list = $order->where('orderStatus','=',0)->select();
                break;
            case 1://代付款
                $list = $order->where('orderStatus','=',1)->select();
                break;
            case 2://代付款
                $list = $order->where('orderStatus','=',2)->select();
                break;
            case 3://代付款
                $list = $order->where('orderStatus','=',3)->select();
                break;
            case 4://代付款
                $list = $order->where('orderStatus','=',4)->select();
                break;
            default:
                $data = array(
                    'status' => 2,
                    'mes' => '您填写的orderStatus是不存在的',
                );
                return json($data);
        }
        if($list != []){
            $data = array(
                'status' => 1,
                'mes' => '订单列表获取成功',
                'data'=>$list
            );
        }else{
            $data = array(
                'status' => 0,
                'mes' => '订单列表不存在',
            );
        }
        return json($data);
    }

    /**
     * 删除订单
     */
    public function getdel()
    {
        $order = new Water_order();
        $id = input('get.orderNo');
        $res = $order->save(['status'=>-9],['orderNo'=>$id]);
        if($res){
            $data = array(
                'msg'=>'订单已经删除',
                'status'=>1,
            );
        }else{
            $data = array(
                'msg'=>'意外错误',
                'status'=>0,
            );
        }
        return json($data);
    }

    /**
     * @param $orderNo
     * @return string
     */
    public function notify($orderNo)
    {
        $order = new Water_order();
        $id = input('get.orderNo');
        $updata = array(
            'status'=>1
        );
        $res = $order->save($updata,['orderNo'=>$orderNo]);
        if($res){
            return 'success';
        }else{
            return 'error';
        }
    }

    /**
     * 统计订单数量
     * -10:被删除 0:代付款 1:代发货 2:已发货 3:已收货 4:已完成
     * @return int|string
     */
    public function getshowcount()
    {

        $order = new Water_order();
        $orderStatus = input('get.orderStatus');
        $count = $order->where('orderStatus','=',$orderStatus)->count();
        return $count;
    }
}