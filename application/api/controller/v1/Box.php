<?php 
namespace app\api\controller\v1;
/**
* 商品列表接口
*/
use think\Controller;
use think\Db;
use app\admin\model\Classification as ClassificationModel;
use app\admin\model\Goods as GoodsModel;
use app\api\controller\v1\Base;
class Box extends Base
{
	function __construct($cache_time=180,$Pay_time=54000)
	{
		//缓存时间
		$this->cache_time = $cache_time;
		//支付时间缓存
		$this->Pay_time = $Pay_time;
	}
	/**
	 * 礼盒分类缓存
	 * @Author   CarLos(翟)
	 * @DateTime 2018-06-21
	 * @Email    carlos0608@163.com
	 */
	public function Boxcla()
	{
		//获取缓存
		$boxcla = cache('boxcla');
		//验证缓存
		if ($boxcla === false) {
			$data = ClassificationModel::GetBoxDataAll();
			cache('boxcla',json_encode($data),$this->cache_time);
			return json_encode($data);
		}else{
			echo $boxcla;
		}
	}	
	/**
	 * 礼盒列表
	 * @Author   CarLos(翟)
	 * @DateTime 2018-06-21
	 * @Email    carlos0608@163.com
	 */
	public function Boxinfo()
	{
		//读取缓存
		$boxinfo = cache('boxinfo');
		//验证缓存
		if ($boxinfo === false) {
			//查询数据
			$Goods = Db::name('goods')->where("is_type=0")->order('recommend_time','desc')->select();
			$Classifi = Db::name('classification')->where('is_type=0')->select();
			//拼接数据
			foreach ($Classifi as &$classinfo) {
				$classinfo['goods'] = [];
				foreach ($Goods as $value) {
					if ($value['cid'] == $classinfo['id']) {
						$classinfo['goods'][] = $value;
					}
				}
			}
			//把数据打入缓存文件中
			cache('boxinfo', json_encode($Classifi), $this->cache_time);
			//返回输出值
			echo json_encode($Classifi);
		}else{
			//返回缓存值
			echo $boxinfo;
		}
	}

	public function	Boxrem()
	{
		$boxrem = cache('boxrem');
		if ($boxrem === false) {
			$Goods = Db::name('goods')->where("is_type=0")->order('recommend_time','desc')->select();
			//把数据打入缓存文件中
			cache('boxrem', json_encode($Goods), $this->cache_time);
			//返回输出值
			echo json_encode($Goods);
		}else{
			echo $boxrem;
		}
		
	}
}
?>