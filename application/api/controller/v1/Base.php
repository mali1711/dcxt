<?php 
namespace app\api\controller\v1;
/**
* 用户接口
*/
use think\Controller;
use think\Db;
class Base extends Controller
{	
	// 判断会员等级 进行操作

	protected function waterSMS($code,$mobile)
	{
	    $url="https://api.miaodiyun.com/20150822/industrySMS/sendSMS";
	    $headers[] = 'Content-type:application/x-www-form-urlencoded';
	    $data['accountSid'] = config('accountSid');
        $data['smsContent'] = '【集集科技】您的验证码为'.$code.'，请于3分钟内正确输入，如非本人操作，请忽略此短信。';
	    $data['to'] = $mobile;
	    $data['timestamp'] =date("YmdHis");
	    $AUTHTOKEN = config('AUTHTOKEN');
	    $str = $data['accountSid'].$AUTHTOKEN.$data['timestamp'];
	    $data['sig'] = md5($str);
	    $fields_string = "";
	    foreach($data as $key=>$value){
	        $fields_string .= $key.'='.$value.'&';
	    }
		$fields_string = rtrim($fields_string, '&');
		$result = self::sendSMS($url,$code,$headers,$fields_string,$mobile);
		
		return $result;
	}

	private function sendSMS($url,$code,$headers,$fields_string,$mobile)
	{
	    $con = curl_init();
	    curl_setopt($con, CURLOPT_URL, $url);
	    curl_setopt($con, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($con, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($con, CURLOPT_HEADER, 0);
	    curl_setopt($con, CURLOPT_POST, 1);
	    curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($con, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($con, CURLOPT_POSTFIELDS, $fields_string);
	    $result = curl_exec($con);
	    curl_close($con);
	    cache('reCode'.$mobile,$code,180);
	    return $result;
	}
}

?>