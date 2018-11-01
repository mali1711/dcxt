<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use app\admin\model\Bucket as BucketModel;
class Order extends Model
{
    //获取所有的数据
    public static function GetAll($type, $orderStatus, $orderShowStatus,$deliverType)
    {   
        $where = [];

        $where['status'] = 1;

        $where['orderCate'] = $type;

        if ($deliverType) {
            $where['deliverType'] = $deliverType;
        }

        if ($orderShowStatus != 0) {
            $where['orderShowStatus'] =  array('neq',0);
        } else {
            // $where['orderShowStatus'] = $orderShowStatus;
        }

        if ($orderStatus) {
            $where['orderStatus'] = $orderStatus;
        } else {
            $orderStatus = 'orderStatus = 1 or orderStatus = 2 or orderStatus = 3 or orderStatus = 4';
        }

    	$result = Order::where($where)->where($orderStatus)->order('orderTstatus asc,id desc')->paginate(15);


        foreach ($result as $key => $value) {
            $result[$key]['userName'] = Db::name('user')->where('id', $value['userId'])->value('nickName');

            //1 代发货 2 已发货 3 待收货 4 已收货 5 已取消
            if ($value['orderStatus'] == 1) {
                $result[$key]['orderStatus'] = '待发货';
            }
            if ($value['orderStatus'] == 2) {
                $result[$key]['orderStatus'] = '已发货';
            }
            if ($value['orderStatus'] == 3) {
                if ($value['deliverType'] == 2) {
                    $result[$key]['orderStatus'] = '已确认';
                } else {
                    $result[$key]['orderStatus'] = '待收货';
                }
                
            }
            if ($value['orderStatus'] == 4) {
                $result[$key]['orderStatus'] = '已完成';
            }
            if ($value['orderStatus'] == 5) {
                $result[$key]['orderStatus'] = '已取消';
            }

            // 1 余额 2 微信 3 不可提现金额
            if ($value['payType'] == 1) {
                $result[$key]['payType'] = '余额支付';
            }

            if ($value['payType'] == 2) {
                $result[$key]['payType'] = '微信支付';
            }

            if ($value['payType'] == 3) {
                $result[$key]['payType'] = '不可提现金额';
            }

            $result[$key]['bucketNum'] = count(explode(',', $result[$key]['bucketId']));

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

        $where['createTime']  = $times;

        $orderList = Db::name('salary')->where($where)->select();

        $list = [];

        $list['data'] = [];

        $userPrice = Db::name('user')->where('id',$uid)->value('price');

        $list['xinzi'] = $userPrice;

        $money = 0;

        $tmpArr = [];

        if($orderList) {
            foreach ($orderList as $key => $value) {
                    // $list['data'][$key] = Db::name('order')->where('id',$value['orderId'])->find();
                    $money += $value['money'];
                    $tmpArr = Db::name('order')->where('id',$value['orderId'])->find();

                    if ($tmpArr['orderCate'] == 1) {
                        $tmpArr['goodsList'] = Db::name('order_goods')->where(['orderNo'=>$tmpArr['orderNo']])->select();
                    } else {
                        $tmpArr['goodsList'] = [];
                    }
                    
                    $tmpArr['ticheng']   = $value['money'];
                    $tmpArr['times'] = date('Y-m',$value['createTime']);
                    $list['data'][$key] = $tmpArr;

            }
            $list['xinzi'] = $money + $userPrice;
            
        }
        
        return $list;
    }


    // 薪资列表 -> 数组
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
        $result['Sname'] = '';
        $result['Smobile'] = '';
        if(isset($result['SuId']) && $result['SuId']) {
            $result['Sname'] = Db::name('user')->where('id',$result['SuId'])->value('nickName');
            $result['Smobile'] = Db::name('user')->where('id',$result['SuId'])->value('mobile');
        }

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
        // halt($result);
        $result['bucketNum'] = count(explode(',', $result['bucketId']));
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

        $yunfei = Db::name('config')->where('id',1)->value('freight');
        $cangchu = 0;
        $number  = 0;
        if ($type != 0) {
            $where['orderStatus'] = $type;
        }

        $orderList            = Db::name('order')->where($where)->order('orderStatus asc, id desc')->select();

        if ($orderList) {
            foreach ($orderList as $key => $value) {

                $goodsList = Db::name('order_goods')->where(['o.orderNo'=>$value['orderNo']])
                                                                        ->alias('o')
                                                                        ->join('goods g','g.id = o.goodsId')
                                                                        ->join('Category c','c.id = g.cid')
                                                                        ->order('o.id desc')
                                                                        // ->where('g.is_type=1')
                                                                        ->field('o.goodsNum,o.goodsPrice,c.name as cateName,g.thumb_img,g.goods_name as goodsName')
                                                                        ->select();

                if ($goodsList) {
                    
                    $cang = 2;
                    foreach ($goodsList as $kk => $vv) {
                        $cangchu += $vv['goodsNum'] * $cang;
                        $number += $vv['goodsNum'];
                    }
                }

                if ($cate == 1) {
                    $orderList[$key]['cangchu'] = $cangchu;
                }
                

                $orderList[$key]['goodsList'] = $goodsList;
                $orderList[$key]['number'] = $number;
                $orderList[$key]['yunfeis'] = $yunfei;
                $orderList[$key]['createTime'] = date('Y-m-d H:i:s',$value['createTime']);

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
        $cangchu = 0;
        $num = 0;
        if (isset($Detail) && $Detail) {

                

                $goodsList = Db::name('order_goods')->where(['o.orderNo'=>$Detail['orderNo']])
                                                                        ->alias('o')
                                                                        ->join('goods g','g.id = o.goodsId')
                                                                        ->join('Category c','c.id = g.cid')
                                                                        ->order('o.id desc')
                                                                        // ->where('g.is_type=1')
                                                                        ->field('o.goodsNum,o.cangchu,o.goodsNum,o.goodsPrice,c.name as cateName,g.thumb_img,g.goods_name as goodsName')
                                                                        ->select();

                if ($goodsList) {
                    foreach ($goodsList as $key => $value) {
                        $cangchu += $value['cangchu'] * $value['goodsNum'];
                        $num += $value['goodsNum'];
                    }
                }

                $Detail['cangchu'] = $cangchu;

                

                if ($Detail['orderCate'] == 1) {
                    $Detail['num']     = $num;
                }
                

                $Detail['address'] = Db::name('config')->where('id',1)->value('address');

                $Detail['yunfeis'] = Db::name('config')->where('id',1)->value('freight');

                $Detail['goodsList'] = $goodsList;

                $Detail['payTime']   = date('Y-m-d H:i:s',$Detail['payTime']);

                $Detail['uname']   = Db::name('user')->where('id',$Detail['SuId'])->value('nickname');
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

        $res = Db::name('order')->where(['id'=>$orderId,'SuId'=>$uid,'orderStatus'=>2])->find();

        if (!$res) {
            return json_encode(['code'=>1025,'msg'=>'操作失败，该订单状态已经发生变化']);
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

            //退桶
            $detail    = '退桶运费'; 

            $result    =  self::AddBill($orderInfo['userId'], 0 - $orderInfo['yunfei'], $detail);


            return json_encode(['code'=>1001,'msg'=>'操作成功']);
        }

        return json_encode(['code'=>1025,'msg'=>'操作失败']);
    }

    public static function SetOrderDetail($id,$payType,$cartType,$orderType,$cardType)
    {

        $bucket  = new BucketModel;

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

        if ($orderType == 2 || $orderType == 5 || $orderType == 4 || $orderType == 7 || $orderType == 10 || $orderType == 8) {
            $data['orderStatus'] = 3;
        }

        
        $result = Db::name('order')->where('id', $id)->update($data);

        $Order  = Db::name('order')->where('id', $id)->find();

        if ($orderType == 5) {

            $oldMoney = Db::name('user')->where('id',$Order['userId'])->value('money');

            $newMoney = $Order['realTotalMoney'] + $oldMoney;

            $datas['money'] = $newMoney;

            $detail    = '余额充值'; 

            $result    =  self::AddBill($Order['userId'], $Order['realTotalMoney'], $detail);

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
                    $bucketArr = Db::name('bucket')->where(['bStatus'=>0,'status'=>1,'uid'=>$Order['userId']])->limit($goodsNum)->select();
                    $bucketData = [];
                    foreach ($bucketArr as $key  => $value) {
                        $bucketData[$key]['id']  = $value['id'];
                        $bucketData[$key]['uid'] = $Order['userId'];
                        $bucketData[$key]['bStatus'] = 1;
                    }

                    $BucketModel = new BucketModel();
                    $res = $BucketModel->saveAll($bucketData);

                    $detail    = '购买商品'; 
                    $realTotalMoney = 0;
                    if ($Order['deliverType'] == 1) {
                        $realTotalMoney = $Order['realTotalMoney'];
                    } elseif ($Order['deliverType'] == 2) {
                        $realTotalMoney = $Order['realTotalMoney'];
                    }

                    $result    =  self::AddBill($Order['userId'], 0 - $realTotalMoney, $detail);
             }        
        } elseif ($orderType == 2) {

            $res   = Db::name('bucket')->where('orderNo', $Order['orderNo'])->update(['status'=>1]);

            //押桶订单
            $detail    = '桶押金充值'; 

            $result    =  self::AddBill($Order['userId'], 0 - $Order['realTotalMoney'], $detail);

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

                $detail    = '通租金充值'; 

                $result    =  self::AddBill($Order['userId'], 0 - $Order['realTotalMoney'], $detail);
                $res   = Db::name('bucket')->where('id', $Order['bucketId'])->update(['validity'=>$newsTime]);

            }
        } elseif ($orderType == 7) {
            if (!$payType) {
                return json_encode(['code'=>1025,'msg'=>'充值类型错误']);
            }  
            $userData = [];
            $userData['cardMoney'] = 0;
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
            //卡金订单
            $detail    = '卡金充值'; 

            $result    =  self::AddBill($Order['userId'], 0 - $Order['goodsMoney'], $detail);
        } elseif ($orderType == 3) {
            // 注销桶
            $buckets         = [];
            $bucketId        = explode (',',$Order['bucketId']);
            
            for ($i = 0; $i < count($bucketId); $i++) {
                $buckets[$i]['id']      = $bucketId[$i];
                $buckets[$i]['status']  = 0;
                $buckets[$i]['bStatus'] = 0;
                $Buckets[$i]['uid']     = $Order['userId'];
            }
            // if(isset($bucketId) && $bucketId) {
            // dump($datas);die();
            // }
            $res = $bucket->saveAll($buckets);

            //退桶运费
            $detail    = '退桶运费'; 

            $result    =  self::AddBill($Order['userId'], 0 - $Order['yunfei'], $detail);
        } elseif ($orderType == 10) {
            
            // 生成收益记录
            $profit = [];
            $profit['uid']        = $Order['shangId'];
            $profit['orderId']    = $Order['id'];
            $profit['money']      = $Order['realTotalMoney'];
            $profit['userId']     = $Order['userId'];

            $profit['createTime'] = time();

            $profit['times'] = strtotime(date('Y-m-d',time()));

            // 给收益表添加数据
            $res = Db::name('profit')->insert($profit);

            // 给商家增加金额
            $oldMoney  = Db::name('user')->where('id', $Order['shangId'])->value('money');

            $newsMoney = $oldMoney + $Order['realTotalMoney'];

            $res = Db::name('user')->where('id', $Order['shangId'])->update(['money'=>$newsMoney]);

            
            //给用户返现不可提现金额
            $level = Db::name('user')->where('id',$Order['userId'])->value('level');

            if ($Order['fanxian'] && $Order['fanxian'] != 0) {
                if($level == 3) {
                    $oldMoney  = Db::name('user')->where('id', $Order['userId'])->value('money');

                    $newsMoney = $oldMoney + $Order['fanxian'];

                    $res = Db::name('user')->where('id', $Order['userId'])->update(['money'=>$newsMoney]);

                } else if($level == 2) {

                    $oldMoney  = Db::name('user')->where('id', $Order['userId'])->value('noMoney');

                    $newsMoney = $oldMoney + $Order['fanxian'];

                    $res = Db::name('user')->where('id', $Order['userId'])->update(['noMoney'=>$newsMoney]);
                }
            }

            //线下消费
            $detail    = '线下消费'; 
            $result    =  self::AddBill($Order['userId'], 0 - $Order['realTotalMoney'], $detail);
            
            if($level == 3) {
                $detail    = '线下消费返利 余额'; 
            } else if($level == 2) {
                $detail    = '线下消费返利 不可提现金额'; 
            }
            if ($Order['fanxian'] && $Order['fanxian'] != 0 && $level != 1) {
                $result    =  self::AddBill($Order['userId'], $Order['fanxian'], $detail);
            }
            
        } elseif ($orderType == 8) {
            // 卡金清零 、退回到余额 并且修改用户的 会员身份
            $oldMoney = Db::name('user')->where('id', $Order['userId'])->value('cardMoney');
            $userMoney = Db::name('user')->where('id', $Order['userId'])->value('money');
            // $newMoney = $oldMoney + $Order['goodsMoney'];
            $userData['cardMoney'] = 0;
            $userData['money']     = $oldMoney + $userMoney;
            $userData['level']     = 1;
            $userData['identification'] = 1;
            $res = Db::name('user')->where('id', $Order['userId'])->update($userData);

            //退卡金订单
            $detail    = '卡金退款'; 

            $result    =  self::AddBill($Order['userId'], $oldMoney, $detail);

        }

        if ($cartType == 1) {
          $res =  Db::name('cart')->where(['uid'=>$Order['userId'],'selected'=>1])->update(['status'=>0]);
        }

        return $res;
    }

    public static function completeOrder($userId, $orderId)
    {

        // 查询订单状态
        $orderInfo = Db::name('order')->where('id',$orderId)->find();

        if (!isset($orderInfo['orderStatus']) || !$orderInfo['orderStatus']) {
            return false;
        }

        // 查询用户信息
        $userInfo  = Db::name('user')->where('id',$userId)->find();

        if (!$userInfo) {
            return false;
        }

        if ($orderInfo['orderStatus'] != 3) {
            return 3;
        }

        // 修改订单状态
        $result = Db::name('order')->where('id',$orderId)->update(['orderStatus'=>4]);

        // 订单内商品数量
        $goodsList = Db::name('order_goods')->where(['orderNo'=>$orderInfo['orderNo']])->select();
        $num = 0;
        foreach ($goodsList as $key => $value) {
            $num += $value['goodsNum'];
        }


         // 跟使用的桶进行匹配
        $bucketArr = Db::name('bucket')->where(['bStatus'=>1,'status'=>1,'uid'=>$userId])->limit($num)->select();
        $bucketData = [];
 
        foreach ($bucketArr as $key => $value) {
            $bucketData[$key]['id']      = $value['id'];
            $bucketData[$key]['bStatus'] = 0;
            $bucketData[$key]['uid']     = $userId;
        }

        $BucketModel = new BucketModel();

        $res = $BucketModel->saveAll($bucketData);

   
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
        
        $detail    = '不可提现金额'; 

        if ($orderInfo['fanxian'] > 0) {

            $result    =  self::AddBill($userId,$orderInfo['fanxian'],$detail);

            //给用户返现不可提现金额
            $oldMoney  = Db::name('user')->where('id',$userId)->value('noMoney');

            $newsMoney = $oldMoney + $orderInfo['fanxian'];

            $res = Db::name('user')->where('id',$userId)->update(['noMoney'=>$newsMoney]);
        }

        return $res;
    }

    public static function completeOrderZiQu($userId, $orderId)
    {
         // 查询订单状态
        $orderInfo = Db::name('order')->where('id',$orderId)->find();

        if (!$orderInfo) {
            return false;
        }

        if (!isset($orderInfo['orderStatus']) || !$orderInfo['orderStatus']) {
            return false;
        }

        // 查询用户信息
        $userInfo  = Db::name('user')->where('id',$userId)->find();

        if (!$userInfo) {
            return false;
        }
        // if ($orderInfo['orderStatus'] != 3) {
        //     return false;
        // }
        // 修改订单状态
        $result = Db::name('order')->where('id',$orderId)->update(['orderStatus'=>3]);

        // //给用户返现不可提现金额
        // $oldMoney  = Db::name('user')->where('id',$userId)->value('noMoney');

        // $detail    = '不可提现金额'; 

        // $result    =  self::AddBill($userId,$orderInfo['fanxian'],$detail);

        // $newsMoney = $oldMoney + $orderInfo['fanxian'];

        // $res = Db::name('user')->where('id',$userId)->update(['noMoney'=>$newsMoney]);

        return $result;

    }

    // 确认订单
    public static function completeOrderDoZiQu($userId, $orderId)
    {
         // 查询订单状态
        $orderInfo = Db::name('order')->where('id',$orderId)->find();

        if (!$orderInfo) {
            return false;
        }

        if (!isset($orderInfo['orderStatus']) || !$orderInfo['orderStatus']) {
            return false;
        }

        // 查询用户信息
        $userInfo  = Db::name('user')->where('id',$userId)->find();

        if (!$userInfo) {
            return false;
        }
        if ($orderInfo['orderStatus'] != 3) {
            return false;
        }
        // 修改订单状态
        $result = Db::name('order')->where('id',$orderId)->update(['orderStatus'=>4]);

        // 订单内商品数量
        $goodsList = Db::name('order_goods')->where(['orderNo'=>$orderInfo['orderNo']])->select();
        $num = 0;
        if ($goodsList) {
            foreach ($goodsList as $key => $value) {
                $num += $value['goodsNum'];
            }     
        }

        // 跟使用的桶进行匹配
        $bucketArr = Db::name('bucket')->where(['bStatus'=>1,'status'=>1,'uid'=>$orderInfo['userId']])->limit($num)->select();
        $bucketData = [];

        foreach ($bucketArr as $key => $value) {
            $bucketData[$key]['id']      = $value['id'];
            $bucketData[$key]['bStatus'] = 0;
            $bucketData[$key]['uid']     = $orderInfo['userId'];
        }

        $BucketModel = new BucketModel();
        $res = $BucketModel->saveAll($bucketData);



        //给用户返现不可提现金额
        $oldMoney  = Db::name('user')->where('id',$userId)->value('noMoney');

        $detail    = '不可提现金额'; 

        $result    =  self::AddBill($userId,$orderInfo['fanxian'],$detail);

        $newsMoney = $oldMoney + $orderInfo['fanxian'];

        if ($newsMoney != 0) {
            $res = Db::name('user')->where('id',$userId)->update(['noMoney'=>$newsMoney]);
        }
       
        return $result;

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

    public static function ShouOrder($userId, $orderId, $orderText)
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
        $result = Db::name('order')->where('id',$orderId)->update(['orderShowStatus'=>1,'showText'=>$orderText]);

        return $result;
    }


    public static function orderTuiKuan($type, $orderId)
    {


        $orderInfo = Db::name('order')->where('id',$orderId)->find();
        $price     = Db::name('config')->where('id',1)->value('price'); 
        $goodsNum  = 0;
        if ($type == 2) {
            if (!isset($orderInfo['SuId']) || !$orderInfo['SuId']) {
                return  json_encode(["code"=>1,"meg"=>'操作失败,该订单未有送水员接单！']);
            } 
        }
        if ($orderInfo) {
            $goodsList = Db::name('order_goods')->where('orderNo',$orderInfo['orderNo'])->select();

            if ($goodsList) {
                foreach ($goodsList as $key => $value) {

                    $stock = Db::name('goods')->where('id',$value['goodsId'])->value('stock');

                    $newsStock = $stock + $value['goodsNum'];

                    Db::name('goods')->where('id',$value['goodsId'])->update(['stock'=>$newsStock]);

                    $goodsNum += $value['goodsNum'];

                }
                    // 跟使用的桶进行匹配
                    $bucketArr = Db::name('bucket')->where(['bStatus'=>1,'status'=>1,'uid'=>$orderInfo['userId']])->limit($goodsNum)->select();
                    $bucketData = [];

                    foreach ($bucketArr as $key => $value) {
                        $bucketData[$key]['id']      = $value['id'];
                        $bucketData[$key]['bStatus'] = 0;
                        $bucketData[$key]['uid']     = $orderInfo['userId'];
                    }

                    $BucketModel = new BucketModel();
                    $res = $BucketModel->saveAll($bucketData);              
            }

        }

        $money = Db::name('user')->where('id',$orderInfo['userId'])->value('money');
        $realTotalMoney = 0;
        // 全额退款
        if ($type == 1) {

            $newsMoney = $money + $orderInfo['realTotalMoney'];

            $realTotalMoney = $orderInfo['realTotalMoney'];

            $result = Db::name('user')->where('id',$orderInfo['userId'])->update(['money'=>$newsMoney]);
        // 扣运费退款
        } elseif ($type == 2) {
            // 商家送货退款
            if ($orderInfo['deliverType'] == 1) {
                $newsMoney = $money + $orderInfo['realTotalMoney']  - $orderInfo['yunfei'];
                $realTotalMoney = $orderInfo['realTotalMoney'] - $orderInfo['yunfei'];

                // 给送水员加薪资
                $salary = [];
                $salary['uid']        = $orderInfo['SuId'];
                $salary['createTime'] = strtotime(date('Y-m',time()));
                $salary['orderId']    = $orderId;
                $salary['money']      = $price * $goodsNum;
                $salary['num']        = $goodsNum;

                // 给薪资表添加数据
                $res = Db::name('salary')->insert($salary);



            // 自取订单退款
            } else {
                $newsMoney = $money + $orderInfo['realTotalMoney'];

                $realTotalMoney = $orderInfo['realTotalMoney'];
            }

            $result = Db::name('user')->where('id',$orderInfo['userId'])->update(['money'=>$newsMoney]);

        }

            $detail    = '订单退款'; 

            $result    =  self::AddBill($orderInfo['userId'],$realTotalMoney,$detail);

            //生成一条消息发送给用户
            $data = [];

            $data['problem'] = '订单退款消息';

            $data['answer'] = '订单号为'.$orderInfo['orderNo'].'的订单已退款，请去余额查看，如有问题请联系网站管理人员！';

            $data['create_time'] = time();
            $data['type'] = 3;
            $data['uid'] = $orderInfo['userId'];

            Db::name('help')->insert($data);

        $result = Db::name('order')->where('id',$orderId)->update(['orderTstatus'=>2,'orderTuiStatus'=>$type]);

        if ($result === false) {
            return  json_encode(["code"=>1,"meg"=>'操作失败']);
        }else{
            return json_encode(["code"=>0,"meg"=>"操作成功"]);
        }
    }

    public static function orderTuiTong($orderId)
    {

        $orderInfo = Db::name('order')->where('id',$orderId)->find();

        if ($orderInfo) {
            $buckets = explode(',', $orderInfo['bucketId']);

            if ($buckets) {
                $bucketMoney = 0;
                foreach ($buckets as &$value) {
                    $bucketMoney += Db::name('bucket')->where('id',$value)->value('bucketDeposit');
                } 

            }
        }
        
        $money = Db::name('user')->where('id',$orderInfo['userId'])->value('money');


        $newsMoney = $money + $bucketMoney;

        $result = Db::name('user')->where('id',$orderInfo['userId'])->update(['money'=>$newsMoney]);
            
        $detail    = '桶押金退款'; 

        $result    =  self::AddBill($orderInfo['userId'],$bucketMoney,$detail);

        $price     = Db::name('config')->where('id',1)->value('price'); 

        if ($orderInfo['SuId']) {
            // 给送水员加薪资
            $salary = [];
            $salary['uid']        = $orderInfo['SuId'];
            $salary['createTime'] = strtotime(date('Y-m',time()));
            $salary['orderId']    = $orderId;
            $salary['money']      = $price;
            $salary['num']        = 1;

            // 给薪资表添加数据
            $res = Db::name('salary')->insert($salary);
        }

        //生成一条消息发送给用户
        $data = [];

        $data['problem'] = '订单退款消息';

        $data['answer'] = '订单号为'.$orderInfo['orderNo'].'的订单已退款，请去余额查看，如有问题请联系网站管理人员！';

        $data['create_time'] = time();
        $data['type'] = 4;
        $data['uid'] = $orderInfo['userId'];

        Db::name('help')->insert($data);

        $result = Db::name('order')->where('id',$orderId)->update(['orderTstatus'=>2]);

        if ($result === false) {
            return  json_encode(["code"=>1,"meg"=>'操作失败']);
        }else{
            return json_encode(["code"=>0,"meg"=>"操作成功"]);
        }
    }

    // 消费明细接口
    public static function AddBill($uid,$money,$detail)
    {
        $data = [];

        $data['uid']        = $uid;

        $data['money']      = $money;

        $data['createTime'] = time();

        $data['detail']     = $detail;

        $result = Db::name('bill')->insert($data);
    }

}