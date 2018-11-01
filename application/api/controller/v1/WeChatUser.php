<?php
namespace app\api\controller\v1;
/**
 * 小程序登录接口 --- 自定义登录
 */
use think\Controller;
use think\Db;
use app\admin\model\User as UserModel;
use app\admin\model\Address as AddressModel;
use app\api\controller\v1\Base;
use app\api\controller\v1\Login;
use think\Exception;

class WeChatUser extends Base
{

    function __construct($userid='',$address_id='')
    {

    }

    /**
     * 执行用户地址的修改
     */
    public function updateAddress($address_id)
    {
        try{
            if ($address_id == null || !intval($address_id)){
                throw new \Exception('无效的参数');
            }
            $result = AddressModel::UpdateData($address_id,$_POST);
            echo json_encode(['code'=>1001,'meg'=>'修改成功','data'=>$result]);
        }catch (\Exception $e){
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }
    }

    /**
     * 登录接口
     * @Author   CarLos(wang)
     * @DateTime 2018-06-08
     * @Email    carlos0608@163.com
     */
    public function AddressOne($userid,$address_id)
    {
        try{
            if ($address_id == null || !intval($address_id)){
                throw new \Exception('无效的参数');
            }
            if ($userid == null || !intval($userid)){
                throw new \Exception('用户身份无效');
            }
            $where = [
                'id'=>$address_id,
                'userid'=>$userid
            ];
            $result = AddressModel::GetCondOne($where);
            if ($result){
                echo json_encode(['code'=>1001,'meg'=>'拿到数据','data'=>$result]);
            }else{
                throw new \Exception('没有数据');
            }
        }catch (\Exception $e){
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }
    }
    /**
     * @return bool
     */
    public function UserInfo($userid)
    {

        try{
            if ($userid == null || !intval($userid)){
                throw new \Exception('用户身份无效');
            }
            $result = UserModel::GetOne($userid);
            echo json_encode(['code'=>1001,'meg'=>'识别成功','data'=>$result]);
        }catch (\Exception $e){
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }
    }
    /**
     * 执行添加、修改支付密码
     */
    public function UpdatePassword($userid)
    {   

        try{

            if ($userid == null || !intval($userid)){
                throw new \Exception('用户身份无效');
            }

            if ($_POST['mobile'] == null){
                throw new \Exception('请输入手机号');
            }
            if ($_POST['recode'] == null){
                throw new \Exception('请输入验证码');
            }
            if ($_POST['password'] == null){
                throw new \Exception('请输入密码');
            }
            if ($_POST['password'] != $_POST['repassword']){
                throw new \Exception('两次密码不一致');
            }


            if (isset($_POST['mobile']) && $_POST['mobile']) {


                // 验证码验证
                $l = new Login();
                
                $res = Login::CheckRecodes($_POST['mobile'],$_POST['recode']);

                if ($res == 0) {
                    throw new \Exception('验证码输入错误！');
                }

                $data = array();

                $data['mobile'] = $_POST['mobile'];

                $re = Db::name('user')->where('id',$userid)->value('mobile');

                if (!$re) {
                    throw new \Exception('请您先去绑定手机号码！');
                }

                $data['id']     = $userid;

                $res    = Db::name('user')->where($data)->find();
                
                if (!$res) {
                     throw new \Exception('该手机号不是您绑定的手机号码！');
                }
               
            }

            if (isset($_POST['oldpassword']) && $_POST['oldpassword']) {
                
                $where            = array();
                $where['id']      = $userid;
                $where['payment'] = md5($_POST['oldpassword']);

                $res = UserModel::where($where)->find();

                if (!$res) {
                    return json_encode(['code'=>1025,'meg'=>'原密码与账号不匹配']);
                }
            }

            $data = [
                'payment'=>md5($_POST['password']),
            ];
            $result = UserModel::where('id', $userid)->update($data);
            return json_encode(['code'=>1001,'meg'=>'设置成功']);
        }catch (\Exception $e){
            return json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }
    }
    /**
     * @param $userid  修改手机号
     */
    public function UpdateMobile($userid)
    {
        try{
            if ($userid == null || !intval($userid)){
                throw new \Exception('用户身份无效');
            }
            if (!preg_match("/^1[34578]{1}\d{9}$/",$_POST['mobile'])){
                throw new \Exception('手机号格式不正确');
            }
            $data = [
                'mobile'=>$_POST['mobile'],
            ];
            UserModel::where('id', $userid)->update($data);
            echo json_encode(['code'=>1001,'meg'=>'绑定成功']);
        }catch (\Exception $e){
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }
    }

    /**
     * @param $userid 收获地址详情
     */
    public function AddressInfo($userid)
    {
        try{
            if ($userid == null || !intval($userid)){
                throw new \Exception('用户身份无效');
            }
            $result = AddressModel::GetWechatAll($userid);
            echo json_encode(['code'=>1001,'meg'=>'获取成功','data'=>$result]);
        }catch (\Exception $e){
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }
    }

    /**
     * @param $address_id  删除用户的收货地址
     */
    public function AddressDelete($address_id,$userid)
    {
        // die(12313);
        try{    
            if ($address_id == null || !intval($address_id)){
                throw new \Exception('无效的参数');
            }
            if ($userid == null || !intval($userid)){
                throw new \Exception('用户身份无效');
            }
            $where = [
                'id'  => $address_id,
                'uid' => $userid
            ];
            self::CheckDefaultAddress($where);
            $result = Db::name('user_address')->where($where)->delete($address_id);
            if ($result){
                echo json_encode(['code'=>1001,'meg'=>'删除成功']);
            }else{
                throw new \Exception('删除失败');
            }
        }catch (\Exception $e){
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }
    }
    /**
     * 检测用户要删除的地址是否为默认
     */
    private function CheckDefaultAddress($where)
    {
        $result = Db::name('user_address')->where($where)->find();
        // if ($result['is_default'] == 1){
        //     throw new \Exception('请更换默认地址');
        // }
        if (!$result){
            throw new \Exception('权限越界');
        }
        return true;
    }
}
