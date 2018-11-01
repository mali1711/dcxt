<?php 
namespace app\api\controller\v1;
/**
* 交流互动接口
*/
use think\Controller;
use think\Db;
use app\admin\model\Interactive as InteractiveModel;
use app\admin\model\Praise as PraiseModel;
use app\api\controller\v1\Base;
class Interactive extends Base
{
	function __construct($cache_time=0,$article='',$userid='')
	{
		//缓存时间
		$this->cache_time = $cache_time;
	}

    /**
     * 用户添加评论
     */
	public function AddComment()
    {

    }

    /**
     * 执行点赞
     */
    public function GiveLike($article,$userid)
    {
        try{
            self::CheckPraseData($article,$userid);
            $where = ['uid'=>$userid, 'wid'=>$article];
            $result = PraiseModel::GetWhereOne($where);
            if (!$result){
                $data = ['uid'=>$userid, 'wid'=>$article, 'create_time'=>time()];
                if (PraiseModel::AddData($data)){
                    echo json_encode(['code'=>1001,'meg'=>'成功']);
                }else{
                    throw new \Exception('失败');
                }
            }else{
                throw new \Exception('重复点赞');
            }
        }catch (\Exception $e){
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }
    }

    /**
     * 取消点赞
     */
    public function UnGiveLike($article,$userid)
    {
        try{
            self::CheckPraseData($article,$userid);
            $where = ['uid'=>$userid, 'wid'=>$article];
            //根据条件查找数据
            $result = PraiseModel::GetWhereOne($where);
            if ($result){
                if(PraiseModel::DeleteData($result['id'])){
                    echo json_encode(['code'=>1001,'meg'=>'取消成功']);
                }else{
                    throw new \Exception('取消失败');
                }
            }else{
                throw new \Exception('故障');
            }
        }catch (\Exception $e){
            echo json_encode(['code'=>1025,'meg'=>$e->getMessage()]);
        }
    }


    private function CheckPraseData($article,$userid){
        if ($article == null || !intval($article)){
            throw new \Exception('文章标识不合法');
        }
        if ($userid == null || !intval($userid)){
            throw new \Exception('用户身份不合法');
        }
        return true;
    }
	/**
	 * 交流互动列表
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 */
	public function InterList()
	{
        $interactive = Db::name('interactive')->select();
        $praise      = Db::name('praise')->select();
        foreach ($interactive as &$inter){
            $inter['pra'] = [];
            foreach ($praise as $value){
                if ($value['wid'] == $inter['id']){
                    $inter['pra'][] = $value['uid'];
                }
            }
        }
        echo json_encode($interactive);
	}
	/**
	 * 交流互动详情
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 */
	public function InterInfo()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			//查找阅读量
			$data = InteractiveModel::GetOne($_GET['id']);
			echo json_encode($data);
		}else{
			echo json_encode(["code"=>1026,"message"=>"缺少文章id"]);
		}
	}
	/**
	 * 添加阅读量
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 */
	public function ReadingAdd()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			//查找阅读量
			$result = InteractiveModel::GetOne($_GET['id']);
			if ($result) {
				$data['reading'] = $result['reading']+1;
				InteractiveModel::UpdateData($_GET['id'],$data);
				echo json_encode(["code"=>1001,"message"=>"文章加+1"]);
			}
		}else{
			echo json_encode(["code"=>1025,"message"=>"少了一个重要的ID"]);
		}
	}
	/**
	 * 添加点赞量
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 * 参数 @todo wid 文章id  uid 点赞用户的id
	 */
	public function PraiseAdd()
	{
		try {
			$uid = $_POST['uid'];
			$wid = $_POST['wid'];
			self::CheckData($uid,$wid);
			$_POST['create_time'] = time();
			if(PraiseModel::AddData($_POST)){
				self::PraAdd($wid);
			}else{
				throw new \Exception("点赞失败");	# code...
			}
		} catch (\Exception $e) {
			echo json_encode(["code"=>1025,"message"=>$e->getMessage()]);
		}
		//PraiseModel::AddData($data);
	}

	private function CheckData($uid,$wid)
	{
		if (empty($uid) || empty($wid)) {
			throw new \Exception("点赞失败,缺少重要参数");	# code...
		}
	}
	/**
	 * 数据加+
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-05
	 * @Email    carlos0608@163.com
	 */
	private function PraAdd($id)
	{
		$result = InteractiveModel::GetOne($id);
		if ($result) {
			$data['praise'] = $result['praise']+1;
			InteractiveModel::UpdateData($id,$data);
			echo json_encode(["code"=>1001,"message"=>"点赞加+1"]);
		}else{
			throw new \Exception("点赞失败,并没有增加一个");	# code...
		}
	}
}
?>