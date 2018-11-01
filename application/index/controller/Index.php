<?php
namespace app\index\controller;
use think\Loader;
use think\Db;
use app\index\controller\Base;
class Index extends Base
{
    public function index()
    {
    	// parent::Check();
    	// parent::CheckAdminLogin();
		// $data = AddressModel::GetAll();
        // $this->assign('active', 'active'); 
		return $this->fetch('songshuiyuan/index',[
			'list'=>null,
		]);
    }

    public function Song()
    {
    	// parent::Check();
    	// parent::CheckAdminLogin();
		// $data = AddressModel::GetAll();

		return $this->fetch('songshuiyuan/index',[
			'list'=>null,
		]);
    }
}
