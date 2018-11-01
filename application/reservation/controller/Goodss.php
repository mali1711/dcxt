<?php

namespace app\reservation\controller;

use think\Controller;
use app\admin\model\Goods;
use think\Request;

class Goodss extends Controller{

    /**
     * 后去商品列表
     * @return mixed
     */
    public function getlist()
    {
        
    }

    /**
     * 获取所有商品列表
     */
    public function getall()
    {
        $goods = new Goods();
        $list = $goods->getall();
        if(empty($list)){
            return returnapi('商品获取失败',100001);
        }else{
           return returnapi('信息获取成功',0,$list);
        }

    }

    /**
     * 根据分类获取商品
     */
    public function getcateGoods($cid='')
    {
        if($cid==''){
            return returnapi('请写传入正确的分类id',200002);
        }
        $goods = new Goods();
        $list = $goods->where('cid',$cid)->select();
        if(empty($list)){
            return returnapi('当前分类下没有商品',100002);
        }else{
            return returnapi('信息获取成功',0,$list);
        }
    }
}
