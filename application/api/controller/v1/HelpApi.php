<?php 
namespace app\api\controller\v1;
/**
* 帮助中心接口
*/
use think\Controller;
use think\Db;
use app\admin\model\Help as HelpModel;
use app\api\controller\v1\Base;
class HelpApi extends Base
{
	/**
	 * 帮助中心列表
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function getHelpList()
	{
		$data = HelpModel::GetAllAPi();
		echo json_encode($data);
	}

	/**
	 * 平台中心列表
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function getMessageList($type,$uid)
	{
		$data = HelpModel::getMessageList($type,$uid);

		echo json_encode($data);
	}

	/**
	 * 平台中心列表
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function MessageNumber($uid)
	{
		$data = HelpModel::MessageNumber($uid);
		echo json_encode($data);
	}




	/**
	 * 平台消息状态
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function setMessage()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			//查找消息
			$result = HelpModel::GetOne($_GET['id']);
			if ($result) {
				$data['status'] = 1;
				HelpModel::UpdateData($_GET['id'],$data);
				echo json_encode(["code"=>1001,"message"=>"ok"]);
			}
		}else{
			echo json_encode(["code"=>1025,"message"=>"参数错误"]);
		}
	}
	
}

?>