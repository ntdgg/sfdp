<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use think\Controller;
use think\facade\Session;
use workflow\workflow;

class Index  extends Controller{
    public function index(){
	
	  $this->assign('menu',db('menu')->select());
      return $this->fetch();
    }
	public function welcome(){
	 
      return $this->fetch();
    }
	public function doc(){
      return $this->fetch();
    }
}