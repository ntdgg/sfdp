<?php
namespace sfdp;

use think\Request;
use think\Db;
use think\view;

class Api
{
	/*构建表单目录*/
	static function sdfp_menu(){
		$menu  =  Db::name('fb')->select();
		$menu_html = '';
		foreach($menu as $k=>$v){
			$menu_html .='<li><a data-href="'.url('sfdp/list',['sid'=>$v['id']]).'" data-title="test" href="javascript:void(0)">'.$v['title'].'</a></li>';
		}
		return $menu_html;
	}
	/*动态生成列表*/
	public function lists($sid)
	{
		return view(env('root_path') . 'extend/sfdp/template/index.html',['sid'=>$sid]);
	}
	/*动态生成表单*/
	public function add($sid)
	{
		$json = db('fb')->find($sid);
		return view(env('root_path') . 'extend/sfdp/template/edit.html',['data'=>$json['ziduan']]);
	}
}
