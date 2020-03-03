<?php
//------------------------
// 自动生成代码
//-------------------------

namespace app\index\controller;

use app\common\controller\admin;
use think\Config;
use think\Controller;
use think\Loader;
use think\Url;
use think\Db;
use tpdf\tpdf;

class Fd extends Admin
{
	 public function initialize()
    {
        parent::initialize();
        $this->tpdf = new tpdf();
		$this->uid = session('uid');
	    $this->role = session('role');
    }
    /**
     * 首页
     * @return mixed
     */
    public function index($map=[])
    {
        if ($this->request->param("title")) $map[] = ['title','like',"%" . $this->request->param("title") . "%"];
        $list=controller('Base', 'event')->commonlist('fb',$map);
		$this->assign('list', $list);
        return $this->view->fetch();
    }
	/**
     * 表单设计
     * @return mixed
     */
    public function desc($map=[])
    {
		if ($this->request->isPost()) {
			$data = input('post.');
			$data['ziduan'] = htmlspecialchars_decode($data['ziduan']);
			$ret = $this->tpdf->FbApi('Save',$data['id'],$data);
			if($ret['status']==1){
				return msg_return('修改成功！');
				}else{
				return msg_return($ret['msg'],1);
			}
	   }
	   $info = $this->tpdf->FbApi('Desc',input('id'));
	   $this->assign('ziduan',$info['field']);
	   $this->assign('fid', $info['fid']);
        return $this->view->fetch();
    }
	/**
     * 表单设计修改
     * @return mixed
     */
	public function edit_desc()
	{
		$info = $this->tpdf->FbApi('Edit',input('id'));
		$this->assign('info',$info['info']);
		$this->assign('ziduan',$info['info']['field']);
		$this->assign('fid', input('id'));
		return $this->view->fetch('desc');
	}
	/**
     * 设计预览
     * @return mixed
     */
	public function dsec_view()
	{
		$this->tpdf->make($this->tpdf->FbApi('Bview',input('id')),'demo');
		return $this->view->fetch('demo/view');
	}
	/**
     * 首页
     * @return mixed
     */
    public function add($map=[])
    {
        if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('fb',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
        return $this->view->fetch();
    }
	
	public function functions()
	{
		if ($this->request->isPost()) {
		$data = input('post.');
		$ret=controller('Base', 'event')->commonadd('fb_fun',$data);
	    if($ret['code']==0){
			return msg_return('发布成功！');
			}else{
			return msg_return($ret['data'],1);
		}
	   }
	   $this->assign('fid', input('id'));
        return $this->view->fetch();
		
	}
	public function ajax_sql(){
		if ($this->request->isPost()){
            $sql=input("post.sql");
			$title=input("post.title");
			try{
			 $data = Db::query($sql);
			 $html = '<select><option value=>请选择'.$title.'</option>';
			 foreach($data as $k=>$v){
				 $html .= '<option value="'.$v["id"].'">'.$v["name"].'</option>';
			 }
			 $html .= '</select>';
			   echo  $html;
			}catch(\Exception $e){
				return  1; 	
			}
        }
	}
	public function shengcheng()
	{
		$data = $this->tpdf->FbApi('Bview',input('id'),'Act');
		$this->tpdf->make($data);
		controller('Base', 'event')->commonedit('fb',['id'=>input('id'),'status'=>1]);
		controller('Base', 'event')->commonadd('menu',['url'=>$data['controller'].'/index','name'=>$data['title']]);
		$this->success('生成成功！','/index/index/welcome');
	}
}
