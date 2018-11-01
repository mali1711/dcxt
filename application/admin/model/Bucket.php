<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Bucket extends Model
{
    //获取所有的数据
    public static function GetAll()
    {
    	$result = Bucket::order('id', 'desc')->paginate(15);
    	return $result;
    }
    // 获取所有的逾期数据

    public static function GetBucketList()
    {
        $where = [];

        $where['status'] = 1;

        // $where['validity'] = '';

        $result = Bucket::where($where)->order('validity', 'asc')->paginate(15);

        if ($result) {
            foreach ($result as $key => $value) {

                $userInfo = Db::name('user')->where('id',$value['uid'])->find();

                $validity = ($value['validity'] - time()) / 24 / 60 / 60;

                $result[$key]['userName']   = $userInfo['nickname'];

                $result[$key]['mobile']     = $userInfo['mobile'];

                $result[$key]['createTime'] = date('Y-m-d H:i:s', $value['createTime']);

                $result[$key]['validity']   = (int)$validity;
   
                if ((int)$validity >= 0) {
                    unset($result[$key]);
                }


            }
        }
        return $result;
    }

    // 提醒
    public static function Tixing($id)
    {

        $bucketInfo = Bucket::where('id',$id)->find();

        if ($bucketInfo === false) {
            return  json_encode(["code"=>1,"meg"=>'操作失败']);
        }else{
        
            //生成一条消息发送给用户
            $data = [];

            $data['problem'] = '平台提醒消息';

            $data['answer'] = '桶Id为'.$id.'的桶租金已逾期，请尽快缴纳逾期费用，以免给您订水带来不便';//如有问题请联系网站管理人员！';

            $data['create_time'] = time();

            $data['type'] = 5;
            
            $data['uid'] = $bucketInfo['uid'];

            Db::name('help')->insert($data);
            
            return json_encode(["code"=>0,"meg"=>"操作成功"]);
        
        }

        
    }





    //接口数据
    public static function GetAllApi()
    {
        $result = Bucket::all();
        return $result;
    }
    /**
     * 查找用户的单个数据
     */
    public static function GetOne($id)
    {
        $result = Bucket::find($id);
        return $result;
    }
    //执行添加
    public static function AddData($data)
    {
        $Bucket = New Bucket;
        $result = $Bucket->save($data);
        return $result;
    }
    //执行修改
    public static function UpdateData($id,$data)
    {
        $Bucket = New Bucket;
        $result = $Bucket->save($data,['id'=>$id]);
        return $result;
    }
    //执行删除
    public static function DeleteData($id)
    {
        $result = Bucket::destroy($id);
        return $result;
    }
}