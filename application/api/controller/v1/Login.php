<?php 
namespace app\api\controller\v1;
/**
* 小程序登录接口 --- 自定义登录
*/
use think\Controller;
use think\Db;
use app\admin\model\User as UserModel;
use app\admin\validate\UserValidate;
use app\api\controller\v1\Base;
use think\Exception;

class Login extends Base
{
	
	function __construct($mobile='')
	{
		
	}
	/**
	 * 登录接口
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-08
	 * @Email    carlos0608@163.com
	 */
	public function DoLogin()
	{
		try {
			if ($_POST['username'] == null) {
				throw new \Exception("请填写用户名");# code...
			}
			if ($_POST['password'] == null) {
				throw new \Exception("请填写密码");# code...
			}
			$result = Db::table('water_user')->where('username',$_POST['username'])->find();
			if (!$result) {
				throw new \Exception("用户名不正确");# code...
			}
			if ($result['password'] != md5($_POST['password'])) {
				throw new \Exception("密码不正确");# code...
			}
			echo json_encode(['code'=>'1001','meg'=>'登录成功','data'=>$result['id']]);
		} catch (\Exception $e) {
			echo json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}
	/**
	 * 小程序注册接口
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-07
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 * @todo 参数有 username(用户名) mobile(手机号) password(密码) nickname(昵称) photo(头像)
	 */
	public function registered()
	{
		try {
			if (empty($_POST['username'])) {
				throw new \Exception("用户名为空");# code...
			}
			if (Db::table('water_user')->where('username',$_POST['username'])->find()) {
				throw new \Exception("用户名已存在");# code...
			}
			if (!preg_match("/^1[34578]{1}\d{9}$/",$_POST['mobile'])) {
				throw new \Exception("手机号不正确");# code...
			}
			if (empty($_POST['password'])) {
				throw new \Exception("密码为空");# code...
			}
            if ($_POST['password'] != $_POST['repassword']) {
                throw new \Exception("两次密码不一致");# code...
            }
			$data['username'] = $_POST['username'];
			$data['password'] = md5($_POST['password']);
			$data['nickname'] = $_POST['username'];
            $data['mobile'] = $_POST['mobile'];
			$data['photo'] = '/default.jpg';
			$data['create_time'] = time();
			$result = UserModel::AddData($data);
			echo json_encode(['code'=>'1001','meg'=>'注册成功','data'=>$result]);
		} catch (\Exception $e) {
			echo json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>'']);
		}
	}
	/**
	 * 发送短信
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-08
	 * @Email    carlos0608@163.com
	 */
	public function SMS($mobile)
	{
		$code = rand(1000,9999);
		try{
		    if ($mobile == null){
		        throw new \Exception('手机号为空');
            }
            if (!preg_match("/^1[34578]{1}\d{9}$/",$mobile)){
		        throw new \Exception('手机号不正确');
            }
            $result = parent::waterSMS($code,$mobile);
            
		    $code = json_decode($result);
		   
		    if ($code->respCode == '00000'){
                echo json_encode(['code'=>1001,'meg'=>config('SmsCode.'.$code->respCode)]);
            }else{
                $res = config('SmsCode.'.$code->respCode);

                if($res == null){
                    echo json_encode(['code'=>1027,'meg'=>'请联系平台运营人员']);
                }else{
                    echo json_encode(['code'=>1026,'meg'=>config('SmsCode.'.$code->respCode)]);
                }
            }
        }catch (\Exception $e){
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }

	}
	/**
	 * 验证短信验证码
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-08
	 * @Email    carlos0608@163.com
	 */
	public function CheckRecode()
	{
		$mobile = $_GET['mobile'];
		$clent_recode = $_GET['recode'];
		$ReCode = cache('reCode'.$mobile);
		if ($ReCode != null && $clent_recode == $ReCode) {
			echo json_encode(['code'=>'1001','meg'=>'验证码正确']);
		}else{
			echo json_encode(['code'=>'1025','meg'=>'验证码错误']);
		}
	}


	/**
	 * 验证短信验证码2
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-08
	 * @Email    carlos0608@163.com
	 */
	public static function CheckRecodes($m,$r)
	{

		$ReCode = cache('reCode'.$m);
		
		if ($ReCode != null && $r == $ReCode) {
			return 1;
		}else{
			return 0;
		}
	}

}
