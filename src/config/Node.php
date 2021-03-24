<?php


use think\facade\Db;
/**
 *+------------------
 * Gadmin 开源后台系统
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
class Node {
	
	public function SaveNode($data,$node){
		$top_id =  Db::name('node')->where('sid',$data['sid'])->find();
		/*增加当设计器重新设计的时候更新Node表中的sid*/
		if($top_id){
			return ['code'=>1,'msg'=>'对不起该栏目已经生成！'];
		 }
		
		$HasNode =  Db::name('node')->where('name',ucfirst($data['db_name']))->find();
		if($HasNode){
			//存在，直接更新所有栏目
			$ret = Db::name('node')->where('id',$HasNode['id'])->update(['sid'=>$data['sid'],'url'=>'sfdp/index?sid='.$data['sid']]);
			if(!$ret){
				return ['code'=>1,'msg'=>'更新主栏目出错'];
			 }
		 }
		
		 $node_top = ['url'=>'sfdp/index?sid='.$data['sid'],'name'=>ucfirst($data['db_name']),'sid'=>$data['sid']];
		 $top_id =  Db::name('node')->insertGetId($node_top);
		
		 if(!$top_id){
			return ['code'=>1,'msg'=>'err'];
		 }
		 return ['code'=>0,'msg'=>'err'];
	}
	public function GetNode(){
		$main_menu = Db::name('node')->select()->toArray();
		$html = '';
		foreach($main_menu as $k=>$v){
			$html .= '<option value="'.$v['id'].'">'.$v['name'].'</option>';
			
		}
		$html .= '';
		
		return ['html'=>$html,'data'=>$main_menu];
	}
}
?>