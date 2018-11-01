<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Order extends Model
{
    //获取所有的数据
    public static function GetAll()
    {   
        $where = [];

        $where['status'] = 1;

        $where['orderCate'] = 1;

    	$result = Order::order('id', 'desc')->where($where)->paginate(15);


        foreach ($result as $key => $value) {
            $result[$key]['userName'] = 'zhangsan';

            //1 代发货 2 已发货 3 待收货 4 已收货 5 已取消
            if ($result[$key]['orderStatus'] == 1) {
                $result[$key]['orderStatus'] = '待发货';
            }
            if ($result[$key]['orderStatus'] == 2) {
                $result[$key]['orderStatus'] = '已发货';
            }
            if ($result[$key]['orderStatus'] == 3) {
                $result[$key]['orderStatus'] = '待收货';
            }
            if ($result[$key]['orderStatus'] == 4) {
                $result[$key]['orderStatus'] = '已收货';
            }
            if ($result[$key]['orderStatus'] == 5) {
                $result[$key]['orderStatus'] = '已取消';
            }

            // 1 余额 2 微信 3 不可提现金额
            if ($result[$key]['payType'] == 1) {
                $result[$key]['payType'] = '余额支付';
            }

            if ($result[$key]['payType'] == 2) {
                $result[$key]['payType'] = '微信支付';
            }

            if ($result[$key]['payType'] == 3) {
                $result[$key]['payType'] = '不可提现金额';
            }

            $result[$key]['createTime'] = date('Y-m-d H:i:s',$value['createTime']);

            $result[$key]['payTime'] = date('Y-m-d H:i:s',$value['payTime']);

        }

        // dump($result);die();
    	return $result;
    }

    // 订单带薪资数据
    public static function getOrderLists($uid,$times)
    {
        $where = [];

        $where['status']      = 1;    

        $where['uid']         = $uid;

        $where['createTime'] = $times;

        $orderList = Db::name('salary')->where($where)->select();

        $list = [];

        $userPrice = Db::name('user')->where('id',$uid)->value('price');

        $list['xinzi'] = $userPrice;

        $money = 0;

        $tmpArr = [];

        if($orderList) {
            foreach ($orderList as $key => $value) {
                    // $list['data'][$key] = Db::name('order')->where('id',$value['orderId'])->find();
                    $money += $value['money'];
                    $tmpArr = Db::name('order')->where('id',$value['orderId'])->find();
                    $tmpArr['goodsList'] = Db::name('order_goods')->where(['orderNo'=>$tmpArr['orderNo']])->select();
                    $tmpArr['ticheng']   = $money;
                    $tmpArr['times'] = date('Y-m',$value['createTime']);
                    $list['data'][$key] = $tmpArr;

            }
            $list['xinzi'] = $money + $userPrice;
            
        }

        return $list;
    }


    // 薪资列表
    public static function getXinziList($uid)
    {
        $where = [];

        $where['status']      = 1;    

        $where['uid']         = $uid;

        $orderList = Db::name('salary')->where($where)->group('createTime')->order('createTime desc')->select();

        return $orderList;
    }

    //接口数据
    public static function GetAllApi()
    {
        $result = Order::all(['is_type'=>0]);
        return $result;
    }
    //交流互动接口
    public static function GetProAllApi()
    {
        $result = Order::all(['is_type'=>1]);
        return $result;
    }
    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id)
    {
        $result = Db::name('order')->find($id);

        $result['nickName'] = Db::name('user')->where('id',$result['userId'])->value('nickName');

        // 1 余额 2 微信 3 不可提现金额
        if ($result['payType'] == 1) {
            $result['payType'] = '余额支付';
        }

        if ($result['payType'] == 2) {
            $result['payType'] = '微信支付';
        }

        if ($result['payType'] == 3) {
            $result['payType'] = '不可提现金额';
        }

        return $result;
    }

    //执行修改
    public static function UpdateData($id,$data)
    {
        $Banner = New Banner;
        $result = $Banner->save($data,['id'=>$id]);
        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Banner::destroy($id);
        return $result;
    }


    public static function getList($uid, $type, $cate)
    {
        $where = [];

        $where['orderCate']   = $cate;

        $where['status']      = 1;    

        $where['deliverType'] = 1;

        $orderStatus          = $type;

            // dump($orderStatus);die;
            if ( $orderStatus != 'orderStatus = 1' ) {
                $where['SuId'] = $uid;
            }
        
            $region = Db::name('user')->where('id',$uid)->value('region');

            $arr = [];

            $regions = '';

            $string  = 'region = 10000';

            if ($region) {
                
                $region = explode(',', $region);

                if (isset($region) && $region) {
                    
                    foreach ($region as $k => $v) {
                    
                        if ($v !== '') {

                            $str = Db::name('region')->where('id',$v)->value('address');

                            $arr[$k] = explode(',', $str);

                        }
                    
                    }
                }
                
                if (isset($arr) && $arr) {
                    foreach($arr as &$v){
                        array_pop($v);
                    }
                }

                foreach ($arr as &$v) {
                    $regions .= ','.implode(',', $v);
                }

                $whe = explode(',', $regions);
                array_shift($whe);

                foreach ($whe as &$value) {
                     $string .= ' Or region='.$value;
                }

                $wheres = $string;
            } else {

                $where['region'] = 0;
            }
        // halt($wheres);
        // 订单列表
            $list = Db::name('order')->where($where)->where($string)->where($orderStatus)->order('orderStatus asc, id desc')->select();

            if ($cate == 1)
            {
                if (isset($list) && $v) {
                    foreach($list as $k => $v) {

                        $list[$k]['goods'] = Db::name('order_goods')->where(['orderNo'=>$v['orderNo']])->select();

                    }
                }
            }
        
        
        return $list;
    }

    public static function getOrderList($uid, $type, $cate, $deliverType)
    {  
        $where = [];

        $where['userId'] = $uid;

        $where['orderCate']   = $cate;

        $where['status']      = 1;    

        $where['deliverType'] = $deliverType;

        if ($type != 0) {
            $where['orderStatus'] = $type;
        }

        $orderList            = Db::name('order')->where($where)->order('orderStatus asc, id desc')->select();

        if ($orderList) {
            foreach ($orderList as $key => $value) {

                $orderList[$key]['goodsList'] = Db::name('order_goods')->where(['o.orderNo'=>$value['orderNo']])
                                                                        ->alias('o')
                                                                        ->join('goods g','g.id = o.goodsId')
                                                                        ->join('Category c','c.id = g.cid')
                                                                        ->order('o.id desc')
                                                                        // ->where('g.is_type=1')
                                                                        ->field('o.goodsNum,o.cangchu,o.goodsPrice,c.name as cateName,g.thumb_img,g.goods_name as goodsName')
                                                                        ->select();
            }
        }

        return $orderList;
    }

    public static function Receipt($orderId, $uid)
    {
        if (!$orderId) {
            return json_encode(['code'=>1025,'msg'=>'订单标识不存在']);
        }
        if (!$uid) {
            return json_encode(['code'=>1025,'msg'=>'用户标识不存在']);
        }

        $result = Db::name('order')->where('id', $orderId)->update(['SuId'=>$uid,'orderStatus'=>2]);

        if ($result) {
            return json_encode(['code'=>1001,'msg'=>'操作成功']);
        }

        return json_encode(['code'=>1025,'msg'=>'操作失败']);
    }

    public static function OrderDetail($id)
    {
        $Detail = Db::name('order')->where('id', $id)->find();

        if (isset($Detail) && $Detail) {

                

                $Detail['goodsList'] = Db::name('order_goods')->where(['o.orderNo'=>$Detail['orderNo']])
                                                                        ->alias('o')
                                                                        ->join('goods g','g.id = o.goodsId')
                                                                        ->join('Category c','c.id = g.cid')
                                                                        ->order('o.id desc')
                                                                        // ->where('g.is_type=1')
                                                                        ->field('o.goodsNum,o.cangchu,o.goodsPrice,c.name as cateName,g.thumb_img,g.goods_name as goodsName')
                                                                        ->select();
                $Detail['uname'] = Db::name('user')->where('id',$Detail['SuId'])->value('nickname');
                $Detail['umobile'] = Db::name('user')->where('id',$Detail['SuId'])->value('mobile');

        }
        return $Detail;
    
    }

    public static function giveUp($orderId, $uid)
    {
        if (!$orderId) {
            return json_encode(['code'=>1025,'msg'=>'订单标识不存在']);
        }
        if (!$uid) {
            return json_encode(['code'=>1025,'msg'=>'用户标识不存在']);
        }

        $result = Db::name('order')->where('id', $orderId)->update(['SuId'=>'','orderStatus'=>1]);

        // dump(Db::name('order')->getLastSql());
        if ($result) {
            return json_encode(['code'=>1001,'msg'=>'操作成功']);
        }

        return json_encode(['code'=>1025,'msg'=>'操作失败']);
    }

    public static function giveTo($orderId, $uid)
    {
        if (!$orderId) {
            return json_encode(['code'=>1025,'msg'=>'订单标识不存在']);
        }
        if (!$uid) {
            return json_encode(['code'=>1025,'msg'=>'用户标识不存在']);
        }

        $result = Db::name('order')->where(['id'=>$orderId,'SuId'=>$uid])->update(['orderStatus'=>3]);

        // dump(Db::name('order')->getLastSql());
        if ($result) {
            return json_encode(['code'=>1001,'msg'=>'操作成功']);
        }

        return json_encode(['code'=>1025,'msg'=>'操作失败']);
    }

    public static function Buckets($orderId, $uid)
    {
        if (!$orderId) {
            return json_encode(['code'=>1025,'msg'=>'订单标识不存在']);
        }
        if (!$uid) {
            return json_encode(['code'=>1025,'msg'=>'用户标识不存在']);
        }

        $result = Db::name('order')->where('id', $orderId)->update(['orderStatus'=>3]);

        // dump(Db::name('order')->getLastSql());
         
        
        if ($result) {

            $orderInfo = Db::name('order')->where('id', $orderId)->find();

            $mon       = Db::name('config')->where('id', 1)->value('bucketDeposit'); 

            $money     = $orderInfo['num'] * $mon;

            $oldMoney  = Db::name('user')->where('id',$orderInfo['userId'])->value('money');

            $newMoney  = $oldMoney + $money;

            Db::name('user')->where('id', $orderInfo['userId'])->update(['money'=>$newMoney]);


            return json_encode(['code'=>1001,'msg'=>'操作成功']);
        }

        return json_encode(['code'=>1025,'msg'=>'操作失败']);
    }

    public static function SetOrderDetail($id,$payType,$cartType,$orderType,$cardType)
    {
        if (!$id) {
            return json_encode(['code'=>1025,'msg'=>'订单标识不存在']);
        } 

        if (!$payType) {
            return json_encode(['code'=>1025,'msg'=>'支付方式错误']);
        }   

        $data = [];

        $datas = [];

        $data['orderStatus'] = 1;

        $data['status']      = 1;

        $data['payTime']     = time();

        $data['payType']     = $payType;

        $data['isPay']       = 1;

        if ($orderType == 2 || $orderType == 5 || $orderType == 4 || $orderType == 7) {
            $data['orderStatus'] = 3;
        }


        $result = Db::name('order')->where('id', $id)->update($data);

        $Order  = Db::name('order')->where('id', $id)->find();

        if ($orderType == 5) {

            $oldMoney = Db::name('user')->where('id',$Order['userId'])->value('money');

            $newMoney = $Order['realTotalMoney'] + $oldMoney;

            $datas['money'] = $newMoney;

            $res   = Db::name('user')->where('id', $Order['userId'])->update($datas);
        } elseif ($orderType == 1) {
            // 查询商品、修改库存
             $goodsInfo = Db::name('order_goods')->where(['orderNo'=>$Order['orderNo']])->select();

             if ($goodsInfo) {
                    $goodsNum = 0;
                foreach ($goodsInfo as $k => $v) {
                    $goodsId  = $v['goodsId'];
                    $num      = $v['goodsNum'];
                    $stock    = Db::name('goods')->where('id',$goodsId)->value('stock');
                    $newStock = $stock - $num;
                    // 删减库存
                    Db::name('goods')->where('id',$goodsId)->update(['stock'=>$newStock]);
                    // 计算订单内商品数量
                    $goodsNum += $v['goodsNum'];
                }

                    // 跟未使用的桶进行匹配
                    $bucketArr = Db::name('bucket')->where(['bStatus'=>0])->limit($goodsNum)->select();

                    foreach ($bucketArr as $key => $value) {
                        Db::name('bucket')->where(['id'=>$value['id']])->update(['bStatus'=>1]);
                    }

             }
            
            $res = 1; 
        } elseif ($orderType == 2) {

            $res   = Db::name('bucket')->where('orderNo', $Order['orderNo'])->update(['status'=>1]);
            // if ($res) {
            //     $ids   = Db::name('bucket')->where(['orderNo'=>$Order['orderNo'],'status'=>1])->column('id');
            //     if ($ids) {

            //         if ($ids) {
            //             $buckets = [];
            //             foreach ($ids as $k => $v) {
            //                 $buckets[$k]['uid'] = $Order['userId'];
            //                 $buckets[$k]['bid'] = $v;
            //                 $buckets[$k]['createTime'] = time();
            //                 $buckets[$k]['status'] = 1;
            //             }
            //             $res = Db::name('user_bucket')->insertAll($buckets);
            //         }

            //     }
            // }
        } elseif ($orderType == 4) {

            // 时间添加

            $res   = Db::name('bucket')->where('id', $Order['bucketId'])->find();

            if ($res) {

                $oldtime = $res['validity'];

                if ($Order['type'] == 1) {
                    $newsTime = $res['validity'] + (30 * 24 * 60 * 60);
                } else if ($Order['type'] == 2) {
                    $newsTime = $res['validity'] + (365 * 24 * 60 * 60);
                }

                $res   = Db::name('bucket')->where('id', $Order['bucketId'])->update(['validity'=>$newsTime]);

            }
        } elseif ($orderType == 7) {
            if (!$payType) {
                return json_encode(['code'=>1025,'msg'=>'充值类型错误']);
            }  
            $userData = [];
            
            if ($Order['cardType'] == 2) {
              
                $userData['level'] = 2;
                $userData['identification'] = 1;
                $userData['cardMoney'] = $Order['goodsMoney'];
                $res = Db::name('user')->where('id', $Order['userId'])->update($userData);

            } elseif ($Order['cardType'] == 3){
                
                $oldMoney = Db::name('user')->where('id', $Order['userId'])->value('cardMoney');
                $newMoney = $oldMoney + $Order['goodsMoney'];
                $userData['cardMoney'] = $newMoney;
                $userData['level'] = 3;
                $userData['identification'] = 1;
                $res = Db::name('user')->where('id', $Order['userId'])->update($userData);
            }
                
        }

        
        if ($cartType == 1) {
            Db::name('cart')->where(['uid'=>$Order['userId'],'selected'=>1])->update(['status'=>0]);
        }

        return $res;
    }

    public static function completeOrder($userId, $orderId)
    {

        // 查询订单状态
        $orderInfo = Db::name('order')->where('id',$orderId)->find();

        if (!$orderInfo) {
            return false;
        }

        // 查询用户信息
        $userInfo  = Db::name('user')->where('id',$userId)->find();

        if (!$userInfo) {
            return false;
        }

        // 修改订单状态
        $result = Db::name('order')->where('id',$orderId)->update(['orderStatus'=>4]);

        // 订单内商品数量
        $goodsList = Db::name('order_goods')->where(['orderNo'=>$orderInfo['orderNo']])->select();
        $num = 0;
        foreach ($goodsList as $key => $value) {
            $num += $value['goodsNum'];
        }

        $price = Db::name('config')->where('id',1)->value('price');

        $money = $num * $price;

        $data = [];
        $data['uid'] = $orderInfo['SuId'];
        $data['createTime'] = strtotime(date('Y-m',time()));
        $data['orderId']    = $orderId;
        $data['money']      = $money;
        $data['num']        = $num;

        // 给薪资表添加数据
        $res = Db::name('salary')->insert($data);

        //给用户返现不可提现金额
        $oldMoney  = Db::name('user')->where('id',$userId)->value('noMoney');

        $newsMoney = $oldMoney + $orderInfo['fanxian'];

        $res = Db::name('user')->where('id',$userId)->update(['noMoney'=>$newsMoney]);

        return $res;
    }

    public static function completeOrderZiQu($userId, $orderId)
    {
         // 查询订单状态
        $orderInfo = Db::name('order')->where('id',$orderId)->find();

        if (!$orderInfo) {
            return false;
        }

        // 查询用户信息
        $userInfo  = Db::name('user')->where('id',$userId)->find();

        if (!$userInfo) {
            return false;
        }

        // 修改订单状态
        $result = Db::name('order')->where('id',$orderId)->update(['orderStatus'=>4]);

        //给用户返现不可提现金额
        $oldMoney  = Db::name('user')->where('id',$userId)->value('noMoney');

        $newsMoney = $oldMoney + $orderInfo['fanxian'];

        $res = Db::name('user')->where('id',$userId)->update(['noMoney'=>$newsMoney]);

        return $res;

    }

    public static function cancelOrder($userId, $orderId, $orderText)
    {
         // 查询订单状态
        $orderInfo = Db::name('order')->where('id',$orderId)->find();

        if (!$orderInfo) {
            return false;
        }

        // 查询用户信息
        $userInfo  = Db::name('user')->where('id',$userId)->find();

        if (!$userInfo) {
            return false;
        }

        // 修改订单状态
        $result = Db::name('order')->where('id',$orderId)->update(['orderStatus'=>5,'orderText'=>$orderText]);

        return $result;
    }




}