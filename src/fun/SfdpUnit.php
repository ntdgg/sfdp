<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 单元类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */

namespace sfdp\fun;

use sfdp\adaptive\Design;


class SfdpUnit{
	/**
     * 创建数据表
     */
    public static function Bmenu()
    {
		$menu = Design::getDesignVer();
		$menu_html = '';
		foreach($menu as $k=>$v){
			$menu_html .='<li><a data-href="'.url('sfdp/sfdpCurd',['act'=>'index','sid'=>$v['id']]).'" data-title="'.$v['s_name'].'">'.$v['s_name'].'</a></li>';
		}
		return $menu_html;
    }
	/**
	 * 构件查询条件
	 */
	public static function Bsearch($search){
		$map =[];
		if(isset($search['search']) && count($search)<>0){
			
			$search_field = $search['search'];
			foreach($search_field as $k=>$v){
				if($v <>''){
					$map[$k] = ['=',$v];
				}
			}
		}
		return $map;
	}
	/**
	 * 加载文件
	 */
	public static function Loadfile($db_name,$css,$js){
		$js_str = '';
		if($js!=''){
			$js_arry = explode("@@",$js);
			foreach($js_arry as $v){
				$js_str .= '<script src="\static/sfdp/user-class/'.$db_name.'/'.$v.'"></script>';
			}
		}
		$css_str = '';
		if($css!=''){
			$css_arry = explode("@@",$css);
			foreach($css_arry as $v){
				$css_str .= '<link rel="stylesheet" href="\\static/sfdp/user-class/'.$db_name.'/'.$v.'" />';
			}
		}
		return ['js'=>$js_str,'css'=>$css_str];
	}
}