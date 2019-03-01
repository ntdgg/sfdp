<?php
namespace app\index\controller;

use app\common\controller\admin;
use think\Request;
use think\Db;

class Newss extends Admin
{
	public function index($map='')
	{
		
		$list=controller('Base', 'event')->commonlist('Newss',$map);
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function edit()
	{
		if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonedit('Newss',$data);
	    if($ret['code']==0){
			return msg_return('修改成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
		$this->assign("type",Db::query("SELECT id,type as name FROM `wf_news_type`"));
		$this->assign("type2",Db::query("SELECT id,type as name FROM `wf_news_type`"));
		
		$this->assign('vo', db('Newss')->find(input('id')));
		return $this->fetch();
	}
	public function add()
	{
	if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('Newss',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
	    $this->assign("type",Db::query("SELECT id,type as name FROM `wf_news_type`"));
		$this->assign("type2",Db::query("SELECT id,type as name FROM `wf_news_type`"));
		
		return $this->fetch('edit');
	}
	public function view()
	{
		$this->assign('vo', db('Newss')->find(input('id')));
		return $this->fetch();
	}
}
