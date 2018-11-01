<?php
namespace app\service;
use think\Controller;
/**
*
*/
class WechatQrcode extends Controller
{
	
	public function __construct()
	{
		# code...
	}
    /**
     * 生成二维码
     * @Author   CarLos(wang)
     * @DateTime 2018-06-19
     * @Email    carlos0608@163.com
     * @param    string             $url [description]
     * @return   [type]                  [description]
     */
	public static function getWchatQrcode($url,$user_id,$data=[],$level=3,$size=4)
	{ 
        //加载二维码生成插件
        Vendor('phpqrcode.phpqrcode'); 
        //二维码存放路径以及文件名称
        $filepath = 'qrcode/Wechat_user_'.$user_id.'.png';
        //二维码校验
        if (file_exists($filepath)) {
            return $filepath;//json_encode(['code'=>1025,'meg'=>'该用户已经生成过专属二维码了']);
        }
        //拼接二维码地址
        $str = '';
        foreach ($data as $key => $value) {
            $str .= $key.'='.$value.'&';
        }
        //拼接完整的url地址
        $url .= '?'.$str;
        //容错级别 
        $errorCorrectionLevel =intval($level) ;
        //生成图片大小 
        $matrixPointSize = intval($size);
        //实例化gd库扩展 
        $object = new \QRcode();
        //生成实际的二维码
        $result = $object->png($url,$filepath, $errorCorrectionLevel, $matrixPointSize, 2,false);
        //返回具体信息
        return $filepath;
        // return json_encode(['code'=>1001,'meg'=>'生成专属二维码成功','path'=>$filepath]); 
    }

}