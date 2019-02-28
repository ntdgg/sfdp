<?php
/**
*+------------------
* 工作流任务服务
*+------------------ 
*/

namespace tpdf;

use think\Loader;

class BuildController{
	 private $namespaceSuffix;
	/**
     * 创建控制器文件
     */
	 
    public function Bc($path, $pathTemplate, $fileName, $code, $data)
    {
		$this->module = $data['module'];
        $this->name = ucfirst($data['controller']);
        $this->nameLower = Loader::parseName($this->name);
        $template = file_get_contents($pathTemplate . "Controller.tpl");
        $file = str_replace(
            ['%MODULE%', '%NAME%'],
            [$this->module, 'controller'],
            $fileName
        );
        return file_put_contents($file, str_replace(
                ["[MODULE]", "[TITLE]", "[NAME]", "[FILTER]", "[NAMESPACE]"],
                [$this->module, $data['title'], $this->name, $code['filter'], $this->namespaceSuffix],
                $template
            )
        );
    }
}