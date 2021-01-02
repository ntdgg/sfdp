<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V5.0
 *+------------------
 * Copyright (c) 2018~2020 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace sfdp\service;

use think\Db;

use sfdp\db\DescDb;

class Control{
	
	static function Api($act,$sid=''){
		//获取流程信息
		if($act =='list'){
			$list = Db::name('sfdp_design')->order('id desc')->select();
			return view(ROOT_PATH.'/sfdp.html',['list'=>$list,'patch'=>ROOT_PATH]);
		}
		if($act =='desc'){
			 $info = DescDb::getDesign($sid);
			 return view(ROOT_PATH.'/sfdp_desc.html',['json'=>$info['s_field'],'fid'=>$info['id'],'look'=>$info['s_look']]);
		}
		if($act =='save'){
			$id = DescDb::saveDesc($sid,'save');
			return json(['code'=>0]);
		}
        return $act.'参数出错';
	}
	
	
	
	
	
}