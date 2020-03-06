<?php
namespace sfdp;

use think\Request;
use think\Db;
use think\view;

class Api
{
	static function sdfp_menu(){
		return '<li><a data-href="'.url('sfdp/list').'" data-title="test" href="javascript:void(0)">111</a></li>';
	}
	public function lists($map='')
	{
		return view(env('root_path') . 'extend/sfdp/template/index.html');
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
		$this->assign('vo', db('Newss')->find(input('id')));
		return $this->fetch();
	}
	public function add()
	{
		$sfdp_id = db('sfdp_link')->where('work_id',2)->value('sfdp_id');
		$json = db('fb')->find($sfdp_id);
		return view(env('root_path') . 'extend/sfdp/template/edit.html',['data'=>$json['ziduan']]);
	}
	public function view()
	{
		$this->assign('vo', db('Newss')->find(input('id')));
		return $this->fetch();
	}
}
