<?php
namespace sfdp;

use think\Request;
use think\Db;
use think\view;
use sfdp\sfdp;

class Api
{
	/*构建表单目录*/
	static function sdfp_menu(){
		$menu  =  Db::name('fb')->select();
		$menu_html = '';
		foreach($menu as $k=>$v){
			$menu_html .='<li><a data-href="'.url('sfdp/list',['sid'=>$v['id']]).'" data-title="'.$v['title'].'">'.$v['title'].'</a></li>';
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
	public function sfdp($sid=''){
		$data = Db::name('fb')->paginate('10');
		
		return view(env('root_path') . 'extend/sfdp/template/sfdp.html',['list'=>$data]);
	}
	/**
     * 表单设计
     * @return mixed
     */
    public function sfdp_desc($sid)
    {
	   $info = db('fb')->find($sid);;
      return view(env('root_path') . 'extend/sfdp/template/sfdp_desc.html',['json'=>$info['ziduan'],'fid'=>$info['id']]);
    }
	public function sfdp_save(){
		$data = input('post.');
		$search = [];
		$list = [];
		$data['ziduan'] = htmlspecialchars_decode($data['ziduan']);
		$field = json_decode($data['ziduan'],true);
		foreach($field['list'] as $k=>$v){
			foreach($v['data'] as $v2){
				if(isset($v2['tpfd_chaxun'])&&($v2['tpfd_chaxun']=='yes')){
					$search[] = $v2;
				}
				if(isset($v2['tpfd_list'])&&($v2['tpfd_list']=='yes')){
					$list[] = $v2;
				}
			}
		}
		$data['chaxun']=json_encode($search); 
		$data['list']=json_encode($list); 
		db('fb')->update($data);;
		return json(['code'=>0]);
	}
	public function sfdp_db($sid){
		$info = db('fb')->find($sid);
		$field = json_decode($info['ziduan'],true);
		foreach($field['list'] as $k=>$v){
			foreach($v['data'] as $v2){
				if(isset($v2['tpfd_db'])){
					$search[] = $v2;
				}
			}
		}
		
		 $sfdp = new sfdp();
		 $sfdp->makedb($field['name_db'],$search);
		
	}
}
