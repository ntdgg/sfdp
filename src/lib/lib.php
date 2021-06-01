<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 工具类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\lib;


class lib{
	/**
	 * 设计器列表模板
	 *
	 * @param  Array $data 列表数据
	 */
	public static function index($data){
		$tmp = self::commontmp('Sfdp超级表单设计器 V5.0');
		$urls= unit::gconfig('url');
		$fun_save = $urls['api'].'?act=btable';
		$tr = '';
		
		if(unit::gconfig('node_mode')==2){
			$className = unit::gconfig('node_action');
			if(!class_exists($className)){
				return 'Sorry,未找到node_action类，请先配置~';
			}
			$Node = (new $className())->GetNode();//获取目录节点信息	
		}else{
			$Node = unit::gconfig('node_data');
		}
		$node_url =$urls['api'].'?act=node';
		$create_url =$urls['api'].'?act=create';
		foreach($data as $k=>$v){
		   $status = ['未锁定','已锁定'];
		   $status_zt = [0=>'未部署',1=>'未部署',2=>'已部署'];
		   $btn = '<a onClick=sfdp.openfullpage("设计——'.$v['s_bill'].'","'.$urls['api'].'?act=desc&sid='.$v['id'].'") class="button">设计</a>';
		   if($v['s_field'] <> 1){
			   $fix = $urls['api'].'?act=fix&sid='.$v['id'];
			   $btn .= ' <a onClick=sfdp.Askshow("'.$fix.'","部署后将生成最新版本,确定是否执行?") class="button">部署</a><a onClick=sfdp.openpage("定义管理——'.$v['s_bill'].'","'.$urls['api'].'?act=custom&sid='.$v['id'].'") class="button">定义</a>';
		   }
		   if($v['s_db_bak']==1){
			   $btn .='<a onClick=sfdp.Askshow("'.$urls['api'].'?act=deldb&sid='.$v['id'].'","删除备份数据库,是否执行?")  class="button">DelDb</a>';
			   $btn .='<a onClick=add_fun('.$v['id'].')  class="button">Menu</a>';
		   }
		   $tr .='<tr class="text-c"><td>'.$v['id'].'</td><td>'.$v['s_bill'].'</td><td>'.$v['s_title'].'</td><td>'.date('Y/m/d H:i',$v['add_time']).'</td><td>'.$status_zt[$v['s_design']].'</td><td>'.$status[$v['s_look']].'（'.$v['s_db'].'）</td><td>'.$btn.'</td></tr>';
		}
		return <<<php
		{$tmp['head']}{$tmp['js']}
			</head>
		<body>
		<div class="page-container">
			<div style='float: left;width:5%;margin-top: 10px;text-align: center;'>
				<a onClick='sfdp.Askshow("{$create_url}","是否创建新表单？")' class="button">创&#12288;建</a><hr/><a onclick="add_btable()" class="button ">黑名单</a><hr/><a onclick="location.reload();" class="button ">刷&#12288;新</a>
			</div>
			<div style='float: left;width:95%;margin-top: 10px;'>
				<table class="table" >
					<tr class="text-c"><th >sid</th><th >编码</th><th>标题</th><th>发布时间</th><th>启用状态</th><th>锁定状态</th><th>操作</th></tr>
					{$tr}
				</table>
			</div>
		</div>
		<div id='menu' style='display:none'>
		{$Node['html']}	
		</div>
		<script>
		function add_fun(sid){
			var htmls = '<div style="margin:10px;">节点：<select id="nodesss" class="select" ><option value="">请选择挂带节点</option>'+$('#menu').html()+'</select>　<a onClick=getmenu('+sid+')  class="button">创建节点</a></div>';
				layer.open({
				  title: '选择挂带节点信息',
				  type: 1,
				  area: ['520px', '150px'], //宽高
				  content: htmls
				});
		}
		function add_btable(title='',name='',id=''){
			var html ='<form action="" method="post" name="form" id="form">'+
					  '<table class=table id="table_view"><tr><td>表名称</td><td style="text-align:left"><input type="text" id="title"></td></tr>'+
					  '<tr><td>表别名</td><td style="text-align:left"><input type="text" id="name" ></td></tr>'+
					  '<tr><td colspan=2 style="text-align: center;"><a class="button" onclick="save_btable()">提交</a></td></tr><tr><td colspan=2>*黑名单表是为了防止重复而设计；<br/>*表名称：不含表前缀；如：sfdp_table<br/>表别名：如新闻信息表</td></tr></table></form>';
				layer.open({
				  type: 1,
				  area: ['320px', '280px'], //宽高
				  content: html
				});
		}
		function save_btable(){
			var title = $('#title').val();
			var name = $('#name').val();
			if(title =='' || name==''){
				sfdp.ShowTip('表名称和内容必须填写！');
				return;
			}
			var url = "{$fun_save}";
			sfdp.sAjax(url,{title:title,name:name});
		}
		function getmenu(sid){
			var node = $('#nodesss').val();
			if(node==''){
				sfdp.ShowTip('请选择节点信息！');
				return;
			}
			var url = "{$node_url}&sid="+sid+"&node="+node;
			sfdp.Askshow(url,"再次确认是否创建目录,是否执行?");
		}
		</script>
		</body>
		</html>
php;
	}
	/**
	 * 函数方法
	 *
	 * @param  Array $data 列表数据
	 */
	public static function fun($data){
		$tr = '';
		$tmp = self::commontmp('Sfdp超级表单设计器');
		$urls= unit::gconfig('url');
		$fun_save = $urls['api'].'?act=fun_save&sid=';
		foreach($data as $k=>$v){
		   $status = ['编辑中','已启用'];
		   if($v['status']==0){
			   $btn ='<a onClick=sfdp.Askshow("'.$urls['api'].'?act=fun_update&status=1&id='.$v['id'].'","是否启用,是否执行?")  class="button">启用</a>';
			}else{
			   $btn ='<a onClick=sfdp.Askshow("'.$urls['api'].'?act=fun_update&status=0&id='.$v['id'].'","是否禁用,是否执行?")  class="button" style="    background-color: indianred;">禁用</a>';
		   }
		   $tr .='<tr class="text-c"><td>'.$v['bill'].'</td><td>'.$v['title'].'</td><td>'.$v['fun_name'].'</td><td>'.date('Y/m/d H:i',$v['add_time']).'</td><td>'.$v['add_user'].'</td><td>'.$status[$v['status']].'</td><td><input id="fun_'.$v['fun_name'].'" value="'.$v['function'].'" type=hidden><a onClick=add_fun("'.$v['title'].'","'.$v['fun_name'].'","'.$v['id'].'")	class="button">编辑</a>'.$btn.'</td></tr>';	
		}
		return <<<php
		{$tmp['css']}{$tmp['head']}{$tmp['js']}
<div class="page-container">
<div style='float: left;width:8%;text-align:center;margin-top:10px'>
	<a onClick='add_fun()' class="button">创建</a><a onclick="location.reload();" class="button ">刷新</a><hr/>
</div>
<div style='float: left;width:92%;margin-top:10px'>
	<table class="table">
			<tr class="text-c"><th width="150">编码</th><th width="150">标题</th><th width="100">函数名</th><th width="80">发布时间</th><th width="50">添加人</th><th width="50">状态</th><th width="200">操作</th>
			</tr>{$tr}
	</table>
</div>
</div>
</body>
</html>
<script>
function add_fun(title='',name='',id=''){
	var fun = $('#fun_'+name).val() ?? '';
	var html ='<form action="" method="post" name="form" id="form">'+
			  '<table class=table id="table_view"><tr><td>函数标题</td><td style="text-align:left"><input type="text" id="title" value="'+title+'"></td></tr>'+
			  '<tr><td>函数名称</td><td style="text-align:left"><input type="text" id="name" value="'+name+'"></td></tr>'+
			  '<tr><td>填写函数</td><td><textarea placeholder="请填写SQL代码！" id="fun" type="text/plain" style="width:100%;height:280px;">'+fun+'</textarea></td></tr><tr><td colspan=2><a class="button" onclick="save_fun('+id+')">提交</a></td></tr></table></form>';
		layer.open({
		  type: 1,
		  area: ['620px', '540px'], //宽高
		  content: html
		});
}
function save_fun(id){
	var NameExp = /^(?!_)(?!.*?_$)[a-z_]+$/;
	var title = $('#title').val();
	var name = $('#name').val();
	if(!NameExp.test(name)){
		sfdp.ShowTip('函数名称只能为英文小写字母加下划线组合！');
		return;
	}
	var fun = $('#fun').val();
	var url = "{$fun_save}";
	sfdp.sAjax(url,{title:title,name:name,fun:fun,id:id});
}
</script>
php;
}
	/**
	 * 定义函数
	 *
	 * @param  Array $data 列表数据
	 * @param  int   $sid 设计器的ID值
	 * @param  Array $list 字段数据
	 * @param  array $listtrue 选中数据
	 * @param  array $field 所有字段数据
	 * @param  array $modue 模块数据
	 */
	public static function custom($sid,$list,$listtrue,$field,$modue){
		$patch = unit::gconfig('static_url');
		$urls= unit::gconfig('url');
		$url =$urls['api'];
		$fun_save = $urls['api'].'?act=customSave&sid='.$sid;
		$access = json_decode($modue['access'],true);
		 $search ='';
		 $field_html ='';
		 $access_html ='';
		 foreach($field as $k=>$v){
			$search .=' <option value="'.$v['id'].'">'.$v['name'].'('.$v['field'].')</option>';	 
		 }
		 foreach($field as $k=>$v){
			 if($v['is_search']==1){
				$field_html .='<div id="checkboxes_id'.$v['id'].'">查询字段：<select style="width:200px" class="smalls" name="search_ids"><option value="'.$v['id'].'">'.$v['name'].'('.$v['field'].')</option></select>  查询条件：<input name="search_value" type="text" value="'.$v['search_type'].'">    <span class="button" onclick=editoption("_id'.$v['id'].'")>Del</span></div>';
			 }		
		 }
		 $user = ['uid'=>'当前用户','role'=>'当前角色'];
		 $eq = ['='=>'等于','<>'=>'不等于','in'=>'包含','no in'=>'不包含'];
		 foreach((array)$access as $k=>$v){
			$access_html .='<div id="access_id'.$v[0].'">控制字段：<select style="width:200px" name="accsee_ids"><option value="'.$v[0].'">'.$v[3].'</option></select> <select style="width:200px" name="accsee_eq"><option value="'.$v[1].'">'.$eq[$v[1]].'</option></select> <select style="width:200px" name="accsee_user"><option value="'.$v[2].'">'.$user[$v[2]].'</option></select> <span class="button" onclick=editaccess("_id'.$v[0].'")>del</span></div>';
		 }
	return <<<php
  <link rel="stylesheet" href="{$patch}sfdp.5.0.css?v=5.0.1" />
  <style>
  #sortable1, #sortable2 {
    border: 1px solid #eee;
    min-height: 30px;
    list-style-type: none;
    margin: 0;
    padding: 5px 0 0 0;
    margin-right: 10px;
	display:inline-block;
	min-width: 150px;
  }
  .ui-state-highlight{
	 border: 1px solid #dad55e;
    background: #fffa90;
    color: #777620;
  }
  .ui-state-default{
	  border: 1px solid #c5c5c5;
    background: #f6f6f6;
    font-weight: normal;
    color: #454545;
  }
  #sortable1 li, #sortable2 li {
    margin: 0 5px 5px 5px;
    padding: 5px;
    font-size: 1.2em;
    width: 120px;
	float:left;
	font-size: 10px;
  }
  </style>
  <script src="{$patch}lib/jquery-1.12.4.js"></script>
  <script src="{$patch}lib/jquery-ui.js"></script>
	<script src="{$patch}lib/layer/2.4/layer.js"></script>
	<script src="{$patch}sfdp.5.0.js?v=5.0.1"></script>
  <script>
  $( function() {
    $( "#sortable1, #sortable2" ).sortable({
      connectWith: ".connectedSortable",
	  cancel:".cancel_me"
    }).disableSelection();
  } );
  	function save_list(){
		var sortid = [];
		var fieldid = [];
		var fieldname = [];
		$( "#sortable2 li" ).each( function (i){
			sortid. push($(this).attr("data-id"));
			fieldid. push($(this).attr("data-field"));
			fieldname. push($(this).attr("data-name"));
		});
		var url = "{$fun_save}";
		sfdp.sAjax(url,{sid:{$sid},data:sortid,field:fieldid,name:fieldname});
	}
  </script>
</head>
<body>
 <table>
 <tr><td  style='width: 8%;text-align: center;'><b>排序规则<b> </td><td style='width: 87%;'><input id="order" value='{$modue["order"]}'></td><td style='width: 5%'><a onclick='save_order()'  class='button'>保存</a></td></tr>
  <tr><td style='text-align: center;'><b>布局设置<b> </td><td>
	  <ul id="sortable1" class="connectedSortable">{$listtrue}</ul><hr/>
	  <ul id="sortable2" class="connectedSortable">{$list}</ul>
	  </td><td><a onclick='save_list()'  class='button'>保存</a></td>
  </tr>
    <tr><td style='text-align: center;'><b>权限控制<b> </td><td>
	{$access_html}
	<div id="access1">控制字段：<select style="width:200px" name="accsee_ids"><option value="">请选择</option>{$search}</select> 
	<select style="width:200px" name="accsee_eq"><option value="=">等于</option><option value="<>">不等于</option><option value="in">包含</option><option value="no in">不包含</option></select>
	<select style="width:200px" name="accsee_user"><option value="uid">当前用户</option><option value="role">当前角色</option></select> 
    <span class='button' onclick=addaccess(1)>Add</span></div>
	</td><td><a onclick='save_access()'  class='button'>保存</a></td></tr>
	<tr><td style='text-align: center;'><b>查询设置<b> </td><td>
	{$field_html}
	<div id="checkboxes1">查询字段：<select style="width:200px" name="search_ids"><option value="">请选择</option>{$search}</select> 查询条件：<input name="search_value" type="text" value="">如：=,>,LIKE     <span class='button' onclick=addoption(1)>Add</span></div>
	</td><td><a onclick='save_search()'  class='button'>保存</a></td></tr>
 </table>
<script type="text/javascript" language="javascript">
	function addaccess(id){
		 $('#access'+id).children('span').attr("onclick","editaccess("+id+")");
		 $('#access'+id).children('span').html('Del');
		 var html ='<div id="access'+(id+1)+'">控制字段：<select style="width:200px" name="accsee_ids"><option value="">请选择</option>{$search}</select> <select style="width:200px" name="accsee_eq"><option value="=">等于</option><option value="<>">不等于</option><option value="in">包含</option><option value="no in">不包含</option></select> <select style="width:200px" name="accsee_user"><option value="uid">当前用户</option><option value="role">当前角色</option></select> <span class="button" onclick=addaccess('+(id+1)+')>Add</span></div>';
		 $('#access'+id).after(html);
	}
	function addoption(id){
		 $('#checkboxes'+id).children('span').attr("onclick","editoption("+id+")");
		 $('#checkboxes'+id).children('span').html('Del');
		 var html ='<div id="checkboxes'+(id+1)+'">查询字段：<select style="width:200px" name="search_ids"><option value="">请选择</option>{$search}</select> 查询条件：<input name="search_value" type="text" value="">如：=,>,LIKE     <span onclick=addoption('+(id+1)+') class="button">Add</span></div>';
		 $('#checkboxes'+id).after(html);
	}
	function editaccess(id){
		$('#access'+id).remove();
	}
	function editoption(id){
		$('#checkboxes'+id).remove();
	}
	function save_access(){
		var ids = [] , value =[] , user =[];
		$("select[name='accsee_ids']").each(function () {
			ids.push(this.value);
		});
		$("select[name='accsee_eq']").each(function () {
			value.push(this.value);
		});
		$("select[name='accsee_user']").each(function () {
			user.push(this.value);
		});
		sfdp.sAjax("{$url}?act=customAccess",{sid:{$sid},ids_val:ids.join(','),value_val:value.join(','),user_val:user.join(',')});
	}
	function save_search(){
		var ids = [] , value =[];
		$("select[name='search_ids']").each(function () {
			ids.push(this.value);
		});
		$("input[name='search_value']").each(function () {
			value.push(this.value);
		});
		sfdp.sAjax("{$url}?act=customSearch",{sid:{$sid},ids_val:ids.join(','),value_val:value.join(',')});
	}
	function save_order(){
		sfdp.sAjax("{$url}?act=customOrder",{sid:{$sid},order:$('#order').val()});
	}
 </script>
</body>
</html>
php;
}
	/**
	 * 设计器界面
	 *
	 * @param  json $json 设计数据
	 * @param  int   $fid 设计ID
	 * @param  int $look 是否锁定
	 */
	public static function desc($json,$fid,$look){
		$patch = unit::gconfig('static_url');
		$urls= unit::gconfig('url');
		$save = $urls['api'].'?act=save';
		$script = $urls['api'].'?act=script&sid='.$fid;
	return <<<php
	<html>
	<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>
	<link rel="stylesheet" href="{$patch}sfdp.5.0.css?v=5.0.1" /> 
	<body><div class="sfdp-top">Sfdp超级表单开发平台 v5.0</div>
		<div>
			<div id="ctlMenus" class="sfdp-con" style="float: left; width: 240px;">
				<div class="sfdp-tool-title">设计控制区 Design control area</div>
				&#8194;&#8194;
				<div class="button" onclick='sfdp.sys_config()'>配置</div><div class="button" id='up_save'>保存</div><div onclick='window.location.reload()' class="button">刷新</div><div class="button" onClick="sfdp.openfullpage('官网','//cojz8.com')">帮助</div>
				<div class="sfdp-cl" ></div>
				<div class="sfdp-tool-title sfdp-mt10">页面布局 Form control library</div>
				<div class='sfdp-tool-fix'onclick='sfdp.build_bj()'><a>&#8194;<b class='ico'>↭</b>&#8194;栅格布局 </a></div>
				<div class='sfdp-tool-fix'onclick='sfdp.openfullpage("脚本","{$script}")'><a>&#8194;<b class='ico'>∮</b>&#8194;脚本开发 </a></div>
				<div class="sfdp-cl"></div>
				<div class="sfdp-tool-title sfdp-mt10">表单控件库 Form control library</div>
					<div class="sfdp-tool-con" ><a data="text">&#8194;<b class='ico'>Α</b>&#8194;文本</a></div>
					<div class="sfdp-tool-con" ><a data="number">&#8194;<b class='ico'>½</b>&#8194;数字</a></div>
					<div class="sfdp-tool-con"><a data="checkboxes">&#8194;<b class='ico'>☑</b>&#8194;多选框</a></div>
					<div class="sfdp-tool-con"><a data="radio">&#8194;<b class='ico'>◉</b>&#8194;单选框</a></div>
					<div class="sfdp-tool-con"><a data="dropdown">&#8194;<b class='ico'>≡</b>&#8194;下拉组件 </a></div>
					<div class="sfdp-tool-con"><a data="date">&#8194;<b class='ico'>◑</b>&#8194;日期时间</a></div>
					<div class="sfdp-tool-con"><a data="textarea">&#8194;<b class='ico'>〓</b>&#8194;多行文本</a></div>
					<div class="sfdp-tool-con"><a data="html">&#8194;<b class='ico'>☪</b>&#8194;Html控件</a></div>
					<div class="sfdp-tool-con"><a data="wenzi">&#8194;<b class='ico'>あ</b>&#8194;文字显示</a></div>
					<div class="sfdp-tool-con"><a data="upload">&#8194;<b class='ico'>➼</b>&#8194;附件上传</a></div>
					<div class="sfdp-tool-con"><a data="time_range">&#8194;<b class='ico'>≈</b>&#8194;时间范围</a></div>
					<div class="sfdp-tool-con"><a data="links">&#8194;<b class='ico'>↔</b>&#8194;超级链接</a></div>
					<div class="sfdp-tool-con"><a data="upload_img">&#8194;<b class='ico'>Ρ</b>&#8194;单图组件</a></div>
					<div class="sfdp-tool-con"><a data="edit">&#8194;<b class='ico'>Ε</b>&#8194;富文本框</a></div>
				<div class="sfdp-cl"></div>
				<div class="sfdp-tool-title sfdp-mt10">内置组件 System control library</div>
					<div class="sfdp-tool-con" ><a data="system_user">&#8194;<b class='ico'>ρ</b>&#8194;系统用户</a></div>
					<div class="sfdp-tool-con" ><a data="system_role">&#8194;<b class='ico'>Θ</b>&#8194;系统角色</a></div>
				<div class="sfdp-cl"></div>
				<div class="sfdp-tool-title sfdp-mt10">子表单设计 Form control library</div>
				<div class='sfdp-tool-fix'onclick='sfdp.build_fb()'><a>&#8194;<b class='ico'>§</b>&#8194; 添加附表 </a></div>
				<div class="sfdp-cl"></div>
			</div>
			<div class="sfdp-con" style="float: left; width: calc(100% - 600px);">
				<!-- <div><div class="button">配置</div><div class="button">保存</div><div class="button">预览</div><div class="button">帮助</div></div> -->
				<div id="sfdp-main"></div>
			</div>
			<div class="sfdp-con" style="float: right; width: 360px;">
				<div class="sfdp-att">
				<br/><br/><div style="padding: 24px 48px;"> <h1>\﻿ (•◡•) / </h1><p> Sfdp V5.0正式版<br/><span style="font-size:19px;">超级表单开发</span></p><span style="font-size:15px;">[ © Guoguo <a href="https://www.cojz8.com/">Sfdp</a> 版权禁删！ ]</span></div>
				</div>
			</div>
			<div class="sfdp-cl"></div>
		</div>
		<script src="{$patch}lib/jquery-1.12.4.js"></script>
		<script src="{$patch}lib/layer/2.4/layer.js"></script>
		<script type="text/javascript" src="{$patch}sfdp.5.0.js?v=5.0.322222"></script>
		<script type="text/javascript" src="{$patch}lib/jquery-ui.js"></script>
		<script type="text/javascript" language="javascript">
			var look_db = {$look};
		    $(function(){
				sfdp.int_data({$json});
			});
			$( ".sfdp-tool-con" ).draggable({
				  connectToSortable: ".fb-fz",
				  helper: "clone",
				  revert: "invalid",
				  cursor: "move"
			});
			$( "#sfdp-main" ).sortable({
			    stop: function( event, ui ) {
			        let obj = {}
			        let obj2 = []
			        const json_data = JSON.parse(localStorage.getItem("json_data"));
			        const sortedList = $("#sfdp-main div.sfdp-rows ")
			        for(let i= 0; i< sortedList.length ;i++){
			            let item = sortedList[i]
			            //删除TR数据
			            sfdp.dataSave('', item.id , 'tr_del');
			            //重新写入数据库
			            sfdp.dataSave(json_data.list[item.id], String(item.id), 'tr')
			        }
			    }
			
			});
            $( "#sfdp-main" ).disableSelection();
            
			$( "#fb-fz" ).sortable({
			  cancel: ".fb-disabled"
			});
			$("#up_save").click(function(){
				var int_data = localStorage.getItem("json_data");
				var id='{$fid}';
				$.ajax({  
					 url:'{$save}',
					 data:{ziduan:int_data,id:id},  
					 type:'post',  
					 cache:true,  
					dataType:'json',			 
					 success:function(data) {  
						if(data.code==0){
							sfdp.ShowTip('success');
						}else{
							sfdp.ShowTip(data.msg);
						}
					  }
				 }); 
			})
  </script>
php;
}
	/**
	 * 脚本函数
	 *
	 * @param  Array $info 设计数据
	 * @param  int   $sid 设计ID
	 */
	public static function script($info,$sid){
		$tmp = self::commontmp('Sfdp超级表单设计器');
		$urls= unit::gconfig('url');
        $info['s_fun'] = $info['s_fun'] ?? '';
		$action = $urls['api'].'?act=script&sid='.$sid;
		$patch = unit::gconfig('static_url');
	return <<<php
	{$tmp['css']}
		<link rel="stylesheet" type="text/css" href="{$patch}lib/codemirror/codemirror.css" />
		<link rel="stylesheet" type="text/css" href="{$patch}lib/codemirror/dracula.css" />
		<script src="{$patch}lib/codemirror/codemirror.js"></script>
		<script src="{$patch}lib/codemirror/javascript.js"></script>
			<form action="{$action}" method="post" name="form" id="form">
			<input type="hidden" name="sid" value="{$sid}">
		<table class="table">
			<tr valign="center">
			<td style='width:35px;text-align:center'>脚本说明</td>
			<td style='width:330px;text-align: left;'>
			脚本说明：<br/>
			load_satr_fun();//前置函数在表单构建器之前会执行，比如插入必要的组件代码。<br/>
			</bq>load_end_fun();//后置代码用于页面加载完成后的代码执行<br/>
			</pre>
			</td>
			</tr>
			<tr valign="center">
			<td style='width:35px;text-align:center'>单据脚本</td><td style='width:330px' >
			<textarea placeholder="请填写JQ脚本代码！" name='function' type="text/plain" style="width:100%;height:450px;display:inline-block;" id='code'>{$info['s_fun']}</textarea> </td>
			</tr>
			<tr valign="center">
			<td style='text-align:center' colspan='2'><button  class="button" type="submit">&nbsp;&nbsp;保存&nbsp;&nbsp;</button>
				<button  class="button" type="button" onclick="sfdp.layer_close()">&nbsp;&nbsp;取消&nbsp;&nbsp;</button></td>
			</tr>
		</table>
	</form>

	{$tmp['js']}
	<script type="text/javascript" language="javascript">
		  var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
			lineNumbers: true,
         lineWrapping: true,
			mode: "text/typescript",
			theme: "dracula",	//设置主题
			 matchBrackets: true,
		  });
	</script>
</body>
</html>
php;
}
	/**
	  * 公用模板方法
	  *
	  **/
	static function commontmp($title){
		$patch = unit::gconfig('static_url');
		$css = '<link rel="stylesheet" type="text/css" href="'.$patch.'sfdp.5.0.css?v=5.0.1" />';
		$js = '<script src="'.$patch.'lib/jquery-1.12.4.js"></script>
		<script src="'.$patch.'lib/layer/2.4/layer.js"></script>
		<script src="'.$patch.'sfdp.5.0.js?v=5.0.1"></script>';
		$head ='<title>'.$title.'</title><head>'.$css.'</head><body style="background-color: white;">';
		return ['head'=>$head,'css'=>$css,'js'=>$js];
	}
}

