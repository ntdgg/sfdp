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
		$menu  =  Db::name('sfdp_design')->select();
		$menu_html = '';
		foreach($menu as $k=>$v){
			$menu_html .='<li><a data-href="'.url('sfdp/list',['sid'=>$v['id']]).'" data-title="'.$v['title'].'">'.$v['title'].'</a></li>';
		}
		return $menu_html;
	}
	/*动态生成列表*/
	public function lists($sid)
	{
		$build = db('sfdp_design')->find($sid);
		$field = json_decode($build['ziduan'],true);
		$list_field = json_decode($build['list'],true);
		$topicid = ''; //变量赋值为空
			//用foreach 遍历下二维数组
			foreach($list_field as $key=>$vals){
				$topicid.=$vals['tpfd_db'].',';
				$topicname[$vals['tpfd_db']]=$vals['tpfd_db'];
			}
			$topicid = rtrim($topicid, ',');
		$list = db($field['name_db'])->field($topicid)->paginate('10');
		return view(env('root_path') . 'extend/sfdp/template/index.html',['sid'=>$sid,'list'=>$list,'field'=>$topicname]);
	}
	/*动态生成表单*/
	public function add($sid)
	{
		$json = db('sfdp_design')->find($sid);
		return view(env('root_path') . 'extend/sfdp/template/edit.html',['data'=>$json['ziduan']]);
	}
	public function sfdp($sid=''){
		$data = Db::name('sfdp_design')->paginate('10')->each(function($item, $key){
				$item['fix'] = Db::name('sfdp_design_ver')->where('sid',$item['id'])->order('id desc')->select();
				return $item;
			});
		
		//dump($data);
		return view(env('root_path') . 'extend/sfdp/template/sfdp.html',['list'=>$data]);
	}
	/**
     * 表单设计
     * @return mixed
     */
    public function sfdp_desc($sid)
    {
	   $info = db('sfdp_design')->find($sid);;
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
		db('sfdp_design')->update($data);;
		return json(['code'=>0]);
	}
	public function sfdp_db($sid){
		$info = db('sfdp_design')->find($sid);
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
	public function sfdp_fix($sid){
		 $sfdp = new sfdp();
		$info = db('sfdp_design')->find($sid);
		$json = json_decode($info['ziduan'],true);
		$ver = [
			'sid'=>$sid,
			's_bill'=>OrderNumber(),
			's_name'=>$json['name'],
			's_db'=>$json['name_db'],
			's_list'=>$info['list'],
			's_search'=>$info['chaxun'],
			's_fun_ver'=>'',
			's_field'=>$info['ziduan'],
			'add_user'=>1,
			'status'=>1,
			'add_time'=>time()
		];
		
		$id  =  Db::name('sfdp_design_ver')->insertGetId($ver);
		db('sfdp_design_ver')->where('id','<>',$id)->where('sid',$sid)->update(['status'=>0]);;
		return json(['code'=>0]);
	}
}
