<?php 
namespace app\api\controller\v1;
use think\Controller;
use think\Db;
use app\admin\model\Banner as BannerModel;
use app\admin\model\Config as ConfigModel;
use app\api\controller\v1\Base;
/**
 * 配置文件接口
 */
class Conf extends Base
{
	function __construct($banner_cache_time=54000)
	{
		//缓存时间 banner的缓存时间放在十五分钟
		$this->banner_cache_time = $banner_cache_time;
	}
	/**
	 * 轮播图接口
	 * @Author   CarLos(翟)
	 * @DateTime 2018-06-20
	 * @Email    carlos0608@163.com
	 */
	public function BannerList()
	{
		$Banner = cache('banner');
		if ($Banner === false) {
			$data = BannerModel::GetAllApi();
			cache('banner',json_encode($data),$this->banner_cache_time);
			return json_encode($data);
		}else{
			return $Banner;
		}
	}

    /**
     * 交流互动列表
     */
	public function ProList()
	{
		$Banner = cache('probanner');
		if ($Banner === false) {
			$data = BannerModel::GetProAllApi();
			cache('probanner',json_encode($data),$this->banner_cache_time);
			return json_encode($data);
		}else{
			return $Banner;
		}
	}

	/**
     * 公共配置
     */
	public function Config()
	{
		
		$data = ConfigModel::GetConAll();
		return json_encode($data);

	}
}
?>