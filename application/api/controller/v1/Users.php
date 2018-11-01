<?php 
namespace app\api\controller\v1;
/**
* 用户接口
*/
use think\Controller;
use think\Db;
use app\admin\model\User as UserModel;
class Users extends Controller
{
	function __construct($cache_time=180,$Pay_time=54000,$goodsid='')
	{
		//缓存时间
		$this->cache_time = $cache_time;
		//支付时间缓存
		$this->Pay_time = $Pay_time;
	}

	/**
	 * 商家信息列表展示
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function UserInfoList()
	{
		$res = false;//cache('Shang_info');
		
		$where = [];

		$flag = $_GET['flag'];

		if ($flag) {
			$where['flag'] = $flag;
		}

		if ($res === false) {
			
			$Shang_info = Db::name('info_show')
                            ->where("status = 1")
                            ->order('id','asc')
                            ->where($where)
                            ->select();
            // dump(Db::name('info_show')->getLastSql);
			//把数据打入缓存文件中
			cache('Shang_info', json_encode($Shang_info), $this->cache_time);
			echo json_encode($Shang_info);
		}else{
			echo $res;
		}
		
	}

	//商家详情信息
	/**
     * @param 商家的详情Id
     */
	public function ShangInfo($Sid)
	{
		try {
			if ($Sid == null) {
                throw new \Exception("商家id不存在");	# code...
			}

            $result = Db::name('info_show')->find($Sid);
            //dump(UserModel::getLastSql());
			if ($result){

                //$goodsimg = explode(',',$result['goods_img']);
                //$result->goods_img = $goodsimg;
                echo json_encode(['code'=>1001,'meg'=>'获取成功','data'=>$result]);
            }else{
			    throw new \Exception('数据未找到');
            }
		} catch (\Exception $e) {
			echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
		}
	}


}