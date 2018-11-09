<?php
/**
 * Created by PhpStorm.
 * User: 马黎
 * Date: 2018/11/5
 * Time: 8:01
 * Explain：购物车
 */
namespace app\reservation\controller;

use app\admin\model\Cart;
use app\admin\model\Goods;
use think\Controller;
use think\Db;
use think\Session;

class Shopcart extends Controller{

    /**
     * @var 正在操作的用户的id
     */
    protected $users_id=3;

    /**
     * 购物车列表
     */
    public function getlist()
    {
       $list =  Db::table('water_cart')->where('uid',$this->users_id)->select();
        if(empty($list)){
             $data = array(
              'status'=>0,
              'msg'=>'购物车是空的'
            );
            return json_encode($data);
        }
       foreach ($list as $k=>$v){
           $list[$k]['goodsInfo'] = $this->getgoods($v['gid']);
       }
        $data = array(
            'status' =>1,
            'msg' =>'商品列表获取成功',
            'data'=>$list
        );
       return json_encode($data);
    }
    public function getgoods($id=24)
    {
        $goods = new Goods();
        $data = $goods->where('id',$id)->column('id,goods_name,goods_pic,dishware_expenses');
        return array_values($data);
    }

    /*
     * 购物车添加数量
     * */
    public function getplus()
    {
        $data = array(
            'uid'=> $this->users_id,
            'gid'=> input('get.gid'),
            'num'=> 1,
            'create_time'=> date('Y-m-d H:i:s'),
        );
        $res = $this->_isshopCart($data);
        if($res){
            $info = array(
                'status' => 10001,
                'msg' => '购物车添加成功',
            );
        }else{
            $Cart = new Cart();
            $res = $Cart->data($data)->save();
            if($res){
                $info = array(
                    'status' => 10000,
                    'msg' => '购物车添加成功',
                );
            }else{
                $info = array(
                    'status' => 1,
                    'msg' => '购物车添加失败',
                );
            }
        }
        return json_encode($info);
    }

    public function getreduceOfGood()
    {
        $where['uid'] = $this->users_id;
        $where['gid'] = input('get.gid');
        $Cart = new Cart();
        $res = $Cart->where($where)->find();
        if($res){
            $Cart->where($where)->setDec('num');
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 减少购物车数量
     */
    public function getreduce()
    {
        $Cart = new Cart();
        $list = $Cart->get(input('get.id'));
        if($list == null){
            $info = array(
                'status'=>10004,
                'msg'=>'删除的商品不存在',
            );
            return json_encode($info);
        }
        if($list->num==1){
            $res = $Cart->destroy(input('get.id'));
            if($res){
                $info = array(
                    'status'=>10000,
                    'msg'=>'商品已经删除',
                );
            }else{
                $info = array(
                    'status'=>10003,
                    'msg'=>'购物车删除意外错误',
                );
            }
        }else{
            $res =  $Cart->where('id',input('get.id'))->setDec('num');
            if($res){
                $info = array(
                    'status'=>10000,
                    'msg'=>'商品已经删除',
                );
            }else{
                $info = array(
                    'status'=>10003,
                    'msg'=>'购物车删除意外错误',
                );
            }
        }

        return json_encode($info);
    }


    /*
     * 判断购物车里是否存在商品，存在数量加一
     */
    protected function _isshopCart($data)
    {
        $where['uid'] = $data['uid'];
        $where['gid'] = $data['gid'];
        $Cart = new Cart();
        $res = $Cart->where($where)->find();
        if($res){
            $Cart->where($where)->setInc('num');
            return true;
        }else{
            return false;
        }


    }


    /**
     * 清空购物车商品数量
     * @return int
     */
    public function getempty()
    {
        $cart = new Cart();
        return $cart->where('uid',$this->users_id)->delete();
    }
    
    /**
     * 获取所有购物车数量
     */
    public function getallGoodsNum()
    {
        
    }

    /**
     * 单个商品的数量
     */
    public function getgoodNum()
    {
        
    }
}
