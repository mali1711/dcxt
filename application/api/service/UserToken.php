<?php 
namespace app\api\service;
/**
* 小程序用户登录界面
*/
use app\index\model\User as UserModel;
use app\lib\exception\WeChatException;
use app\lib\exception\TokenException;
class UserToken
{
	protected $code;
    protected $wxLoginUrl;
    protected $wxAppID;
    protected $wxAppSecret;

    function __construct($code,$nickName,$avatarUrl)
    {
        $this->code = $code;
        $this->nickName = $nickName;
        $this->avatarUrl = $avatarUrl;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(
            config('wx.login_url'), $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    public function get()
    {
        $result = curl_get($this->wxLoginUrl);
        // 注意json_decode的第一个参数true
        // 这将使字符串被转化为数组而非对象

        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            // 为什么以empty判断是否错误，这是根据微信返回
            // 规则摸索出来的
            // 这种情况通常是由于传入不合法的code
            throw new Exception('获取session_key及op上enID时异常，微信内部错误');
        }
        else {
            // 建议用明确的变量来表示是否成功
            // 微信服务器并不会将错误标记为400，无论成功还是失败都标记成200
            // 这样非常不好判断，只能使用errcode是否存在来判断
            $loginFail = array_key_exists('errcode', $wxResult);
            if ($loginFail) {
                $this->processLoginError($wxResult);
            }
            else {
                return $this->grantToken($wxResult);
            }
        }
    }
    // 处理微信登陆异常
    // 那些异常应该返回客户端，那些异常不应该返回客户端
    // 需要认真思考
    private function processLoginError($wxResult)
    {
        throw new WeChatException(
            [
                'msg' => $wxResult['errmsg'],
                'errorCode' => $wxResult['errcode']
            ]);
    }
    private function grantToken($wxResult)
    {
        $openid = $wxResult['openid'];
        $user = UserModel::getByOpenID($openid);
        if (!$user)
            // 借助微信的openid作为用户标识
            // 但在系统中的相关查询还是使用自己的uid
        {
            $uid = $this->newUser($openid);
        }
        else {
            $uid = $user->id;
        }
        return  $uid;
    }
    // 创建新用户
    private function newUser($openid)
    {
        // 有可能会有异常，如果没有特别处理
        // 这里不需要try——catch
        // 全局异常处理会记录日志
        // 并且这样的异常属于服务器异常
        // 也不应该定义BaseException返回到客户端
        $user = UserModel::create(
            [
                'openId' => $openid,
                'create_time'=>time(),
                'update_time'=>time(),
                'nickname'=>$this->nickName,
                'username'=>$this->nickName,
                'identity'=>1,
                'identification'=>2,
                'level'=>2,
                'validity'=>130,
                'photo'=>$this->avatarUrl
            ]);
        return $user->id;
    }

}

 ?>