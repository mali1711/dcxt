<?php 
namespace app\lib\exception;
//异常处理的基类
use think\Exception;
class BaseException extends Exception
{
	//http 状态码
	public $code=400;
	//错误的具体信息
	public $msg='参数错误';
	//自定义的错误码
	public $errorCode = 10000;

}

 ?>