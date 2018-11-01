<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;

class Comments extends Base
{
	/**
	 * [index description]
	 * @Author   CarLos(wang)
	 * @DateTime 2018-06-11
	 * @Email    carlos0608@163.com
	 * @return   [type]             [description]
	 */
    public function index()
    {	
    	parent::CheckAdminLogin();
    	return $this->fetch('Comments/index');
    }
}
