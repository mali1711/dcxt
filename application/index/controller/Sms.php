<?php
namespace app\index\controller;
use think\Loader;
use think\Db;
use app\index\controller\Base;
use app\index\model\User as UserModel;
/**
 * 发送短信
 * @Author   CarLos(wang)
 * @DateTime 2018-06-08
 * @Email    carlos0608@163.com
 */
class SMS extends \think\Controller
{
    public function SMS($mobile)
    {
        $code = rand(1000,9999);
        // dump(cache('reCode'.$mobile));die;
        try{
            if ($mobile == null){
                throw new \Exception('手机号为空');
            }
            if (!preg_match("/^1[34578]{1}\d{9}$/",$mobile)){
                throw new \Exception('手机号不正确');
            }
// return self::waterSMS($code,$mobile);
            $result = json_decode(self::waterSMS($code,$mobile));
            
            $code = $result;
            if ($code->respCode == '00000'){

                return ['code'=>1001,'msg'=>config('SmsCode.'.$code->respCode)];
            }else{
                $res = config('SmsCode.'.$code->respCode);

                if($res == null){
                    return ['code'=>1027,'msg'=>'请联系平台运营人员'];
                }else{
                    return ['code'=>1026,'msg'=>config('SmsCode.'.$code->respCode)];
                }
            }
        }catch (\Exception $e){
            return ['code'=>1025,'msg'=>$e->getMessage()];
        }

    }

    protected static function waterSMS($code,$mobile)
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

    private static function sendSMS($url,$code,$headers,$fields_string,$mobile)
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
    