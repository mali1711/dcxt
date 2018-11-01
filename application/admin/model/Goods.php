<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Goods extends Model
{
    //获取所有的数据
    public static function GetAll()
    {
        $result = Db::table('water_goods')
                    ->alias('g')
                    ->join('Category c','c.id = g.cid')
                    ->order('g.status desc','g.id desc')
                    // ->where('g.is_type=1')
                    ->field('g.*,c.name')
                    ->paginate(15);
        
        // if($result) {
        //     foreach ($result as &$value) {
                
                
        //         $value['yunfei'] = Db::name('config')->where('id',1)->value('freight');
        //         $data[] = $value;

        //     }
        //     $result = $data;
        // }

// die;
    	return $result;
    }

    //接口数据
    public static function GetAllApi()
    {
        $result = Goods::all();
        return $result;
    }
    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id,$uid=null)
    {
        $result = Goods::find($id);

        if ($result) {

            if ($uid && $uid != 'null') {
                
                // 会员等级
                $level = Db::name('user')->where('id',$uid)->value('level');
                
                if ($level) {

                    switch ($level) {
                        case 1:
                            $result['level'] = '普通会员';
                            $result['money'] = $result['ordinaryMoney'];
                            break;
                        case 2:
                            $result['level'] = '银卡会员';
                            $result['money'] = $result['silverCardMoney'];
                            break;
                        case 3:
                            $result['level'] = '金卡会员';
                            $result['money'] = $result['goldenCardMoney'];
                            break;
                    }
                }
                
            }
            // 分类
            $result['catName'] = Db::name('category')->where('id',$result['cid'])->value('name');
            // 运费
            $result['yunfei']  = Db::name('config')->where('id',1)->value('freight');

            $result['cangchu'] = Db::name('config')->where('id',1)->value('warehousing');
        
        }

        return $result;
    }

    //执行添加
    public static function AddData($data)
    {
        $Goods = New Goods;

        $result = $Goods->validate('GoodsValidate')->save($data);

        if ($result === false) {
            return  json_encode(["code"=>1,"meg"=>$Goods->getError()]);
        }else{
            return json_encode(["code"=>0,"meg"=>"操作成功"]);
        }
    }
    //执行修改
    public static function UpdateData($id,$data,$type = 0)
    {
        $Goods = New Goods;
        if ($type == 1) {
            $result = $Goods->save($data,['id'=>$id]);
        } else {
            $result = $Goods->validate('GoodsValidate')->save($data,['id'=>$id]);
        }
        
        // dump($Goods->getLastSql());
        // dump($result);die();
        if ($result) {
            return json_encode(["code"=>1,"meg"=>"操作成功"]);
        }else{
            return json_encode(["code"=>1,"meg"=>$Goods->getError()]);
        }

    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Goods::destroy($id);
        return $result;
    }
}