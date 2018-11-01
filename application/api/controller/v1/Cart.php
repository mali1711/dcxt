<?php 
namespace app\api\controller\v1;
/**
* 用户接口
*/
use think\Controller;
use think\Db;
use app\admin\model\User as UserModel;
use app\api\controller\v1\Login;

class Cart extends Controller
{


    public function AddCart()
    {
    	try{   
    	 	$data = [];

            $data['status'] = 1;

    	 	// id
			if (isset($_POST['uid']) && $_POST['uid']) {
				$data['uid'] = $_POST['uid'];
			} else {
				return json_encode(['code'=>1025,'meg'=>'用户标识不存在']);
			}
			// 姓名
			if (isset($_POST['goods_id']) && $_POST['goods_id']) {
				$data['gid'] = $_POST['goods_id'];
			} else {
				return json_encode(['code'=>1025,'meg'=>'商品标识不存在']);
			}
			// 姓名
			if (isset($_POST['numbers']) && $_POST['numbers']) {
				$data['num'] = $_POST['numbers'];
			} else {
				return json_encode(['code'=>1025,'meg'=>'商品数量不存在']);
			}

			// 添加购物车 判断是否有相同数据 ， 如果有，泽添加数量，如果没有，则添加
			$Info = Db::name('cart')->where(['gid'=>$data['gid'],'uid'=>$data['uid'],'status'=>1])->find(); 
		 	 
		 	 if ($Info) {
		 	 	$data['num'] = $Info['num'] + $data['num'];

                // 查询库存
                $numbers = Db::name('goods')->where('id',$data['gid'])->value('stock'); 

                if ($numbers < $data['num']) {
                    return json_encode(['code'=>'1025','meg'=>'添加失败,库存不足','data'=>null]);
                }
		 	 	// 修改数据
		 	 	$result = Db::name('cart')->where('id',$Info['id'])->update(['num'=>$data['num']]);

		 	 	if (!$result) {
		 	 		return json_encode(['code'=>1025,'meg'=>'添加购物车失败']);
		 	 	}

		 	 } else {
		 	 	// 添加购物车 
				$data['create_time'] = time();
				$result = Db::name('cart')->insert($data); 
		 	 }
		 	
			
			if (!$result) {
				return json_encode(['code'=>'1025','meg'=>'失败','data'=>null]);
			}else{
				return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>$result]);
			}
    	}catch (\Exception $e){
            return json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }
    }


    /*
     * 获取购物车内容 @param $uid  
     */
    public function CartList($uid,$type,$cat = 1)
    {

    	if ($uid == null || !intval($uid)){
                return json_encode(['code'=>1025,'meg'=>'用户标识不存在']);
            }
 
        // 会员等级
        $level = Db::name('user')->where('id',$uid)->value('level');
        
        $where = array();

        $type == 2 ? $where['selected'] = 1 : '';
        $where['uid']      = $uid;
        $where['status']      = 1;
        $res = Db::name('cart')->where($where)->order('create_time desc')->select();
        // dump($where);die();
        // 运费
        $yunfei  = Db::name('config')->where('id',1)->value('freight');
        //仓储
        $cangchu = Db::name('config')->where('id',1)->value('warehousing');

        if (!$cangchu){ 
            $cangchu = 0;
        }

        if ($res) {

            $data  = [];
            $Cinfo = [];
            $fanxian = 0;

        	foreach ($res as $k => $v) {

                $goods_info = Db::name('goods')->where('id',$v['gid'])->find();
        		$res[$k]['goods_name'] = $goods_info['goods_name'];
        		$res[$k]['goods_pic']  = $goods_info['goods_pic'];
        		$res[$k]['thumb_img']  = $goods_info['thumb_img'];
                // 分类
                $res[$k]['catName'] = Db::name('category')->where('id',$goods_info['cid'])->value('name');
                



                 if ($level) {

                    switch ($level) {
                        case 1:
                            $Cinfo['level']   = '普通会员';
                            $res[$k]['money']  = $goods_info['ordinaryMoney'];
                            $fanxian          = $fanxian+($goods_info['ordinaryMoney']*$res[$k]['num']);
                            break;
                        case 2:
                            $Cinfo['level']   = '银牌会员';
                            $res[$k]['money']  = $goods_info['silverCardMoney'];
                            $fanxian          = $fanxian+($goods_info['silverCardMoney']*$res[$k]['num']);
                            break;
                        case 3:
                            $Cinfo['level']  = '金牌会员';
                            $res[$k]['money'] = $goods_info['goldenCardMoney'];
                            $fanxian         = $fanxian+($goods_info['goldenCardMoney']*$res[$k]['num']);
                            break;
                    }
                }


            }
            // dump($fanxian);die();
            
            $total = 0;
            $cangchuTotal = 0;
            $newsYunfei = 0;
            $number = 0;
            //总金额
            for ($i = 0; $i < count($res); $i++) {         // 循环列表得到每个数据
                $newsYunfei += $res[$i]['num'] * $yunfei; 
                $total += $res[$i]['num'] * $res[$i]['goods_pic'];   // 所有价格加起来
                $cangchuTotal += $res[$i]['num'] * $cangchu;
                $number += $res[$i]['num'];

            }
            // dump($cangchuTotal);die();
            // $res[0]['total'] = $total;

            //拼装数据
            //商品总价
            $Cinfo['goods_total']  = $total;

            // 仓储
            $Cinfo['cangchu']      = $cangchu;

            // 总仓储
            $Cinfo['cangchuTotal'] = $cangchuTotal;

            // 运费
            $Cinfo['yunfei']       = $newsYunfei;

            // 商品数量
            $Cinfo['number']       = $number;

            $Cinfo['yunfeis']      = $yunfei;

            if ($cat == 1) {
                //总价
                $Cinfo['total']        = $total + $cangchuTotal + $newsYunfei;
            } else {
                $Cinfo['total']        = $total + $cangchuTotal;
            }

            // 返现
            $Cinfo['fanxian']      = $fanxian;

            $data['res']  = $res;
            $data['data'] = $Cinfo;

            return json_encode(['code'=>1001,'meg'=>'获取成功', 'data'=>$data]);
        }
        return json_encode(['code'=>1025,'meg'=>'获取失败', 'data'=>null]);
    }

	/**
     * @param $uid $cid  购物车 数量 修改
     */

    public function SetCart($uid,$cid)
    {
        // 用户标识
        if (isset($uid) && $uid) {
            $data['uid'] = $uid;
        } else {
            return json_encode(['code'=>1025,'meg'=>'用户标识不存在']);
        }
        // 购物车标识
        if (isset($cid) && $cid) {
            $data['id'] = $cid;
        } else {
            return json_encode(['code'=>1025,'meg'=>'商品标识不存在']);
        }

        // 数量
        if (isset($_POST['num']) && $_POST['num']) {

            $num = $_POST['num'];

            // 查询库存
            $gid     = Db::name('cart')->where('id',$data['id'])->value('gid'); 
            $numbers = Db::name('goods')->where('id',$gid)->value('stock'); 

            if ($numbers < $num) {
                return json_encode(['code'=>'1025','meg'=>'操作失败,库存不足']);
            }

        } else {
            return json_encode(['code'=>1025,'meg'=>'商品数量不存在']);
        }



        $result = Db::name('cart')->where($data)->update(['num'=>$num]);

        if ($result) {
            return json_encode(['code'=>1001,'meg'=>'操作成功']);
        } 

        return json_encode(['code'=>1025,'meg'=>'操作失败']);
    }
    
    /**
     * @param $address_id  删除用户的购物车
     */
    public function CartDelete($cart_id)
    {
        // die(12313);
        try{    
            if ($cart_id == null || !intval($cart_id)){
                throw new \Exception('无效的参数');
            }

            $where = [
                'id'  => $cart_id,
            ];
            
            $result = Db::name('cart')->where($where)->delete($cart_id);
            if ($result){
                echo json_encode(['code'=>1001,'meg'=>'删除成功']);
            }else{
                throw new \Exception('删除失败');
            }
        }catch (\Exception $e){
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }
    }

    /*
     *
     * @param $cartId $selected
     *
     */
    public function SetCartStatus($cartId, $status)
    {
        // 用户标识
        if (isset($cartId) && $cartId) {
            $id = $cartId;
        } else {
            return json_encode(['code'=>1025,'meg'=>'购物车商品标识不存在']);
        }

        $selected = $status;

        $result = Db::name('cart')->where('id', $id)->update(['selected'=>$selected]);

        if ($result) {
            return json_encode(['code'=>1001,'meg'=>'操作成功']);
        }

        return json_encode(['code'=>1025,'meg'=>'操作失败']);
    }   

    // 批量修改状态
    public function SetCartStatusALl($uid, $status)
    {
        // 用户标识
        if (isset($uid) && $uid) {
            $uid = $uid;
        } else {
            return json_encode(['code'=>1025,'meg'=>'用户标识不存在']);
        }

        $selected = $status;

        $result = Db::name('cart')->where('uid', $uid)->update(['selected'=>$selected]);

        if ($result) {
            return json_encode(['code'=>1001,'meg'=>'操作成功']);
        }

        return json_encode(['code'=>1025,'meg'=>'操作失败']);
    }

    // 查询 桶数量
    public function bucketNum($goodsId,$uid) 
    {
        if($goodsId == 0) {
            // 计算 购物车内商品总数量
            
            $goodsInfo = Db::name('cart')->where(['status'=>1,'selected'=>1,'uid'=>$uid])->select();

            if ($goodsInfo) {
                $count = 0;
                foreach ($goodsInfo as $k => $v) {
                    $count += $v['num'];
                }

                $res = Db::name('bucket')->where(['uid'=>$uid,'bStatus'=>0,'status'=>1])->select();

                if ($res) {
                    foreach ($res as $kk => $vv) {
                         $validity = ($vv['validity'] - time()) / 24 / 60 / 60;
                        if ((int)$validity < 0) {
                            unset($res[$kk]);
                        }
                    }
                }

                $number = count($res);

                // dump($number);dump($count);halt();
                if ($number >= $count) {
                    return json_encode(['code'=>1001,'meg'=>'success']);
                }
            }


        } else {
            $res = Db::name('bucket')->where(['uid'=>$uid,'bStatus'=>0,'status'=>1])->count();

            if ($res >= 1) {
                return json_encode(['code'=>1001,'meg'=>'success']);
            }
        }
        return json_encode(['code'=>1025,'meg'=>'success']);
        
    }

}

?>