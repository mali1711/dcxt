<?php 
namespace app\api\controller\v1;
/**
* 帮助中心接口
*/
use think\Controller;
use think\Db;
use app\admin\model\Search as SearchModel;
use app\api\controller\v1\Base;
class Search extends Base
{
	/**
	 * 帮助中心列表
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
	public function getList()
	{
		$order  = ['order' => 'desc','id' => 'desc'];
		$data = SearchModel::where('status',1)->order($order)->select();//getALl();
	
		$list = collection($data)->toArray();

		return json_encode($list);
	}

	
}

?>