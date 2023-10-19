<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V7.0
  *+------------------
  * Sfdp 工具类
  *+------------------
  * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
  *+------------------
  */
namespace sfdp\lib;


use sfdp\adaptive\Script;

class unit{

    public static function sConfig($sfdp_ver_info,$load_file,$sid){
        $fun = self::uJs($sfdp_ver_info);
        $config = [
            'g_js'=>self::sJs($sfdp_ver_info['sid']),
            'fun' =>$fun,
            'load_file' =>$load_file,
            'upload_file'=>unit::gconfig('upload_file'),
            'upload_files'=>unit::gconfig('upload_files'),
            's_type' =>$sfdp_ver_info['s_type']
        ];
        return [$fun,$config];
    }
    /**
     *优化用户自定义脚本用法
     */
    public static  function uJs($info){
        $fun = '';
        if($info['s_fun_id']>0){
            $sfdp_script_ver = Script::getVer($info['s_fun_id']);
            $fun = '<script src="/static/sfdp/user-defined/'.$info['s_fun_ver'].'.js?ver='.$sfdp_script_ver.'"></script>';
        }
        return $fun;
    }
    /**
     *全局变量用法
     */
    public static function sJs($sid){
        $ginfo = unit::getuserinfo();
        $g_js ='<script>
            var g_uid='.$ginfo['uid'].';
            var g_role='.$ginfo['role'].';
            var g_saas="'.$ginfo['saas_id'].'";
            var g_username="'.$ginfo['username'].'";
            var g_sid='.$sid.';
            </script>';
        return $g_js;
    }
    /**
     *返回错误得JS消息提示
     */
    public static function errJSMsg($msg,$url='',$relod=false){
        if($url <> ''){
            echo "<script language='javascript'>parent.layer.msg('".$msg."'); location.assign('".$url."');</script>";exit;
        }else{
            echo '<script>var index = parent.layer.getFrameIndex(window.name);parent.layer.msg("'.$msg.'");setTimeout("parent.layer.close(index)",2000);</script>';exit;
        }
    }
	/**
	 * 判断是否是POST
	 *
	 **/
	public static function is_post()
	{
	   return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD'])=='POST';	
	}
	/**
	 * 加载自定义事务驱动文件
	 *
	 * @param  string $class 类
	 * @param  int $id 单据对应的ID编号
	 * @param  int  $run_id 运行中的流程ID
	 * @param  array $data 步骤类
	 */
	public static function LoadClass($class,$id,$run_id='',$data='') {
		$className = unit::gconfig('wf_work_namespace').$class;
		if(!class_exists($className)){
			return -1;
		}
		return new $className($id,$run_id,$data);
	} 
	/**
	* 获取定义的信息
	* @param string $key
	**/
	public static function getuserinfo($key='') {
		if(unit::gconfig('gateway_mode')==1){
			$user_info = [
				'uid'=>session(self::gconfig('int_user_id')),
				'username'=>session(self::gconfig('int_user_name')),
				'role'=>session(self::gconfig('int_user_role')),
                'saas_id'=>session(self::gconfig('int_saas_id')),
			];
		}else{
			$className = unit::gconfig('gateway_action');
			if(!class_exists($className)){
				return -1;
			}
			$user_info = (new $className())->GetUserInfo();;
		}
		if($user_info['uid']==''||$user_info['role']==''||$user_info['username']==''){
			return -1;
		}
		if($key==''){
			return $user_info;
		}else{
			return $user_info[$key] ?? '';
		}
		
	}
	/**
	 * 根据键值加载全局配置文件
	 *
	 * @param string $key 键值
	 */
	public static function gconfig($key) {
		$file = dirname(dirname(__DIR__) . DIRECTORY_SEPARATOR, 4) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'sfdp.php';
		if(!file_exists($file)){
			echo 'sorry,config no find!';exit;
		}
		$ret =require($file);
		return $ret[$key] ?? '';
	}
	/**
	 * 缩进设置
	 *
	 * @param string $step 步骤
	 * @param string $string 字符串
	 * @param string $size 长度
	 */
	static function tab($step = 1, $string = ' ', $size = 4)
	{
		return str_repeat($string, $size * $step);
	}
	/**
	 * 根据日期编码生成
	 *
	 */
	static function OrderNumber()
	{
		return "D" . date("Ymd").rand(1000,9999);;
	}

    static function errMsg($code){
        $msg =  [
            3001=>'Sorry,逻辑检查失败，[存在未设置字段信息]',
            3002=>'Sorry,逻辑检查失败，[存在相同的字段信息]',
            3003=>'Sorry，[未找到自定义类，请先配置]',
            3004=>'Sorry，[函数不存在字段别名name/id]'
        ];

        return $msg[$code] ?? '对不起，未知错误！';
    }
}

