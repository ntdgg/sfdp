<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V5.0
 *+------------------
 * Sfdp 数据类
 *+------------------
 * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */
namespace sfdp\adaptive;


use sfdp\fun\SfdpUnit;
use sfdp\lib\unit;
use think\facade\Db;

class M{

    protected $mode ;
    public function  __construct(){
        if(unit::gconfig('db_mode')==1){
            $className = '\\sfdp\\custom\\AdapteeM';
        }else{
            $className = unit::gconfig('db_namespace').'AdapteeM';
        }
        $this->mode = new $className();
    }

    /**
     * @param $data
     * @return array
     */
    static function Save($data){
        $find = (new M())->mode->find($data['sid']);
        if($find){
            $post = [
                'is_open'=>$data['is_open'],
                'exp_time'=>$data['exp_time'],
                'source'=>$data['source'],
                'route'=>$data['route'],
                'type'=>$data['type'],
                'act'=>$data['act'],
                'id'=>$find['id'],
                'add_time'=>date('Y-m-d H:i:s')
            ];
            $ret = (new M())->mode->update($post);
            if(!$ret){
                return ['code'=>1,'msg'=>'更新失败！'];
            }
        }else{
            $post = [
                'sid'=>$data['sid'],
                'table'=>$data['table'],
                'is_open'=>$data['is_open'],
                'exp_time'=>$data['exp_time'],
                'source'=>$data['source'],
                'token'=>md5($data['sid'].'|'.$data['table']),
                'route'=>$data['route'],
                'type'=>$data['type'],
                'act'=>$data['act'],
                'uid'=>session('softId'),
                'add_time'=>date('Y-m-d H:i:s')
            ];
            $ret = (new M())->mode->insert($post);
            if(!$ret){
                return ['code'=>1,'msg'=>'更新失败！'];
            }
        }
        if($data['act']==1){
            return self::CreatePHP($data['sid']);
        }
        return ['code'=>0,'msg'=>'success'];
    }

    /**
     * @param $sid
     * @return array
     */
    static function CreatePHP($sid){
        $find = (new M())->mode->find($sid);
        $class = self::convertUnderline($find['table']);
        $template = file_get_contents(BEASE_SFDPURL . "/adaptive/M.tpl");
        $str = str_replace(
            ["[class]"],
            [$class],
            $template
        );
        if(@file_put_contents(root_path(). '/app/api/controller/m/'.$class.'.php' , $str) === false)
        {
            return ['code'=>1,'msg'=>'写入文件失败，请检查/app/api/controller/m/目录是否有权限'];
        }
        return ['code'=>0,'msg'=>'success'];
    }

    /**
     * @param $str
     * @param bool $ucfirst
     * @return mixed|string
     */
    static function convertUnderline($str,$ucfirst = true)
    {
        while(($pos = strpos($str , '_'))!==false)
            $str = substr($str , 0 , $pos).ucfirst(substr($str , $pos+1));
        return $ucfirst ? ucfirst($str) : $str;
    }
}