<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use app\admin\model\Order as OrderModel;


class Putforward extends Model
{
    public $table = "water_put_forward";
    //获取所有的数据
    public static function GetAll($where)
    {
    	$result = Putforward::where($where)
                            ->alias('p')
                            ->join('User u','u.id = p.uid')
                            ->order('p.PutStatus asc', 'p.createTime desc','p.id desc','p.status asc')
                            ->field('p.*,u.nickname')
                            // ->select();
                            ->paginate(15);

    	return $result;
    }

    //接口数据
    public static function GetWechatAll($userid)
    {
        $result = PutForward::all(['userid'=>$userid]);
        return $result;
    }
    
    //查找一条数据
    public static function GetOne($id)
    {
        $result = PutForward::alias('p')
                            ->join('User u','u.id = p.uid')
                            ->field('p.*,u.nickname')
                            ->find($id);

        $result['cardNo']    = '';
        $result['bank']      = '';
        $result['cusername'] = '';        

        if ($result) {
            $res = Db::name('card')->where('id',$result['cardId'])->find();
            if($res) {
                $result['cardNo'] = $res['cardNo'];
                $result['bank']   = $res['bank'];
                $result['cusername'] = $res['userName'];
            } 
        }

        return $result;
    }

    //多条件查询一条数据
    public static function GetCondOne($where)
    {
        $result = PutForward::where($where)->find();
        return $result;
    }

    //执行添加
    public static function doAction($id,$putStatus)
    {
        if (!$id && !$putStatus) {
            return  json_encode(["code"=>0,"meg"=>'参数不足']);
        }   

        $data   = PutForward::where('id',$id)->find();

        // 执行修改状态操作
        try {

        if ($putStatus == 1) {
            
            // // 查询用户余额进行操作
            // $money = Db::name('user')->where('id',$data['uid'])->value('money');

            // // 判断余额
            // if ($money < $data['money']) {
            //     return  json_encode(["code"=>0,"meg"=>'操作失败,余额不足']);
            // }

            // //进行 余额计算
            // $new_money = $money - $data['money'];

            // //入库操作
            // Db::name('user')->where('id',$data['uid'])->update(['money'=>$new_money]);

            $result = PutForward::where('id',$id)->update(['putStatus'=>$putStatus,'status'=>1]);


            // //提现
            // $detail    = '提现金额'; 

            // $result    =  OrderModel::AddBill($data['uid'], 0 - $data['money'], $detail);

            } else if ($putStatus == 2) {

                $result = PutForward::where('id',$id)->update(['putStatus'=>$putStatus,'status'=>1]);
                
                // 查询用户余额进行操作
                $money = Db::name('user')->where('id',$data['uid'])->value('money');

                $newMoney = $data['money'] + $money;

                //入库操作
                Db::name('user')->where('id',$data['uid'])->update(['money'=>$newMoney]);

                //提现
                $detail    = '提现驳回'; 

                $result    =  OrderModel::AddBill($data['uid'], $data['money'], $detail);

                //生成一条订单通知消息
                    
                $userMobile = Db::name('config')->where('id',1)->value('userMobile');

                $datas = [];

                $datas['problem'] = '提现驳回消息';

                $datas['answer'] = '，您的提现申请被驳回，管理员会与您联系，管理员联系方式：' . $userMobile;

                $datas['create_time'] = time();
                $datas['type'] = 2;
                $datas['uid'] = $data['uid'];

                Db::name('help')->insert($datas);


            }
            
            if ($result === false) {
                return  json_encode(["code"=>0,"meg"=>'操作失败']);
            }else{
                return json_encode(["code"=>1,"meg"=>"操作成功"]);
            }

        } catch (Exception $e) {
            return  json_encode(["code"=>0,"meg"=>'操作失败']);
        }
        

    }

    //执行修改
    public static function UpdateData($id,$data)
    {

    }

}
