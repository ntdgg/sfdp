<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V7.0
 *+------------------
 * Sfdp 适配器基础数据类
 *+------------------
 * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
 *+------------------
 */
namespace sfdp\custom;

use think\facade\Db;

class AdapteeM{

    function insert($data){
        $info = Db::name('sfdp_data')->insertGetId($data);
        if($info){
            return  $info;
        }else{
            return  false;
        }
    }
    function update($data){
        $info = Db::name('sfdp_data')->update($data);
        if($info){
            return  $info;
        }else{
            return  false;
        }
    }

	function find($id){
		$info = Db::name('sfdp_data')->where('sid',$id)->find();
		if($info){
			return  $info;
		}else{
			return  false;
		}
	}

	
	
}