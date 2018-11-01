<?php
namespace app\lib\exception;

/**
 * token验证失败时抛出此异常 
 */
use think\Exception;
use think\exception\Handle;
class ExceptionHandler extends Handle
{
    // public $code = 401;
    // public $msg = 'Token已过期或无效Token';
    // public $errorCode = 10001;
    //内核的rander方法
	private $code;
	private $msg;
	private $errorCode;

	public function render(Exception $e){
		if ($e instanceof BaseException) {
			# 如果是自定义的异常
			$this->code = $e->code;
			$this->msg = $e->msg;
			$this->errorCode = $e->errorCode;
		}else{
			$this->code=500;
			$this->msg = '服务器错误请联系后台人员';
			$this->errorCode = 999;
		}
		//返回的结果
		$result = [
			'msg' =>$this->msg,
			'errorCode'=>$this->errorCode,
		];
		return json($result,$this->code);
	}
}