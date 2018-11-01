<?php
/**
 * Created by 七月.
 * Author: 七月
 * 微信公号：小楼昨夜又秋风
 * 知乎ID: 七月在夏天
 * Date: 2017/2/26
 * Time: 14:15
 */

namespace app\api\controller\v1;
use think\Controller;
use think\Loader;
use think\Db;
Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class Pay extends Controller
{
   public function DoPay() {
            //订单号
        // $order=$_GET['orderNo'];
        $order = time();
        $money=$_GET['money']*100;
        //     初始化值对象
        $input = new \WxPayUnifiedOrder();
        //     文档提及的参数规范：商家名称-销售商品类目
        $input->SetBody("长阳集");
        //     订单号应该是由小程序端传给服务端的，在用户下单时即生成，demo中取值是一个生成的时间戳
        $input->SetOut_trade_no("$order");
        //     费用应该是由小程序端传给服务端的，在用户下单时告知服务端应付金额，demo中取值是1，即1分钱
        $input->SetTotal_fee("$money");
        $input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
        $input->SetTrade_type("JSAPI");
        //     由小程序端传给服务端
        $input->SetOpenid(input('openid'));
        // halt($input);
        //     向微信统一下单，并返回order，它是一个array数组
        $order = \WxPayApi::unifiedOrder($input);
        //     json化返回给小程序端
        header("Content-Type: application/json");
        echo json_encode($order);
  }

    public function yuePay() {
        try {   
            // 拼接参数
            if (isset($_GET['openid']) && $_GET['openid']) {
                $openId = $_GET['openid'];
            } else {
                throw new \Exception("操作失败，缺少参数");
            }
            // 拼接参数
            if (isset($_GET['orderNo']) && $_GET['orderNo']) {
                $orderNo = $_GET['orderNo'];
            } else {
                throw new \Exception("操作失败，缺少参数");
            }

            // 拼接参数
            if (isset($_GET['uid']) && $_GET['uid']) {
                $uid = $_GET['uid'];
            } else {
                throw new \Exception("操作失败，缺少参数");
            }

            // 拼接参数
            if (isset($_GET['paytype']) && $_GET['paytype']) {
                $type = $_GET['paytype'];
            } else {
                throw new \Exception("操作失败，缺少参数");
            }

            $orderType = Db::name('order')->where('id',$orderNo)->value('orderCate');

            $money     = Db::name('order')->where('id',$orderNo)->value('realTotalMoney');

            $userInfo  = Db::name('user')->where(['id'=>$uid,'openId'=>$openId])->find();

            if (!$userInfo) {
                throw new \Exception("操作失败，用户信息不匹配");
            }

            $datas = [];
            // 余额支付
            if ($type == 1) {
                    
                if ($orderType == 3) {
                    $money = Db::name('order')->where('id',$orderNo)->value('yunfei');
                }

                $userMoney = $userInfo['money'];

                if ($userMoney < $money) {
                    throw new \Exception("操作失败，余额不足");
                }

                $newsMoney = $userMoney - $money;

                $datas['money']     = $newsMoney;

            // 不可提现金额支付
            } else if ($type == 3) {

                $userMoney = $userInfo['noMoney'];

                if ($userMoney < $money) {
                    throw new \Exception("操作失败，余额不足");
                }

                $newsMoney = $userMoney - $money;

                $datas['noMoney']     = $newsMoney;

            }


            $res = Db::name('user')->where(['id'=>$uid,'openId'=>$openId])->update($datas);

            if ($res) {
                return json_encode(['code'=>'1001','meg'=>'付款成功','data'=>null]);
            } else {
                return json_encode(['code'=>'1025','meg'=>'操作失败，服务器错误','data'=>null]);
            }

        } catch (\Exception $e) {
            // Db::rollback();
            return json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
        }

    } 


}