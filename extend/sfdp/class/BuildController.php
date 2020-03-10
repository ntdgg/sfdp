<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V3.0
 *+------------------
 * Copyright (c) 2018~2020 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace sfdp;

use think\Loader;

class BuildController{
	/**
     * 创建控制器文件
     */
	 
    public function Bc($path, $pathTemplate, $fileName, $code, $data)
    {
		$this->module = $data['module'];
        $this->name = ucfirst($data['controller']);
        $template = file_get_contents($pathTemplate . "Controller.tpl");
        $file = str_replace(
            ['%MODULE%', '%NAME%'],
            [$this->module, 'controller'],
            $fileName
        );
		$fun = '';
		foreach($data['form'] as  $k=>$v){
			if($v['fun']=='yes'){
				$fun .='$this->assign("'.$v['fun_con']['zdname'].'",Db::query("'.$v['fun_con']['sql'].'"));'.PHP_EOL .'		';
			}
		}
        return file_put_contents($file, str_replace(
                ["[MODULE]", "[TITLE]", "[NAME]", "[FILTER]",'[FUN]'],
                [$this->module, $data['title'], $this->name, $code['filter'],$fun],
                $template
            )
        );
    }
}