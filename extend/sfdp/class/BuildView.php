<?php
/**
 *+------------------
 * Tpfd 表单控件
 *+------------------
 * Copyright (c) 2006~2018 http://cojz8.cn All rights reserved.
 *+------------------
 * Author: guoguo(1838188896@qq.com)
 *+------------------
 */

namespace sfdp;

use think\Db;
use think\facade\Config;
use think\Exception;
use think\facade\Request;

require_once Tp_DF . '/config/config.php';

class BuildView{
	public function Bview($pathView, $pathTemplate, $fileName, $code, $data)
	{
		$this->module = $data['module'];
		$this->buildIndex($pathView, $pathTemplate, $fileName, $code, $data);
		$this->buildView($pathView, $pathTemplate, $fileName, $code, $data);
		$this->buildEdit($pathView, $pathTemplate, $fileName, $code, $data);
	}
	private function buildIndex($path, $pathTemplate, $fileName, $code, $data)
    {
        $script = '';
        if ($code['search_selected']) {
            $script = '{block name="script"}' . implode("", $code['script_search']) . "\n"
                . '<script>' . "\n"
                . tab(1) . '$(function () {' . "\n"
                . $code['search_selected']
                . tab(1) . '})' . "\n"
                . '</script>' . "\n"
                . '{/block}' . "\n";
        }
        // 菜单全选的默认直接继承模板
        $menuArr = isset($data['menu']) ? $data['menu'] : [];
        $menu = '';
        if ($menuArr) {
            $menu = '{tp:menu menu="' . implode(",", $menuArr) . '" /}';
        }
        $tdMenu = '';
        $template = file_get_contents($pathTemplate . "index.tpl");
        $file = $path . "index.html";
        if ($this->module == Request::module()) {
            $module = '';
        } else {
            $module = Request::module() . '@';
        }
		if ($data['flow'] == 0) {
            $flow = "{:action('wf/btn',['wf_fid'=>\$vo.id,'wf_type'=>'".$data['table']."','status'=>\$vo.status])}";
        } else {
            $flow= '';
        }
		$form = implode("\n" . tab(1), $code['search']);
        $th = implode("\n" . tab(3), $code['th']);
        $td = implode("\n" . tab(3), $code['td']);
        $tdMenu .= tab(4) . '{tp:menu menu=\'sdeleteforever\' /}';
        return file_put_contents($file, str_replace(
                ["[MODULE]", "[FORM]", "[MENU]", "[TH]", "[TD]", "[TD_MENU]", "[SCRIPT]","[FLOW]"],
                [$module, $form, $menu, $th, $td, $tdMenu, $script,$flow],
                $template
            )
        );
    }
	/**
     * 创建 edit.html 文件
     */
    private function buildView($path, $pathTemplate, $fileName, $code, $data)
    {
        $template = file_get_contents($pathTemplate . "view.tpl");
        $file = $path . "view.html";
        if ($this->module == Request::module() || !$this->module) {
            $module = '';
        } else {
            $module = Request::module() . '@';
        }
        return file_put_contents($file, str_replace(
            ["[MODULE]", "[ROWS]", "[SET_VALUE]", "[SCRIPT]"],
            [$module, $code['edit'], implode("\n", array_merge($code['set_checked'], $code['set_selected'])), implode("", $code['script_edit'])],
            $template));
    }
	/**
     * 创建 edit.html 文件
     */
    private function buildEdit($path, $pathTemplate, $fileName, $code, $data)
    {
        $template = file_get_contents($pathTemplate . "edit.tpl");
        $file = $path . "edit.html";
        if ($this->module == Request::module() || !$this->module) {
            $module = '';
        } else {
            $module = Request::module() . '@';
        }
        return file_put_contents($file, str_replace(
            ["[MODULE]", "[ROWS]", "[SET_VALUE]", "[SCRIPT]"],
            [$module, $code['edit'], implode("\n", array_merge($code['set_checked'], $code['set_selected'])), implode("", $code['script_edit'])],
            $template));
    }
}