<?php 
namespace app\api\controller\v1;
/**
* 用户接口
*/
use think\Controller;
use think\Db;
use app\admin\model\User as UserModel;
use app\api\controller\v1\Login;

class Card extends Controller
{

	// 获取默认的银行卡
	public function GetCardDefault($uid) {

		$res  = Db::name('card')->where(['uid'=>$uid,'is_default'=>1])->field('id,bank,cardNo')->find();

		// dump($res);die;

		return json_encode($res);
	}

	// 获取用户的银行卡
	public function CardInfo($uid)
	{
		// 用户绑定的银行卡
		$Adds = Db::name('card')
                    ->where("uid=$uid")
                    ->order('createTime desc')
                    ->select();
        if ($Adds) {
        	foreach ($Adds as $k => $v) {
        	$Adds[$k]['key'] = $k;
        }
        }
        
		
		echo json_encode($Adds);
	}

	// 修改绑定银行卡详情
	public function CardDetail($id)
	{

		if ($id == 'undefined') {
			$id = 0;
		} 
		// 用户收货地址
		$Adds = Db::name('card')
                    ->where("id=$id")
                    ->find();
        
		echo json_encode($Adds);
	}


	// 设置默认银行卡
	public function SetCardDefault($id, $uid)
	{
		if (!$id && !$uid) {
			echo json_encode(['code'=>'1025','meg'=>'参数错误','data'=>null]);
		}
		try {
			Db::name('card')->where('uid',$uid)->update(['is_default'=>0]);

			Db::name('card')->where('id',$id)->update(['is_default'=>1]);

		} catch (\Exception $e) {
			
			echo json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		
		}

	}

	/**
	 * 添加银行卡 @todo userid  
	 * @Author   CarLos(翟)
	 * @DateTime 2018-06-13
	 * @Email    carlos0608@163.com
	 */
	public function AddCard()
	{	

		try {
			$data = array();
			// id
			if (isset($_GET['uid']) && $_GET['uid']) {
				$data['uid'] = $_GET['uid'];
			}
			// 姓名
			if (isset($_POST['userName']) && $_POST['userName']) {
				$data['userName'] = $_POST['userName'];
			}
			// 电话
			if (isset($_POST['mobile']) && $_POST['mobile']) {
				$data['mobile'] = $_POST['mobile'];
			}
			// 银行开户行
			if (isset($_POST['bank']) && $_POST['bank']) {
				$data['bank'] = $_POST['bank'];
			}
			// 银行卡账号
			if (isset($_POST['cardNo']) && $_POST['cardNo']) {
				$data['cardNo'] = $_POST['cardNo'];
			}
			self::CheckInfo($data);
			
			$re = Db::name('user')->where('id',$data['uid'])->value('mobile');

            if (!$re) {
                throw new \Exception('请您先去绑定手机号码！');
            }

            $userInfo['id']     = $data['uid'];
            $userInfo['mobile'] = $data['mobile'];

            $res    = Db::name('user')->where($userInfo)->find();
            
            if (!$res) {
                 throw new \Exception('该手机号不是您绑定的手机号码！');
            }



			// 验证码验证
		    $l = new Login();
		    	
		    $res = Login::CheckRecodes($_POST['mobile'],$_POST['recode']);

		    if (!$res) {
		    	throw new \Exception("验证码输入错误");# code...
		    }


			if (isset($_GET['id']) && $_GET['id'] && $_GET['id'] != 'undefined') {
				
				$id = $_GET['id'];

				$result = Db::name('card')->where('id', $id)->update($data);

			} else {
				
				$data['createTime'] = time();

				$new = Db::name('card')->where(['uid'=>$data['uid']])->select();

				if (!$new) {
					$data['is_default'] = 1;
				}

				$result = Db::name('card')->insert($data);
			}

 			
			if (!$result) {
				throw new \Exception("操作失败");# code...
			}else{
				echo json_encode(['code'=>'1001','meg'=>'操作成功','data'=>$result]);
			}
		} catch (\Exception $e) {
			echo json_encode(['code'=>'1025','meg'=>$e->getMessage(),'data'=>null]);
		}
	}
	/**
	 * 验证数据
	 * @Author   CarLos(翟)
	 * @DateTime 2018-06-13
	 * @Email    carlos0608@163.com
	 * @param    [type]             $data [description]
	 */
	private function CheckInfo($data)
	{
		if ($data['uid'] == null) {
			throw new \Exception("缺少用户标识");# code...
		}
		if ($data['bank'] == null) {
			throw new \Exception("请填写银行卡类型");# code...
		}
		if ($data['cardNo'] == null) {
			throw new \Exception("请填写银行卡号");# code...
		}
		if ($data['mobile'] == null) {
			throw new \Exception("请填写手机号");# code...
		}
        if (!intval($data['mobile'])) {
            throw new \Exception("手机号有非法字符");# code...
        }
        if (!preg_match("/^1[34578]{1}\d{9}$/",$_POST['mobile'])) {
            throw new \Exception("手机号格式不对");# code...
        }
		if ($data['userName'] == null) {
			throw new \Exception("请填写姓名");# code...
		}
	}
	
    /**
     * @param $address_id  删除用户的银行卡
     */
    public function CardDelete($card_id,$userid)
    {
        // die(12313);
        try{    
            if ($card_id == null || !intval($card_id)){
                throw new \Exception('无效的参数');
            }
            if ($userid == null || !intval($userid)){
                throw new \Exception('用户身份无效');
            }
            $where = [
                'id'  => $card_id,
                'uid' => $userid
            ];
            // self::CheckDefaultCard($where);
            $result = Db::name('card')->where($where)->delete($card_id);
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
     * 检测用户要删除的银行卡是否为默认
     */
    private function CheckDefaultCard($where)
    {
        $result = Db::name('card')->where($where)->find();
        if ($result['is_default'] == 1){
            throw new \Exception('请更换默认银行卡');
        }
        if (!$result){
            throw new \Exception('权限越界');
        }
        return true;
    }











	
}

?>