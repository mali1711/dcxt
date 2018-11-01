<?php
namespace app\admin\controller;
use think\Loader;
use think\Db;
use app\admin\controller\Base;

class Index extends Base
{
    public function index()
    {	
    	parent::CheckAdminLogin();
    	return $this->fetch('Index');
    }

    public function add()
    {
    	return $this->fetch('Index/add');
    }
}
