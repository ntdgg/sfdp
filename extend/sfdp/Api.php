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
		/* if ($this->request->isPost()) {
			$data = input('post.');
			$data['ziduan'] = htmlspecialchars_decode($data['ziduan']);
			$ret = $this->sfdp->FbApi('Save',$data['id'],$data);
			if($ret['status']==1){
				return msg_return('修改成功！');
				}else{
				return msg_return($ret['msg'],1);
			}
	   } */
	   $info = db('fb')->find($sid);;
      return view(env('root_path') . 'extend/sfdp/template/sfdp_desc.html',['ziduan'=>$info['ziduan'],'fid'=>$info['id']]);
    }
}
