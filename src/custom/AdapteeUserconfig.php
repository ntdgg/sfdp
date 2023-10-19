<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V7.0
 *+------------------
 * Sfdp 适配器设计器数据类
 *+------------------
 * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
 *+------------------
 */
namespace sfdp\custom;

use think\facade\Db;

use sfdp\fun\BuildFun;
use sfdp\fun\SfdpUnit;
use sfdp\fun\BuildTable;

use sfdp\lib\unit;


class AdapteeUserconfig{

    function getInfo($sid){
        $info = Db::name('sfdp_user_config')->where('uid',unit::getuserinfo('uid'))->where('sid',$sid)->cache('sfdp_user_config_'.$sid,600)->field('field,field_name')->find();
        if($info){
            return  ['field'=>explode(',',$info['field']),'field_name'=>explode(',',$info['field_name'])];
        }else{
            return  false;
        }
    }

    function add($data){
        $data['update_time'] = time();
        $info = Db::name('sfdp_user_config')->where('uid',unit::getuserinfo('uid'))->where('sid',$data['sid'])->find();
        if($info){
            $res = Db::name('sfdp_user_config')->cache('sfdp_user_config_'.$data['sid'])->where('id',$info['id'])->update($data);
        }else{
            $data['uid'] = unit::getuserinfo('uid');
            $res = Db::name('sfdp_user_config')->insertGetId($data);
        }
        if($res){
            return  true;
        }else{
            return  false;
        }
    }

}