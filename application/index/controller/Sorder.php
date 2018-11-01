<?php
namespace app\index\controller;
use think\Loader;
use think\Db;
use app\index\controller\Base;
class Sorder extends Base
{
    public function index()
    {
    	// parent::Check();
    	// parent::CheckAdminLogin();
		// $data = AddressModel::GetAll();
		return $this->fetch('songshuiyuan/index',[
			'list'=>null,	
		]);
    }

    public function OrderList()
    {
    	// parent::Check();
    	// parent::CheckAdminLogin();
		// $data = AddressModel::GetAll();
		return $this->fetch('songshuiyuan/s_order',[
			'list'=>null,	
		]);
    }


    public function TOrderList()
    {
    	// parent::Check();
    	// parent::CheckAdminLogin();
		// $data = AddressModel::GetAll();
		return $this->fetch('songshuiyuan/t_order',[
			'list'=>null,	
		]);
    }

    public function SOrderList()
    {
        return $this->fetch('songshuiyuan/s_q_order',[
            'list'=>null,   
        ]);
        
    }





    
}
