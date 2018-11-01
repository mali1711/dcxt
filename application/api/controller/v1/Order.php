<?php 
namespace app\api\controller\v1;
/**
* 用户接口
*/
use think\Controller;
use think\Db;
use app\admin\model\Address as AddressModel;
use app\admin\model\User as UserModel;
use app\admin\model\Order as OrderModel;
use app\admin\model\OrderGoods as OrderGoodsModel;
use app\api\service\UserToken;
use app\api\controller\v1\Cart;
use app\admin\model\Bucket as BucketModel;
use app\api\controller\v1\User as U;
use app\api\controller\v1\Goods;
class Order extends Controller
{
	
	public function addOrder()
	{
		Db::startTrans();
		try {

			$Cart    = new Cart;
			$order   = new OrderModel;
			$bucket  = new BucketModel;
			$goods   = new Goods;
			$address = new AddressModel;
			$user    = new U;

			$data = [];
			$data['orderNo']    = self::OrderNo();
			$data['createTime'] = time();
			$data['orderCate']  = 1;
			
			// 拼接参数
			if (isset($_GET['uid']) && $_GET['uid']) {
				$data['userId'] = $_GET['uid'];
			}
			
			// 商品类型
			if (isset($_GET['ctype']) && $_GET['ctype']) {
				$type = $_GET['ctype'];
			} else {
				throw new \Exception("操作失败，缺少参数");
			}

			// 商品Id
			if (isset($_GET['goods_id']) && $_GET['goods_id']) {
				$goods_id = $_GET['goods_id'];
			} 

			// 配送方式
			if (isset($_POST['deliverType']) && $_POST['deliverType']) {
				$data['deliverType'] = $_POST['deliverType'];
			} else {
				throw new \Exception("操作失败,配送方式参数错误");
			}

			if (isset($_POST['shenfen']) && $_POST['shenfen']) {
				$data['shenfen'] = $_POST['shenfen'];
			}

			if (isset($_POST['fanxian']) && $_POST['fanxian']) {
				$data['fanxian'] = $_POST['fanxian'];
			}
			// $data['deliverType'] = 1;

			// 订单备注
			if (isset($_POST['orderRemarks']) && $_POST['orderRemarks']) {

				if ($_POST['orderRemarks'] == 'undefined') {
					$data['orderRemarks'] = '';
				} else {
					$data['orderRemarks'] = $_POST['orderRemarks'];
				}
				
			} else {
				$data['orderRemarks'] = '';
			}

			// 订单 商品信息 订单商品总金额
			if (1 == $type) {
				$goodsRes = json_decode($goods->GoodsInfo($goods_id,$data['userId'],1,$data['deliverType']));
				
				// 商品总金额
				$data['goodsMoney']     = $goodsRes->data->data->goods_total;
				// 订单总金额
				$data['totalMoney']     = $goodsRes->data->data->total;
				// 订单实际金额
				$data['realTotalMoney'] = $goodsRes->data->data->total;
				// 运费
				$data['yunfei']         = $goodsRes->data->data->yunfei;
				
			} else if (2 == $type){
				$cats  = json_decode($Cart->CartList($data['userId'],2,$data['deliverType']));
				// 商品总金额
				$data['goodsMoney']     = $cats->data->data->goods_total;
				// 订单总金额
				$data['totalMoney']     = $cats->data->data->total;
				// 订单实际金额
				$data['realTotalMoney'] = $cats->data->data->total;
				// 运费
				$data['yunfei']         = $cats->data->data->yunfei;

			}
			
			$addre = json_decode($user->getDefault($data['userId']));

			if (!$addre || $addre->id == 0) {
 				throw new \Exception("操作失败,用户没有选择收货地址");
			}

			$adds = Db::name('address')->where('id',$addre->cid)->value('title');

			// 收货信息
			$data['userName'] = $addre->name;

			$data['userAddress'] = $adds.$addre->address;

			$data['userMobile'] = $addre->mobile;

			$data['region']     = $addre->cid;
	
			//验证数据
			self::CheckInfo($data);
			// 插入数据 & 获取orderNo
			
			$order->save($data);
			$orderNo     = $order->orderNo;
			$orderId     = $order->id;
			if (!$orderNo) {
				throw new \Exception("订单生成失败");
			}

			$goodsInfo = [];
			if (1 == $type) {
				// 单商品通道
				// 拼装数据
				$goodsRes = $goods->GoodsInfo($goods_id,$data['userId'],2,$data['deliverType']);

				$goodsRes = json_decode($goodsRes);

				if ($goodsRes) {
					foreach($goodsRes->data->res as $k => $v) {
						$goodsInfo[$k]['orderNo']    = $orderNo;
						$goodsInfo[$k]['goodsId']    = $v->id;
						$goodsInfo[$k]['goodsNum']   = 1;
						$goodsInfo[$k]['goodsPrice'] = $v->goods_pic;
						$goodsInfo[$k]['goodsName']  = $v->goods_name;
						$goodsInfo[$k]['goodsImg']   = $v->thumb_img;
						$goodsInfo[$k]['youhui']     = $v->money;
						$goodsInfo[$k]['cangchu']    = $v->cangchu;
					}
				}
				// dump($goodsRes->data->res);die;

			} else if (2 == $type){
				// 购物车通道
				// 拼装数据
				
				$cats  = $Cart->CartList($data['userId'],2,$data['deliverType']);
				// dump($cats->data);die;
				if ($cats) {
					$cats = json_decode($cats);
					
					foreach($cats->data->res as $k => $v) {
						$goodsInfo[$k]['orderNo']    = $orderNo;
						$goodsInfo[$k]['goodsId']    = $v->gid;
						$goodsInfo[$k]['goodsNum']   = $v->num;
						$goodsInfo[$k]['goodsPrice'] = $v->goods_pic;
						$goodsInfo[$k]['goodsName']  = $v->goods_name;
						$goodsInfo[$k]['goodsImg']   = $v->thumb_img;
						$goodsInfo[$k]['youhui']     = $v->money;
						$goodsInfo[$k]['cangchu']    = Db::name('config')->where('id',1)->value('warehousing');
					} 
						
				}

			}

			if (isset($goodsInfo) && $goodsInfo) {
				// 批量添加
				$orderGoods  = new OrderGoodsModel;
				$orderGoods->saveAll($goodsInfo);
			} else {
				throw new \Exception("获取商品信息失败");
			}

			Db::commit();
			return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>$orderId]);
		} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}

	// 验证数据
	private function CheckInfo($data)
	{

		if ($data['userId'] == null) {
			throw new \Exception("操作失败，缺少参数");# code... 用户标识
		}
		if ($data['goodsMoney'] == null) {
			throw new \Exception("操作失败，缺少参数");# code... 商品总金额
		}
		if ($data['deliverType'] == null) {
			throw new \Exception("操作失败，缺少参数");# code... 配送方式
		}
		if ($data['totalMoney'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...订单总金额
		}
		if ($data['realTotalMoney'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...订单实际金额
		}
		if ($data['userName'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...收货人姓名
		}
		if ($data['userAddress'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...收货人地址
		}
		if ($data['userMobile'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...收货人手机号码
		}
		if ($data['yunfei'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...运费
		}
	}


	//订单号生成规则
	public static function OrderNo()
	{
		$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    	$orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    	return $orderSn;
	}

	// 押桶订单
	public function addOrderT()
	{
		Db::startTrans();
		try {

			$Cart    = new Cart;
			$bucket  = new BucketModel;
			$order   = new OrderModel;
			$goods   = new Goods;
			$address = new AddressModel;
			$user    = new U;
			// $userId  = 1;
			$time    = 0;

			$data = [];
			$data['orderNo']    = self::OrderNo();
			$data['createTime'] = time();
			$data['orderCate']  = 2;

			// 拼接参数
			if (isset($_GET['uid']) && $_GET['uid']) {
				$userId = $_GET['uid'];
			} else {
				throw new \Exception("操作失败，缺少参数");
			}
			
			// 商品类型
			if (isset($_GET['type']) && $_GET['type']) {
				$type = $_GET['type'];
			} else {
				throw new \Exception("操作失败，缺少参数");
			}
			// dump(is_numeric($_GET['num']));die;
			if (!intval($_GET['num']) || !is_numeric($_GET['num'])) {
				throw new \Exception("押桶数量格式错误！");
			}

			// 商品Id
			if (isset($_GET['num']) && $_GET['num'] ) {
				$num = $_GET['num'];
			} else {
				throw new \Exception("操作失败，缺少参数");
			}
			
			$data['userId']     = $userId;

			// $num     = 2;

			// $type    = 1;

			$data['num'] = $num;

			$data['goodsMoney'] = 0;

			$bucketMoney = Db::name('config')->where('id',1)->value('bucketDeposit');

			$money = $bucketMoney * $num;

			if ($type == 1) {

				$goodsMoney = Db::name('config')->where('id',1)->value('bucketRentMonth');
				$data['goodsMoney'] = $goodsMoney * $num;
				$time = time()+ (30 * 24 * 60 * 60);

			} elseif ($type == 2) {

				$goodsMoney = Db::name('config')->where('id',1)->value('bucketRentYear');
				$data['goodsMoney'] = $goodsMoney * $num;
				$time = time()+ (365 * 24 * 60 * 60);
			}

			$data['totalMoney']     = $data['goodsMoney'] + $money;


			$data['realTotalMoney']     = $data['goodsMoney'] + $money;

			//验证数据
			//self::CheckInfoT($data);
			
			$order->save($data);
			$orderNo = $order->orderNo;
			$orderId = $order->id;
			if (!$orderNo) {
				throw new \Exception("订单生成失败");
			}

			$buckets = [];

			for ($i = 0; $i < $num; $i++) {
			  	
			  	$buckets[$i]['uid'] = $userId;

				$buckets[$i]['orderNo'] = $orderNo;

				$buckets[$i]['createTime'] = time();

				$buckets[$i]['validity']   = $time;

				$buckets[$i]['status'] = 0;

				$buckets[$i]['bucketDeposit'] = Db::name('config')->where('id',1)->value('bucketDeposit');

			} 

			if (isset($buckets) && $buckets) {
				// 批量添加
				$bucket->saveAll($buckets);
			} else {
				throw new \Exception("获取桶信息失败");
			}

			Db::commit();
			return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>$orderId]);

			// dump($bucket);die();
			} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}

	// 验证数据 桶订单
	private function CheckInfoT($data)
	{

		if ($data['userId'] == null) {
			throw new \Exception("操作失败，缺少参数");# code... 用户标识
		}
		if ($data['goodsMoney'] == null) {
			throw new \Exception("操作失败，缺少参数");# code... 商品总金额
		}
		if ($data['deliverType'] == null) {
			throw new \Exception("操作失败，缺少参数");# code... 配送方式
		}
		if ($data['totalMoney'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...订单总金额
		}
		if ($data['realTotalMoney'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...订单实际金额
		}
		if ($data['userName'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...收货人姓名
		}
		if ($data['userAddress'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...收货人地址
		}
		if ($data['userMobile'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...收货人手机号码
		}
		if ($data['yunfei'] == null) {
			throw new \Exception("操作失败，缺少参数");# code...运费
		}
	}

	// 退桶订单
	public function addOrderTui()
	{
		Db::startTrans();
		try {

			$Cart    = new Cart;
			$bucket  = new BucketModel;
			$order   = new OrderModel;
			$goods   = new Goods;
			$address = new AddressModel;
			$user    = new U;
			$userId  = $_POST['userId'];

			if (!$userId) {
				throw new \Exception("用户身份失效");
			}

			if (!isset($_POST['num']) || !$_POST['num']) {
				throw new \Exception("数量错误");
			}

			$data = [];
			$data['orderNo']    = self::OrderNo();
			$data['createTime'] = time();
			$data['orderCate']  = 3;			

			// 拼接参数
			$data['userId']     = $userId;

			$data['yunfei']     = Db::name('config')->where('id',1)->value('freight');

			$bucketMoney        = Db::name('config')->where('id',1)->value('bucketDeposit');

			$data['goodsMoney'] = $bucketMoney;

			$data['totalMoney'] = $bucketMoney * $_POST['num'];

			$data['realTotalMoney'] = $data['yunfei'];

			$data['num']        = $_POST['num'];

			$addre = json_decode($user->getDefault($data['userId']));
			
			if (!$addre || $addre->id == 0) {
 				throw new \Exception("请先去设置默认收货地址！");
			}

			if (!isset($_POST['ids']) || !$_POST['ids']) {
				throw new \Exception("参数错误");
			}

			$adds = Db::name('address')->where('id',$addre->cid)->value('title');

			$data['bucketId'] = $_POST['ids'];

			// 收货信息
			$data['userName'] = $addre->name;

			$data['userAddress'] = $adds.$addre->address;

			$data['userMobile'] = $addre->mobile;

			$data['region']     = $addre->cid;

			$data['deliverType'] = 1;

			//验证数据
			//self::CheckInfoT($data);
			
			$order->save($data);
			$orderNo = $order->orderNo;
			$orderId = $order->id;

			if (!$orderNo) {
				throw new \Exception("订单生成失败");
			}

			Db::commit();
			return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>$orderId]);

			// dump($bucket);die();
			} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}

	// 桶租金订单
	public function addOrderTzj()
	{
		Db::startTrans();
		try {

			$Cart    = new Cart;
			$bucket  = new BucketModel;
			$order   = new OrderModel;
			$goods   = new Goods;
			$address = new AddressModel;
			$user    = new U;
			
			$time    = 0;

			$data = [];
			$data['orderNo']    = self::OrderNo();
			$data['createTime'] = time();
			$data['orderCate']  = 4;

			// 拼接参数
			if (isset($_GET['uid']) && $_GET['uid']) {
				$data['userId'] = $_GET['uid'];
			} else {
				throw new \Exception("操作失败，缺少参数");
			}
			
			// 商品类型
			if (isset($_GET['type']) && $_GET['type']) {
				$type = $_GET['type'];
				$data['type'] = $type;
			} else {
				throw new \Exception("操作失败，缺少参数");
			}

			// 商品类型
			if (isset($_GET['bucketId']) && $_GET['bucketId']) {
				$data['bucketId'] = $_GET['bucketId'];
			} else {
				throw new \Exception("操作失败，缺少参数");
			}


			$data['goodsMoney'] = 0;

			if ($type == 1) {

				$data['goodsMoney'] = Db::name('config')->where('id',1)->value('bucketRentMonth');
				$time = time()+ (30 * 24 * 60 * 60);

			} elseif ($type == 2) {

				$data['goodsMoney'] = Db::name('config')->where('id',1)->value('bucketRentYear');
				$time = time()+ (365 * 24 * 60 * 60);
			}

			$data['totalMoney'] = $data['goodsMoney'];

			$data['realTotalMoney'] = $data['goodsMoney'];

			//验证数据
			//self::CheckInfoT($data);
			
			$order->save($data);
			$orderNo = $order->orderNo;
			$orderId = $order->id;
			if (!$orderNo) {
				throw new \Exception("订单生成失败");
			}

			Db::commit();
			return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>$orderId]);

			// dump($bucket);die();
			} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}

	// 桶租金充值
	public function rent()
	{
		Db::startTrans();
		try {

			$Cart    = new Cart;
			$bucket  = new BucketModel;
			$order   = new OrderModel;
			$goods   = new Goods;
			$address = new AddressModel;
			$user    = new U;
			$userId  = 1;
			$time    = 0;

			$data = [];
			$data['orderNo']    = self::OrderNo();
			$data['createTime'] = time();
			$data['orderCate']  = 4;

			// 拼接参数
			$data['userId']     = $userId;

			$data['bucketId']   = 1;

			$type               = 1;

			$num                = 1;

			$data['goodsMoney'] = 0;

			$bucketMoney        = Db::name('config')->where('id',1)->value('bucketDeposit');

			if ($type == 1) {

				$data['goodsMoney'] = Db::name('config')->where('id',1)->value('bucketRentMonth');
				$time = time()+ (30 * 24 * 60 * 60);

			} elseif ($type == 2) {

				$data['goodsMoney'] = Db::name('config')->where('id',1)->value('bucketRentYear');
				$time = time()+ (365 * 24 * 60 * 60);
			}

			$data['totalMoney'] = $data['goodsMoney'];

			//验证数据
			//self::CheckInfoT($data);
			
			$order->save($data);
			$orderNo = $order->orderNo;
			if (!$orderNo) {
				throw new \Exception("订单生成失败");
			}

			Db::commit();
			return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>null]);

			// dump($bucket);die();
			} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}

	public function getOrderList($uid, $orderStatus, $cate, $deliverType)
	{

		try {

			if ($uid == null) {
                throw new \Exception("用户标识不存在");	# code...
			}
			if (!intval($uid)){
                throw new \Exception("用户标识类型不对");	# code...
            }

            if ($orderStatus == null) {
                throw new \Exception("订单状态不存在");	# code...
			}

            if ($cate == null) {
                throw new \Exception("订单类型不存在");	# code...
			}
			if (!intval($cate)){
                throw new \Exception("订单类型类型不对");	# code...
            }

            if ($deliverType == null) {
                throw new \Exception("派送方式不存在");	# code...
			}
			if (!intval($deliverType)){
                throw new \Exception("派送方式类型不对");	# code...
            }


            
            $orderList = OrderModel::getOrderList($uid, $orderStatus, $cate, $deliverType);

			if ($orderList){

                $data          = $orderList;

                return json_encode(['code'=>1001,'meg'=>'获取成功','data'=>$data]);

            }else{
			    return json_encode(['code'=>1001,'meg'=>'暂无数据','data'=>null]);
            }
		} catch (\Exception $e) {
			return json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
		}
	}

	public function OrderDetail($id)
	{

		try {
			if ($id == null) {
                throw new \Exception("订单id不存在");	# code...
			}
            
            $result = OrderModel::OrderDetail($id);
			if ($result){

                return json_encode(['code'=>1001,'meg'=>'获取成功','data'=>$result]);
            }else{
			    throw new \Exception('数据未找到');
            }
		} catch (\Exception $e) {
			return json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
		}
	}

	// 处理订单
	public function DoOrderPay()
	{

		$id = $_POST['id'];

		$payType   = $_POST['payType'];

		$cartType  = $_POST['cartType'];

		$orderType = $_POST['orderType'];

		if(isset($_POST['cardType']) && $_POST['cardType']) {
			$cardType  = $_POST['cardType'];
		} else {
			$cardType = 0;
		}

		try {
			if ($id == null) {
                throw new \Exception("订单id不存在");	# code...
			}
            
            $result = OrderModel::SetOrderDetail($id, $payType, $cartType,$orderType,$cardType);
            
			if ($result){

                return json_encode(['code'=>1001,'meg'=>'成功','data'=>null]);

            }else{
			    throw new \Exception('error');
            }
		} catch (\Exception $e) {
			return json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
		}
	}

	// 余额充值
	public function yuEorder()
	{
		try {

			$order   = new OrderModel;
			$user    = new U;
			
			$data = [];
			$data['orderNo']    = self::OrderNo();
			$data['createTime'] = time();
			$data['orderCate']  = 5;

			// 拼接参数
			if (isset($_GET['uid']) && $_GET['uid']) {
				$data['userId'] = $_GET['uid'];
			} else {
				throw new \Exception("用户身份失效");
			}

			// 拼接参数
			if (isset($_GET['goodsMoney']) && $_GET['goodsMoney']) {
				$goodsMoney = $_GET['goodsMoney'];
			} else {
				throw new \Exception("金额错误");
			}


			$data['goodsMoney'] = $goodsMoney;


			$data['totalMoney'] = $goodsMoney;

			$data['realTotalMoney'] = $goodsMoney;

			//验证数据
			//self::CheckInfoT($data);
			
			$order->save($data);
			$orderNo = $order->orderNo;
			$orderId     = $order->id;
			if (!$orderNo) {
				throw new \Exception("订单生成失败");
			}
			return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>$orderId]);

			// dump($bucket);die();
			} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}



	// 线下消费
	public function AddPaymentOrder()
	{
		try {

			$order   = new OrderModel;
			$user    = new U;
			
			$data = [];
			$data['orderNo']    = self::OrderNo();
			$data['createTime'] = time();
			$data['orderCate']  = 10;

			// 拼接参数
			if (isset($_POST['uid']) && $_POST['uid']) {
				$data['userId'] = $_POST['uid'];
			} else {
				throw new \Exception("用户身份失效");
			}

			// 拼接参数
			if (isset($_POST['moneys']) && $_POST['moneys']) {
				$goodsMoney = $_POST['moneys'];
			} else {
				throw new \Exception("金额错误");
			}

			// 拼接参数
			if (isset($_POST['paytype']) && $_POST['paytype']) {
				$payType = $_POST['paytype'];
			} else {
				throw new \Exception("支付类型错误");
			}

			// 拼接参数
			if (isset($_POST['shangId']) && $_POST['shangId']) {
				$shangId = $_POST['shangId'];
			} else {
				throw new \Exception("商家标识不存在");
			}

			$shangInfo = Db::name('user')->where(['id'=>$shangId,'identity'=>2,'status'=>1])->find();

			if (!$shangInfo){
				throw new \Exception("该商家不存在");
			}

			if($payType == 1) {
				$data['fanxian']        = $goodsMoney * $shangInfo['fanxian'];
			} else if ($payType == 3) {
				$data['fanxian'] = 0;
			}

			$data['goodsMoney']     = $goodsMoney;

			$data['totalMoney']     = $goodsMoney;

			$data['realTotalMoney'] = $goodsMoney;

			$data['payType']        = $payType;

			$data['shangId']        = $shangId;

			//验证数据
			//self::CheckInfoT($data);
			
			$order->save($data);
			$orderNo = $order->orderNo;
			$orderId     = $order->id;
			if (!$orderNo) {
				throw new \Exception("订单生成失败");
			}
			return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>$orderId]);

			// dump($bucket);die();
			} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}

	// 充值卡金
	public function addCardorder()
	{
		try {

			$order   = new OrderModel;
			$user    = new U;
			
			$data = [];
			$data['orderNo']    = self::OrderNo();
			$data['createTime'] = time();
			$data['orderCate']  = 6;

			// 拼接参数
			if (isset($_GET['uid']) && $_GET['uid']) {
				$data['userId'] = $_GET['uid'];
			} else {
				throw new \Exception("用户身份失效");
			}

			// 拼接参数
			if (isset($_GET['goodsMoney']) && $_GET['goodsMoney']) {
				$goodsMoney = $_GET['goodsMoney'];
			} else {
				throw new \Exception("金额错误");
			}
			// 拼接参数
			if (isset($_GET['cardType']) && $_GET['cardType']) {
				$cardType = $_GET['cardType'];
			} else {
				throw new \Exception("类型错误");
			}


			$data['goodsMoney'] = $goodsMoney;

			$data['cardType'] = $cardType;

			$data['totalMoney'] = $goodsMoney;

			$data['realTotalMoney'] = $goodsMoney;

			//验证数据
			//self::CheckInfoT($data);
			$userInfo = Db::name('user')->where('id',$data['userId'])->find();

			if (!$userInfo) {
				throw new \Exception("系统错误，请联系客服人员");
			}

			if ($userInfo['identification'] == 1 && $userInfo['level'] == $cardType) {
				throw new \Exception("请勿重复充值");
			}

			
			$order->save($data);
			$orderNo = $order->orderNo;
			$orderId     = $order->id;
			if (!$orderNo) {
				throw new \Exception("订单生成失败");
			}
			return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>$orderId]);

			// dump($bucket);die();
			} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}

	// 点击完成订单， 统计送水员薪资 、 返给用户不可提现金额
	
	public function completeOrder()
	{
		try {

			$order   = new OrderModel;
			$user    = new U;

			// 拼接参数
			if (isset($_POST['userId']) && $_POST['userId']) {
				$userId = $_POST['userId'];
			} else {
				throw new \Exception("用户身份失效");
			}

			// 拼接参数
			if (isset($_POST['orderId']) && $_POST['orderId']) {
				$orderId = $_POST['orderId'];
			} else {
				throw new \Exception("订单号不存在");
			}

			// 拼接参数
			if (isset($_POST['orderType']) && $_POST['orderType']) {
				$orderType = $_POST['orderType'];
			} else {
				throw new \Exception("订单号不存在");
			}
			
			if ($orderType == 1) {
				$res = OrderModel::completeOrder($userId, $orderId);

			} else {
				$res = OrderModel::completeOrderZiQu($userId, $orderId);
			}
			
			if ($res == 3) {
				return json_encode(['code'=>'1025','meg'=>'操作失败，送水员尚未确认送达','data'=>null]);
			}

			
			if ($res) {
				return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>null]);
			}

			return json_encode(['code'=>'1025','meg'=>'操作失败','data'=>null]);

			// dump($bucket);die();
			} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1','meg'=>$e->getMessage(),'data'=>null]);
		}
	}

	// 点击取消订单
	
	public function cancelOrder()
	{
		try {

			$order   = new OrderModel;
			$user    = new U;

			// 拼接参数
			if (isset($_POST['userId']) && $_POST['userId']) {
				$userId = $_POST['userId'];
			} else {
				throw new \Exception("用户身份失效");
			}

			// 拼接参数
			if (isset($_POST['orderId']) && $_POST['orderId']) {
				$orderId = $_POST['orderId'];
			} else {
				throw new \Exception("订单号不存在");
			}

			// 拼接参数
			if (isset($_POST['orderText']) && $_POST['orderText']) {
				$orderText = $_POST['orderText'];
			} else {
				throw new \Exception("取消订单原因不存在");
			}
			

			$res = OrderModel::cancelOrder($userId, $orderId, $orderText);
			
			

			if ($res) {
				return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>null]);
			}

			return json_encode(['code'=>'1025','meg'=>'操作失败','data'=>null]);

			// dump($bucket);die();
			} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}

	// 申请售后
	public function ShouOrder()
	{
		try {

			$order   = new OrderModel;
			$user    = new U;

			// 拼接参数
			if (isset($_POST['userId']) && $_POST['userId']) {
				$userId = $_POST['userId'];
			} else {
				throw new \Exception("用户身份失效");
			}

			// 拼接参数
			if (isset($_POST['orderId']) && $_POST['orderId']) {
				$orderId = $_POST['orderId'];
			} else {
				throw new \Exception("订单号不存在");
			}

			// 拼接参数
			if (isset($_POST['orderText']) && $_POST['orderText']) {
				$orderText = $_POST['orderText'];
			} else {
				throw new \Exception("申请售后原因不存在");
			}
			

			$res = OrderModel::ShouOrder($userId, $orderId, $orderText);
			
			

			if ($res) {
				return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>null]);
			}

			return json_encode(['code'=>'1025','meg'=>'操作失败','data'=>null]);

			// dump($bucket);die();
			} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}
	

	// 退卡金订单
	public function TuiCardorder()
	{
		try {

			$order   = new OrderModel;
			$user    = new U;
			
			$data = [];
			$data['orderNo']    = self::OrderNo();
			$data['createTime'] = time();
			$data['orderCate']  = 8;

			// 拼接参数
			if (isset($_GET['uid']) && $_GET['uid']) {
				$data['userId'] = $_GET['uid'];
			} else {
				throw new \Exception("用户身份失效");
			}

			// 拼接参数
			if (isset($_GET['goodsMoney']) && $_GET['goodsMoney']) {
				$goodsMoney = $_GET['goodsMoney'];
			} else {
				throw new \Exception("金额错误");
			}
			// 拼接参数
			$cardType = Db::name('user')->where('id',$data['userId'])->value('level');



			$data['goodsMoney'] = $goodsMoney;

			$data['cardType'] = $cardType;

			$data['totalMoney'] = $goodsMoney;

			$data['realTotalMoney'] = $goodsMoney;

			//验证数据
			//self::CheckInfoT($data);
			$userInfo = Db::name('user')->where('id',$data['userId'])->find();

			if (!$userInfo) {
				throw new \Exception("系统错误，请联系客服人员");
			}
		
			$order->save($data);
			$orderNo = $order->orderNo;
			$orderId     = $order->id;
			if (!$orderNo) {
				throw new \Exception("订单生成失败");
			}
			return json_encode(['code'=>'1001','meg'=>'操作成功','data'=>$orderId]);

			// dump($bucket);die();
			} catch (\Exception $e) {
			// Db::rollback();
			return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}
}

?>