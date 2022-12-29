<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V7.0
  *+------------------
  * Sfdp 构建函数方法
  *+------------------
  * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
  *+------------------
  */

namespace sfdp\fun;

use think\Db;
use think\facade\Config;
use think\Exception;

class BuildFun{
	/**
     * 创建数据表
     */
    public static function Bfun($data,$name)
    {
		$fileName = 'static' . DIRECTORY_SEPARATOR . 'sfdp' . DIRECTORY_SEPARATOR .'user-defined'. DIRECTORY_SEPARATOR .$name.'.js';
		return file_put_contents($fileName, $data);
		return ['info'=>'创建成功！','code'=>0];
    }
}