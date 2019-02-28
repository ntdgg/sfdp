<?php
namespace app\index\controller;

use app\common\controller\admin;
use think\Request;

class Demo extends Admin
{
	public function index($map='')
	{
		        if ($this->request->param("demo")) {
            $map['demo'] = ["like", "%" . $this->request->param("demo") . "%"];
        }

		$list=controller('Base', 'event')->commonlist('Demo',$map);
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function edit()
	{
		$list=controller('Base', 'event')->commonedit('Demo');
		$this->assign('vo', $list);
		return $this->fetch();
	}
	public function add()
	{
	if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('Demo',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
		return $this->fetch('edit');
	}
}
