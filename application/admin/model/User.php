<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class User extends Model
{
    //获取所有的数据
    public static function GetAll($id)
    {
    	$result = Db::table('water_user')
                    ->where('identity',$id)
                    ->order('status desc','id asc')
                    // ->field('g.*')
                    ->paginate(15);

        $result->toArray();

        foreach ($result as $k => $v) {

                $data = array();
                $data = $v;

            switch ($data['identity']) {
                case '1':
                    $data['identity'] = "会员";
                    break;
                case '2':
                    $data['identity'] = "商家";
                    $data['flag']     = Db::name('info_show')->where('uid',$data['id'])->value('flag');
                    break; 
                case '3':
                    $data['identity'] = "送水员";
                    break; 
            }

            switch ($data['identification']) {
                case '1':
                    $data['identification'] = "永久会员";
                    break;
                case '2':
                    $data['identification'] = "临时会员";
                    break; 
            }

            switch ($data['level']) {
                case '1':
                    $data['level'] = "普通会员";
                    break;
                case '2':
                    $data['level'] = "银卡会员";
                    break; 
                case '3':
                    $data['level'] = "金卡会员";
                    break;
            }

            $result->offsetSet($k,$data);
        }

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
    public static function GetOne($id)
    {
        $result = User::find($id);


        if (isset($result) && $result) {
            switch ($result['identity']) {
                case '1':
                    $result['identity'] = "会员";
                    $result['type'] = 1;
                    break;
                case '2':
                    $result['identity'] = "商家";
                    $result['type'] = 2;
                    break; 
                case '3':
                    $result['identity'] = "送水员";
                    $result['type'] = 3;
                    break; 
            }

            switch ($result['identification']) {
                case '1':
                    $result['identification'] = "永久会员";
                    break;
                case '2':
                    $result['identification'] = "临时会员";
                    break; 
            }

            switch ($result['level']) {
                case '1':
                    $result['level'] = "普通会员";
                    break;
                case '2':
                    $result['level'] = "银卡会员";
                    break;
                case '3':
                    $result['level'] = "金卡会员";
                    break; 
            }

            $newTime = ((($result['validity'] * 24*60*60) + strtotime($result['create_time'])) - time())/24/60/60;

            if ((int)$newTime < 0 ) {
                $newTime = 0;
            }

            $result['newsvalidity'] = (int)$newTime;

            $bucketNum = Db::name('bucket')->where(['uid'=>$result['id'],'status'=>1])->count();

            $bucketDay = Db::name('bucket')->where(['uid'=>$result['id'],'status'=>1])->order('validity desc')->value('validity');

            if ($bucketDay) {
                $bucketDay = intval((($bucketDay - time()) / 60 / 60 / 24));
            }

            $result['bucketNum'] = $bucketNum ? $bucketNum : 0;
            $result['bucketDay'] = $bucketDay ? $bucketDay : 0;

        }

        return $result;
    }
    //执行添加
    public static function AddData($data)
    {
        $User = New User;
        if (isset($data['password'])  && $data['password'] && isset($data['passwords'])  && $data['passwords'] && $data['password'] == $data['passwords']) {
        $data['password'] = md5(trim($data['password']));
        unset($data['passwords']);
            } else{
                return json_encode(["code"=>0,"meg"=>"两次密码不一致，请检查密码重新输入"]);
        }

        $data['nickname'] = $data['username'];


        $result = $User->validate('UserValidate')->save($data);

        if ($result === false) {
            return  json_encode(["code"=>0,"meg"=>$User->getError()]);
        }else{
            return json_encode(["code"=>1,"meg"=>"操作成功"]);
        }
    }
    //执行修改
    public static function UpdateData($id,$data)
    {
        $User = New User;
        
        try {

            $result = $User->validate('UserValidate')->save($data,['id'=>$id]);

            if ($result) {
                return json_encode(["code"=>1,"meg"=>"操作成功"]);
            }else{
                return json_encode(["code"=>0,"meg"=>"操作失败"]);
            }
        } catch (\Exception $e) {
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }

    }

   //执行修改支付密码
    public static function do_updata_pay($id,$data)
    {
        $User = New User;

        if (isset($data['payment'])  && $data['payment'] && isset($data['payments'])  && $data['payments'] && $data['payment'] == $data['payments']) {
                $data['payment'] = md5(trim($data['payment']));
                unset($data['payments']);
        } else{
            return json_encode(["code"=>0,"meg"=>"两次密码不一致，请检查密码重新输入"]);
        }
        
        try {

            $result = $User->save($data,['id'=>$id]);

            if ($result) {
                return json_encode(["code"=>1,"meg"=>"操作成功"]);
            }else{
                return json_encode(["code"=>0,"meg"=>"操作失败"]);
            }
        } catch (\Exception $e) {
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }

    }


    //执行修改登录密码
    public static function do_updata_pwd($id,$data)
    {
        $User = New User;

        if (isset($data['password'])  && $data['password'] && isset($data['passwords'])  && $data['passwords'] && $data['password'] == $data['passwords']) {
                $data['password'] = md5(trim($data['password']));
                unset($data['passwords']);
        } else{
            return json_encode(["code"=>0,"meg"=>"两次密码不一致，请检查密码重新输入"]);
        }
         
        try {

            $result = $User->save($data,['id'=>$id]);

            if ($result) {
                return json_encode(["code"=>1,"meg"=>"操作成功"]);
            }else{
                return json_encode(["code"=>0,"meg"=>"操作失败,于原密码一样"]);
            }
        } catch (\Exception $e) {
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }

    }

    //执行删除
    public static function DeleteData($id)
    {
        $result = Goods::destroy($id);
        return $result;
    }

    //查看商家显示信息
    public static function GetInfoOne($id)
    {

        $result = Db::name('info_show')->where('uid',$id)->find();

        return $result;
    }

    //商家信息信息 Api 主键
    public static function ShangInfo()
    {

        $result = Db::name('info_show')->find($id);

        return $result;
    }


    //修改商家显示信息
    public static function update_info_show($id,$data)
    {
        if ($id) {
            $result = DB::name('info_show')->where('id',$id)->update($data);
        } else {
             $result = DB::name('info_show')->insert($data);
        }

        if ($result) {
                return json_encode(["code"=>1,"meg"=>"操作成功"]);
            }else{
                return json_encode(["code"=>1,"meg"=>"操作成功"]);
            }
    }




}