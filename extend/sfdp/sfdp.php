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

use think\Exception;
use think\facade\Log;
use think\facade\Config;
use think\Db;
use think\Loader;
use think\facade\Request;

define ( 'FILE_PATH', realpath ( dirname ( __FILE__ ) ) );
define('APP_PATH',\Env::get('app_path') );
define('ROOT_PATH',\Env::get('root_path') );
define('DS',DIRECTORY_SEPARATOR);

require_once FILE_PATH . '/class/build.php';
require_once FILE_PATH . '/config/config.php';
require_once FILE_PATH . '/db/FbDb.php';
require_once FILE_PATH . '/class/BuildView.php';
require_once FILE_PATH . '/class/BuildTable.php';
require_once FILE_PATH . '/class/BuildController.php';


class sfdp
{
    private $module;
    private $name;
    private $dir;
    private $nameLower;
    private $data;
    // 控制器黑名单
    private $blacklistName = [
        'Flow','Flowdesign','Formdesign','Index','News','User'
    ];
    // 数据表黑名单
    private $blacklistTable = [
        'flow', 'flow_process','form','form_function','menu','news','news_type','role','role_user','run','run_cache','run_log','run_process','run_sign','user'
    ];

	/*
	 * API处理接口 API
	 * 
	 **/
		
	function FbApi($Type,$fid='',$data='demo')
	{
		if ($Type == "List") {
				$info = FbDb::Lists($flow_id); 
			} else if ($Type == "Desc") {
				$info = FbDb::Fbdesc($fid);
			} else if ($Type == "Edit") { 
				$info = FbDb::Fbedit($fid);
			} else if ($Type == "Save")  { 
				$info = FbDb::Fbsave($data); 
			} else if ($Type == "Bview")  { 
				$info = FbDb::FBview($fid,$data);
			} else{
				throw new \Exception ( "参数出错！" );
			}
		return $info;
	}
	static function lists(){
		return 123;
	}
    public function make($data)
    {
		$this->data = $data;
        $this->module = $data['module'];
        $this->name = ucfirst($data['controller']);
        $this->nameLower = Loader::parseName($this->name);
	
		if (!self::checkPath(APP_PATH . $data['module'])) {
            throw new Exception("目录没有权限不可写!", 403);
		}
		// 将菜单全部转为小写
        if (isset($data['menu']) && $data['menu']) {
            foreach ($data['menu'] as &$menu) {
                $menu = strtolower($menu);
            }
        }
		// 数据表表名
        $tableName = str_replace(DS, '_', $this->dir) . $this->nameLower;
        // 判断是否在黑名单中
        if (in_array($data['controller'], $this->blacklistName)) {
            throw new Exception('该控制器不允许创建');
        }
        // 判断是否在数据表黑名单中
        if (isset($data['table']) && $data['table'] && in_array($tableName, $this->blacklistTable)) {
            throw new Exception('该数据表不允许创建');
        }
		$this->buildDir();//创建目录
		$pathView = APP_PATH . $this->module . DS . "view" . DS . $this->dir . $this->nameLower . DS;
        $pathTemplate = ROOT_PATH . 'extend' . DS . "tpdf" . DS . "template" . DS ;
        $fileName = APP_PATH . "%MODULE%" . DS . "%NAME%" . DS . $this->dir . $this->name . ".php";
        $code = $this->parseCode();
		
		if($data['controller'] != 'demo'){
			if($data['file']=='all'){
				//生成数据库
				$bulid = new BuildTable();
				$bulid->Btable($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
				//生成控制器
				$bulid = new BuildController();
				$bulid->Bc($pathView, $pathTemplate, $fileName, $code, $data);
				//生成视图
				$bulid = new BuildView();
				$bulid->Bview($pathView, $pathTemplate, $fileName, $code, $data);
			}
			if($data['file']=='database'){
				$bulid = new BuildTable();
				$bulid->Btable($pathView, $pathTemplate, $fileName, $tableName, $code, $data);
			}
			if($data['file']=='controller'){
				$bulid = new BuildController();
				$bulid->Bc($pathView, $pathTemplate, $fileName, $code, $data);
			}
		}else{
			$bulid = new BuildView();
			$bulid->Bview($pathView, $pathTemplate, $fileName, $code, $data);
		}
    }
	/**
	 * 目录可写检测
	 *
	 **/
	private function checkPath($path){
		try {
            $path = $path ? $path : APP_PATH . 'index' . DS;
            $testFile = $path . "bulid.test";
            if (!file_put_contents($testFile, "test")) {
                return false;
            }
            unlink($testFile);
            return true;
        } catch (Exception $e) {
            return false;
        }
	} 
	/**
	 * 生成目录
	 *
	 **/
	private function buildDir(){
		$dir_list = [$this->module . DS . "view" . DS . $this->dir . $this->nameLower];
        if ($this->dir) {
            $dir_list[] = $this->module . DS . "controller" . DS . $this->dir;
        }
        foreach ($dir_list as $dir) {
            $path = APP_PATH . $dir;
            if (!is_dir($path)) {// 创建目录
                mkdir($path, 0755, true);
            }
        }
	}
	private function parseCode()
    {
        $sortable = false; // 生成 form.html 文件的代码
        $search = ['<form class="mb-20" method="get" action="{:url($Request.action)}">'];// 生成 th.html 文件的代码
        $th = ['<th width="25"><input type="checkbox"></th>'];// 生成 td.html 文件的代码
        $td = ['<td><input type="checkbox" name="id[]" value="{$vo.id}"></td>'];// 生成 edit.html 文件的代码
        $editField = '<table class="table table-border table-bordered table-bg">';// radio类型的表单控件编辑状态使用javascript赋值
        $setChecked = [];// select类型的表单控件编辑状态使用javascript赋值
        $setSelected = [];// 搜索时被选中的值
        $searchSelected = '';
		$scriptEdit = [];
        // 控制器过滤器
        $filter = '';
		if (isset($this->data['form']) && $this->data['form']) {
			$ii = 0;
			end($this->data['form']);
			$key_last = key($this->data['form']);
			 foreach ($this->data['form'] as $key =>$form) {
				/*控制器表单查询生成*/
				if(isset($form['search']) && $form['search']=='yes'){
					 $filter .= tab(2) . 'if ($this->request->param("' . $form['name'] . '")) {' . "\n"
                        . tab(3) . '$map[\'' . $form['name'] . '\'] = ["like", "%" . $this->request->param("' . $form['name'] . '") . "%"];' . "\n"
                        . tab(2) . '}' . "\n";
					 $search[] = tab(1) . '<input type="text" class="input-text" style="width:250px" '
                                . 'placeholder="' . $form['title'] . '" name="' . $form['name'] . '" '
                                . 'value="{$Request.param.' . $form['name'] . '}" '
                                . '>';
				}
				/*生成index首页用*/
				if(isset($form['lists']) && $form['lists']=='yes'){ 
					$td[] = '<td>{$vo.' . $form['name'] ."}</td>";
					$th[] = '<th>' . $form['title'] ."</th>";
				}
				/*生成 Edit.html 开始*/
				if($ii == 0){
					$editField .= "\n" . tab(3).'<tr>';
				}
				$bulid = new build();
				$input = $bulid->convertInput($form);
				
				$editField .= "\n" . tab(3).$input['Field'];
				$ii =$ii + $input['num'];
				if($ii == 3){
					$editField .= "\n" . tab(3).'</tr>';
					$ii = 0;
				}
				/*判断最后是否有足够的TD*/
				if($key_last == $key){
					if($ii < 3){
						$editField .= $bulid->bulidTd($ii);
					}
				}
				/*生成 Edit.html 结束*/
				$scriptEdit[]= $input['script_edit'];
			 }
		}
		if($this->data['flow']==0){ 
					$th[] = '<th>状态</th>';
					$td[] = "<td>{:action('flow/status',['status'=>\$vo.status])}</td>";
				}
		$editField .= "</table>";
		 if (count($search) > 1) {
            // 有设置搜索则显示
            $search[] = tab(1) . '<button type="submit" class="btn btn-success"><i class="Hui-iconfont">&#xe665;</i> 搜索</button>';
            $search[] = '</form>';
			} else {
				$search = [];
		}
        $scriptSearch = [];
        return [
            'search'          => $search,
            'th'              => $th,
            'td'              => $td,
            'edit'            => $editField,
            'set_checked'     => $setChecked,
            'set_selected'    => $setSelected,
            'search_selected' => $searchSelected,
            'filter'          => $filter,
            'script_edit'     => $scriptEdit,
            'script_search'   => $scriptSearch,
        ];
    }
}
