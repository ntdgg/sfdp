<?php
/**
 *+------------------
 * Tpfd 表单控件
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace app\[MODULE]\controller;

use app\common\controller\admin;
use think\Request;
use think\Db;

class [NAME] extends Admin
{
	public function index($map='')
	{
		[FILTER]
		$list=controller('Base', 'event')->commonlist('[NAME]',$map);
		$this->assign('list', $list);
		return $this->fetch();
	}
	public function edit()
	{
		if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonedit('[NAME]',$data);
	    if($ret['code']==0){
			return msg_return('修改成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
		[FUN]
		$this->assign('vo', db('[NAME]')->find(input('id')));
		return $this->fetch();
	}
	public function add()
	{
	if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('[NAME]',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
	    [FUN]
		return $this->fetch('edit');
	}
	public function view()
	{
		$this->assign('vo', db('[NAME]')->find(input('id')));
		return $this->fetch();
	}
}
