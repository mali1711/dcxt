<?php 
namespace app\api\controller\v1;
/**
* 桶租金接口
*/
use think\Controller;
use think\Db;
use app\admin\model\Bucket as BucketModel;
use app\api\controller\v1\Base;
class Bucket extends Base
{
	/**
	 * 通租金列表
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function getList()
	{

		$uid = $_GET['uid'];
		if (!$uid) {
			echo json_encode(['code'=>'1025','meg'=>'参数错误','data'=>null]);
		}

		$order  = ['bStatus' => 'desc','createTime' => 'desc','id' => 'desc'];
		$data = BucketModel::where(['status'=>1, 'uid'=>$uid])->order($order)->select();//getALl();
	
		$list = collection($data)->toArray();

		if ($list) {
			foreach ($list as $key => $value) {
				# code...
				$list[$key]['day']  = (int)(($value['validity'] - time()) / 60 / 60 /24);

				$list[$key]['key']  = $key;

				$list[$key]['selected']  = 0;
				
				$list[$key]['time'] = date('Y-m-d H:i:s',$value['createTime']);		
			}
		}

		echo json_encode(['code'=>'1001','meg'=>'获取成功','data'=>$list]);
	}

	public function bucketInfo()
	{
		
		if (!isset($_GET['uid']) || !$_GET['uid']) {
			echo json_encode(['code'=>'1025','meg'=>'参数错误','data'=>null]);
		}

		
		if ((!isset($_GET['id']) || !$_GET['id'])) {
			echo json_encode(['code'=>'1025','meg'=>'参数错误','data'=>null]);
		}

		$uid = $_GET['uid'];

		$id  = $_GET['id'];

		$info = BucketModel::where(['id'=>$id,'uid'=>$uid])->find();//getALl();

		if ($info) {

			# code...
			$info['day']  = (int)(($info['validity'] - time()) / 60 / 60 /24);
			
			$info['time'] = date('Y-m-d',$info['createTime']);		

			echo json_encode(['code'=>'1001','meg'=>'获取成功','data'=>$info]);
	
		} else {
			echo json_encode(['code'=>'1025','meg'=>'获取失败','data'=>null]);
		}

		


	}
}

?>