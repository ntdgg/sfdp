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
	
	public static function index($data){
		$tmp = self::commontmp('Sfdp超级表单设计器');
		$urls= unit::gconfig('url');
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
		   $btn = '<a onClick=commonfun.openfullpage("设计——'.$k['s_bill'].'","'.$urls['api'].'?act=desc&sid='.$v['id'].'") class="button">设计</a><a onClick=commonfun.openfullpage("元素管理——'.$k['s_bill'].'","'.$urls['api'].'?act=ui&sid='.$v['id'].'") class="button">元素</a>';
		   if($v['s_field'] <> 1){
			   $fix = $urls['api'].'?act=fix&sid='.$v['id'];
			   $btn .= '<a onClick=commonfun.Askshow("'.$fix.'","部署后将生成最新版本,确定是否执行?") class="button">部署</a><a onClick=commonfun.openfullpage("脚本管理——'.$k['s_bill'].'","'.$urls['api'].'?act=script&sid='.$v['id'].'") class="button">脚本</a>';
		   }
		   if($v['s_db_bak']==1){
			   $btn .='<a onClick=commonfun.Askshow("'.$urls['api'].'?act=deldb&sid='.$v['id'].'","删除备份数据库,是否执行?")  class="button">DelDb</a>';
			   $btn .='<a onClick=add_fun('.$v['id'].')  class="button">Menu</a>';
		   }
		   $tr .='<tr class="text-c"><td>'.$v['s_bill'].'</td><td>'.$v['s_title'].'</td><td>'.date('Y/m/d H:i',$v['add_time']).'</td><td>'.$k['s_design'].'</td><td>'.$status[$v['s_look']].'（'.$k['s_db'].'）</td><td>'.$btn.'</td></tr>';	
		}
		return <<<php
		{$tmp['css']}{$tmp['head']}{$tmp['js']}
			</head>
		<body>
		<div class="page-container">
			<div style='float: left;width:8%'>
				<a onClick='commonfun.Askshow("{$create_url}","是否创建新表单？")' class="button">创建</a><hr/><a onclick="location.reload();" class="button ">刷新</a>
			</div>
			<div style='float: left;width:92%'>
				<table class="table" >
					<tr class="text-c"><th >编码</th><th>标题</th><th>发布时间</th><th>启用状态</th><th>锁定状态</th><th>操作</th></tr>
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
		function getmenu(sid){
			var node = $('#nodesss').val();
			if(node==''){
				commonfun.ShowTip('请选择节点信息！');
				return;
			}
			var url = "{$node_url}&sid="+sid+"&node="+node;
			commonfun.Askshow(url,"再次确认是否创建目录，,是否执行?");
			
		}
		</script>
		</body>
		</html>
php;
	}
	public static function fun($data){
		$tr = '';
		$tmp = self::commontmp('Sfdp超级表单设计器');
		$urls= unit::gconfig('url');
		$fun_save = $urls['api'].'?act=fun_save&sid=';
		foreach($data as $k=>$v){
		   $status = ['编辑中','已启用'];
		   $tr .='<tr class="text-c"><td>'.$v['bill'].'</td><td>'.$v['title'].'</td><td>'.$k['fun_name'].'</td><td>'.date('Y/m/d H:i',$v['add_time']).'</td><td>'.$k['add_user'].'</td><td>'.$status[$v['status']].'</td><td><a onClick=add_fun("'.$v['title'].'","'.$v['fun_name'].'","'.$v['function'].'","'.$v['id'].'")	class="button">编辑</a></td></tr>';	
		}
		return <<<php
		{$tmp['css']}{$tmp['head']}{$tmp['js']}
<div class="page-container">
<div style='float: left;width:8%'>
	<a onClick='add_fun()' class="button">创建</a><a onclick="location.reload();" class="button ">刷新</a><hr/>
</div>
<div style='float: left;width:92%'>
	<table class="table">
			<tr class="text-c"><th width="150">编码</th><th width="150">标题</th><th width="100">函数名</th><th width="80">发布时间</th><th width="50">添加人</th><th width="50">状态</th><th width="200">操作</th>
			</tr>{$tr}
	</table>
</div>
</div>
</body>
</html>
<script>
function add_fun(title='',name='',fun='',id=''){
	var html ='<form action="" method="post" name="form" id="form">'+
			  '<table class=table id="table_view"><tr><td>函数标题</td><td style="text-align:left"><input type="text" id="title" value="'+title+'"></td></tr>'+
			  '<tr><td>函数名称</td><td style="text-align:left"><input type="text" id="name" value="'+name+'"></td></tr>'+
			  '<tr><td>填写函数</td><td><textarea placeholder="请填写SQL代码！" id="fun" type="text/plain" style="width:100%;height:100px;">'+fun+'</textarea></td></tr><tr><td colspan=2><a class="button" onclick="save_fun('+id+')">提交</a></td></tr></table></form>';
		layer.open({
		  type: 1,
		  area: ['520px', '340px'], //宽高
		  content: html
		});
}
function save_fun(id){
	var NameExp = /^(?!_)(?!.*?_$)[a-z_]+$/;
	var title = $('#title').val();
	var name = $('#name').val();
	if(!NameExp.test(name)){
		commonfun.ShowTip('函数名称只能为英文小写字母加下划线组合！');
		return;
	}
	var fun = $('#fun').val();
	var url = "{$fun_save}";
	commonfun.sAjax(url,{title:title,name:name,fun:fun,id:id});
}
</script>
php;
}
public static function ui($data){
	$tr ='';
	$tmp = self::commontmp('Sfdp超级表单设计器');
	foreach($data as $k=>$v){
		   $status = ['编辑中','已启用'];
		   $tr .='<tr valign="top"><td style="width:35px;text-align:left">元素标识：'.$v['tpfd_id'].'<br/>元素名称：'.$v['tpfd_name'].'('.$v['td_type'].')</td><td style="width:330px" >字段名：'.$v['tpfd_db'].'('.$v['tpfd_dblx'].')('.$v['tpfd_dbcd'].') |列表：'.$v['tpfd_list'].' | 查询：'.$v['tpfd_chaxun'].' <br/></td></tr>';	
		}	
	return <<<php
	{$tmp['css']}<div class="page-container"> 
			<table><tr><td colspan='2' style='width:330px'><pre>元素说明：<br>特别说明：显示设计的所有UI元素</pre></td></tr>
				{$tr}
			</table>
			</div>
	</body>
	</html>
php;
}
public static function desc($json,$fid,$look){
	$patch = unit::gconfig('static_url');
	$urls= unit::gconfig('url');
	$save = $urls['api'].'?act=save';
	return <<<php

 <body> 
  <link rel="stylesheet" href="{$patch}css/sfdp.desc.css" /> 
  <link rel="stylesheet" href="{$patch}css/sfdp.common.css" /> 
   <div class="fb-main"> 
    <ul style="list-style: none;padding: 0 0 0 20px;border-bottom: 1px solid #ccc;display: block;">
     <li><h3>SFDP 超级表单开发平台—V5.0</h3></li>
    </ul> 
    <div class="fb-left"> 
     <ul class="fb-ul">
      <li class="active">设计控制区 Design control area</li>
     </ul> 
     <div class="fb-content"> 
      <div class="section"> 
       <a class="fb-button" onclick="showLayer('config',1,0)"><img src='{$patch}images/set.png'> 配置</a> 
       <a class="fb-button" id='save'><img src='{$patch}images/help.png'> 保存</a> 
       <a class="fb-button" onclick="showLayer('view',1,0)"><img src='{$patch}images/help.png'> 预览</a> 
       <a class="fb-button"  onClick="commonfun.openfullpage('官网','//cojz8.com')"><img src='{$patch}images/help.png' > 帮助 </a>
      </div> 
     </div> 
	 <ul class="fb-ul">
      <li class="active">页面布局 Form control library</li>
     </ul>
	  <div class="fb-content"> 
      <div class="section"> 
       <a class="fb-button" onclick="addtr(1)"><img src='{$patch}images/table.png'> 一格 </a> 
       <a class="fb-button" onclick="addtr(2)"><img src='{$patch}images/table.png'> 二格 </a> 
       <a class="fb-button" onclick="addtr(3)"><img src='{$patch}images/table.png'> 三格 </a> 
       <a class="fb-button" onclick="addtr(4)"><img src='{$patch}images/table.png'> 四格 </a> 
      </div> 
     </div> 
     <ul class="fb-ul">
      <li class="active">表单控件库 Form control library</li>
     </ul> 
     <div class="fb-content"> 
      <div class="section"> 
		  <a data="text" class="fb-button fbtd"><img src='{$patch}images/text.png'> 文本 </a> 
		   <a data="checkboxes" class="fb-button fbtd"><img src='{$patch}images/checkboxes.png'>多选框</a> 
		   <a data="radio" class="fb-button fbtd"><img src='{$patch}images/radio.png'>单选框</a> 
		   <a data="dropdown" class="fb-button fbtd"><img src='{$patch}images/dropdown.png'> 下拉 </a> 
		   <a data="date" class="fb-button fbtd"><img src='{$patch}images/time.png'> 日期时间</a> 
		   <a data="textarea" class="fb-button fbtd"><img src='{$patch}images/textarea.png'> 多行文本</a> 
		   <a data="html" class="fb-button fbtd"><img src='{$patch}images/html.png'> Html控件</a> 
		   <a data="wenzi" class="fb-button fbtd"><img src='{$patch}images/text.png'> 文字显示</a> 
		   <a data="upload" class="fb-button fbtd"><img src='{$patch}images/upload.png'> 上传组件</a> 
      </div> 
     </div> 
    </div> 
    <div class="fb-center" style="padding-top: 0px; "> 
     <table id="table_center"> 
      <tbody> 
       <tr class="table_tr">
        <th  colspan="4">正在设计：<b id="fb_name"></b></th>
       </tr> 
      </tbody> 
     </table> 
    </div> 
    <div class="fb-right"> 
     <ul class="fb-ul">
      <li class="active">日志输出区 Logs Out</li>
     </ul> 
     <div id="logout" class="logout"></div> 
    </div> 
   </div> 
  </div>
  <style>

  </style>
<div class="tpfd-pop" id="pop">
	<div class="tpfd-wrap">
	<div style='font-size: 16px;font-weight: 800;'>系统配置 <span id="zd_id"></span>
	<input id="showtype" type="hidden" value="other">
	</div>
		<span class="tpfd-close">X</span>
			<form id="myform">  
		<div class="tpfd-content" id='table'>
		</div>
		</form>  
		<button class="tpfd-ok">保 存</button>
	</div>
</div>
 </body>
</html>
<script>
//全局变量、锁定数据库名称不允许修改
var look_db = {$look};
</script>
	<script src="{$patch}lib/jquery-1.12.4.js"></script>
	<script src="{$patch}lib/jquery-ui.js"></script>
	<script src="{$patch}lib/layer/2.4/layer.js"></script>
	<script src="{$patch}js/sfdp.fun.js"></script>
	<script src="{$patch}js/sfdp.unit.js"></script>
	<script src="{$patch}sfdp.5.0.js"></script>
	<script src="{$patch}js/sfdp.view.js"></script>
	<script src="{$patch}js/sfdp.field.js"></script>
   <script>
   
  $( function() {
		//初始化设计数据
		int_data({$json});
		//设置拖拽单元属性
		$( ".fbtd" ).draggable({
		  connectToSortable: ".fb-fz",
		  helper: "clone",
		  revert: "invalid",
		  cursor: "move"
		});
		$( "#fb-fz" ).sortable({
		  cancel: ".fb-disabled"
		});
	});
	$("#save").click(function(){
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
					commonfun.ShowTip('success');
				}else{
					commonfun.ShowTip(data.msg);
				}
			  },  
			  error : function() {  
			  }  
		 }); 
		})
  </script>
php;
}
public static function script($info,$sid){
	$tr ='';
	$tmp = self::commontmp('Sfdp超级表单设计器');
	$urls= unit::gconfig('url');
	$action = $urls['api'].'?act=script&sid='.$sid;
	return <<<php
	{$tmp['css']}
			<form action="{$action}" method="post" name="form" id="form">
			<input type="hidden" name="sid" value="{$sid}">
		<table class="table">
			<tr valign="center">
			<td style='width:35px;text-align:center'>脚本说明</td>
			<td style='width:330px;text-align: left;'>
			<pre><bq>&lt;h2</bq><bq>&gt;</bq>脚本说明：<bq style="">&lt;/h2</bq><bq>&gt;</bq><br><bq>&lt;p</bq><bq>&gt;</bq>特别说明：脚本支持所有的JQ语句，但系统内置了几个常用的<bq>&lt;/p</bq><bq>&gt;</bq><br><bq>&lt;p</bq><bq>&gt;</bq>load_satr_fun();//前置函数<bq>&lt;/p</bq><bq>&gt;</bq><br><bq>&lt;p</bq><bq>&gt;</bq>前置函数在表单构建器之前会执行，比如插入必要的组件代码。<bq>&lt;/p</bq><bq>&gt;</bq><br><bq>&lt;p</bq><bq>&gt;</bq>load_end_fun();//后置函数<bq>&lt;/p</bq><bq>&gt;</bq><br><bq>&lt;p</bq><bq>&gt;</bq>后置代码用于页面加载完成后的代码执行<bq>&lt;/p</bq><bq>&gt;</bq>
			</pre>
			</td>
			</tr>
			<tr valign="center">
			<td style='width:35px;text-align:center'>单据脚本</td><td style='width:330px' >
			<textarea placeholder="请填写JQ脚本代码！" name='function' type="text/plain" style="width:100%;height:350px;">{$info['s_fun']}</textarea> </td>
			</tr>
			<tr valign="center">
			<td style='text-align:center' colspan='2'><button  class="button" type="submit">&nbsp;&nbsp;保存&nbsp;&nbsp;</button>
				<button  class="button" type="button" onclick="layer_close()">&nbsp;&nbsp;取消&nbsp;&nbsp;</button></td>
			</tr>
		</table>
		
	</form>
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
		$css = '<link rel="stylesheet" href="'.$patch.'css/sfdp.common.css" /><link rel="stylesheet" type="text/css" href="'.$patch.'sfdp.5.0.css" />';
		$js = '<script src="'.$patch.'lib/jquery-1.12.4.js"></script>
		<script src="'.$patch.'lib/layer/2.4/layer.js"></script>
		<script src="'.$patch.'js/sfdp.fun.js"></script>';
		$head ='<title>'.$title.'</title><head>'.$css.'</head><body>';
		return ['head'=>$head,'css'=>$css,'js'=>$js];
	}
	
}

