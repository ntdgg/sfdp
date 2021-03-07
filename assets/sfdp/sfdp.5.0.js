	//Tpfd表单控件
	var fb_config_data = {
		name:'',//表单名称
		name_db:'',//数据表名称
		tpfd_id:'SFDP'+ commonfun.dateFormat(new Date(), "mmssS"),//表单ID
		tpfd_btn:{},
		tpfd_script:'',//数据表脚本
		tpfd_class :'',//数据表脚本
		list:{
			//设计数据
		},
		tpfd_time:commonfun.dateFormat(new Date(), "yyyy-MM-dd hh:mm:ss"),//表单设计时间
		tpfd_ver:'v5.0'//表单设计器版本
	};
	var NameExp = /^[\u4e00-\u9fa5]{0,}$/;
	var DbNameExp = /^(?!_)(?!.*?_$)[a-z0-9_]+$/;
	var DbFieldExp = /^(?!_)(?!.*?_$)[a-z_]+$/;
	var NumExp = /^[0-9]*[1-9][0-9]*$/;
	//日志输出区
	function logout(info){
		console.log(commonfun.dateFormat(new Date(), "mm:ss")+'] ' + info);
	}
	//表单构建
	function addtr(id,old_data='',showtype=''){ 
		if(!has_item('name')){
			return;
		}
		var $targetTbody= $("#table_center tbody");
		var $tr = $targetTbody.children("tr[class='table_tr']:last");
		if(old_data==''){
			var code = 'Tr'+commonfun.dateFormat(new Date(), "hhmmssS");
		}else{
			var code = old_data['tr'];
		}
		var html = commonfun.tpfd_tableui(code,id);

		var logs ='新增1*'+id+'单元行';
		if(old_data==''){
			commonfun.dataSave({tr:code,data:{},type:id},code,'tr');
			logout(logs);
		}else{
			logout('[恢复]'+logs);
		}
		if(showtype==''){
			$tr.after(html);
			commonfun.delTr();
			$( ".fb-fz" ).sortable({
					opacity: 0.5,
					revert: true,
					stop: function( event, ui ) {
						var type = $(this).children('a').attr("data");
						var parent_code = $(this).parent().attr("id"); //获取Tr的ID值 
						if($(this).html().indexOf("code") >= 0 ) { 
							var code = '<span class="code">'+parent_code+'</span><span class="code2">x</span>';
						}else{
							var code = '';
						}
						var html =fb_tpl(type,code,parent_code,$(this).attr("id"));
						//禁止本单元格再放置其他控件
						$(this).removeClass("fb-fz");
						$(this).removeClass("ui-sortable");
						$(this).addClass("fb-disabled");
						$( ".fb-disabled" ).sortable( "disable" );
						$(this).html(html);
					}
			});
		}else{
			return html;
		}
	}
	
	//文本转换
	function fb_tpl(type,code,parent_code,td_xh,old_data=0){
		if(old_data==0){
			var td_id = type+'_'+ commonfun.dateFormat(new Date(), "mmssS");
		}else{
			var td_id =old_data;
		}
		var labid = 'onclick=showLayer("'+type+'","'+td_id+'","'+parent_code+'") id="label'+td_id+'"';
		var html =commonfun.tpfd_change(labid,type);
		if(old_data==0){
			commonfun.dataSave({td:td_xh,td_type:type,tpfd_id:td_id},parent_code,'tr_data');
		}
		return html+code;
	}
	//
	function fb_set(type,id,parent_code){
		$('#zd_id').html(id);
		var all_data = JSON.parse(localStorage.getItem("json_data"));
		var default_data = all_data['list'][parent_code]['data'][id];
		if(default_data.tpfd_db==undefined){
			var tpfd_db = {tpfd_id: id,tr_id:parent_code, tpfd_db:'',tpfd_name: "", tpfd_dbcd: "",tpfd_zanwei: "", tpfd_moren: "", tpfd_chaxun: "no",tpfd_list: "no"};
		}else{
			var tpfd_db =default_data;
		}
		return commonfun.tpfd_return(type,tpfd_db);
	}
	function fb_set_return(data){
		$('#label'+data.tpfd_id).html(data.tpfd_name+'：');
		$('.tpfd-pop').fadeOut();
	}
	$(".code2").unbind('click').click(function(){
		var tr_id = $(this).parent().parent().attr("id");
		commonfun.dataSave('',tr_id,'tr_del');
		logout('删除了单元行'+tr_id);
		$(this).parent().parent().remove();
	});
	//点击保存按钮
	$('.tpfd-ok').on('click', function() {
		var params = commonfun.fromdata($("#myform")); //将表单序列化为JSON对象  
		if($('#showtype').val()=='view'){
			$('.tpfd-pop').fadeOut();
			commonfun.ShowTip(' 界面预览成功 ');
			logout('界面预览成功！');
		}else{
			if(params.name!=undefined){
				if(!NameExp.test(params.name)){
					commonfun.ShowTip(' Name格式不正确 ');
					return;
				}
				if(!DbNameExp.test(params.name_db)){
					commonfun.ShowTip(' Dbname格式不正确 ！');
					logout('仅允许使用小写字母和数字和下划线~');
					return;
				}
				$('#fb_name').html(params.name+'(DbTable:'+params.name_db+')');
				var json_data = JSON.parse(localStorage.getItem("json_data"));
				console.log(params);
					json_data['name']=params.name;
					json_data['name_db']=params.name_db;
					json_data['tpfd_btn']=params.tpfd_btn;
					json_data['tpfd_class']=params.tpfd_class;
					json_data['tpfd_script']=params.tpfd_script;
					localStorage.setItem("json_data",JSON.stringify(json_data));
					$('.tpfd-pop').fadeOut();
					logout('初始化配置成功！');
			}
		}
	});
	$('.data-ok').on('click', function() {
		var params = commonfun.fromdata($("#dataform")); //将表单序列化为JSON对象 
		console.log(params.tpfd_db);
		if(params.tpfd_db==undefined){
			commonfun.ShowTip('请选择要设置的字段信息~');
			return;
		}
		if(!DbFieldExp.test(params.tpfd_db)){
			commonfun.ShowTip('　数据表段有误　');
			return;
		}
		if(params.tpfd_name==''){
			commonfun.ShowTip('　标题不能为空　');
			return;
		}
		fb_set_return(params);
		commonfun.dataSave(params,params.tr_id,'td_data');
		commonfun.ShowTip('保存成功！');
	});
	//配置项判断
	function has_item(Item){
		var json_data = JSON.parse(localStorage.getItem("json_data"));
		if(json_data[Item]==''){
			alert('参数' + Item + '未配置！');
			return ; 
		}
		return true;
	}
	function showLayer(type,id,parent_code){
		if(type=='config'){
			var json_data = JSON.parse(localStorage.getItem("json_data"));
			
			var xEdit='',xDel='',xStatus='',xWorkFlow='';
			if(json_data.tpfd_btn == 'undefined'){
				json_data.tpfd_btn = {};
			}
			var btnArray = json_data.tpfd_btn;
			
			if(isInArray(btnArray,'Edit')){
				xEdit ='checked';
			}
			if(isInArray(btnArray,'Del')){
				xDel ='checked';
			}
			if(isInArray(btnArray,'Status')){
				xStatus ='checked';
			}
			if(isInArray(btnArray,'WorkFlow')){
				xWorkFlow ='checked';
			}
			var html = '<table><tr><td><div>设置表单标题：<input name="name" type="text" value='+json_data.name+'></div></td></tr>'+
			'<tr><td><div>数据库表名称：<input name="name_db" type="text" value='+json_data.name_db+' '+((look_db) == '1' ? 'readonly' : '') +' ></div></td></tr>'+
			'<tr><td><div>设置列表控件：<input  name="tpfd_btn" value=add type="checkbox" checked  onclick="return false;">Add <input  name="tpfd_btn" value=Edit type="checkbox" '+xEdit+' >Edit <input  name="tpfd_btn" value=Del type="checkbox" '+xDel+' >Del <input  name="tpfd_btn" value=View type="checkbox" checked  onclick="return false;">View <input  name="tpfd_btn" value=Status '+xStatus+' type="checkbox">Status <input  name="tpfd_btn" value=WorkFlow type="checkbox" '+xWorkFlow+'>WorkFlow <br/>控件说明：status Workflow同时选中优先使用workflow</td></tr>'+
			'<tr><td><div>设置表单样式：<textarea id="tpfd_class" name="tpfd_class">'+json_data.tpfd_class +'</textarea><a onclick=commonfun.insFgf("tpfd_class")>分割</a></td></tr>'+
			'<tr><td><div>加载脚本控件：<textarea id="tpfd_script" name="tpfd_script">'+json_data.tpfd_script +'</textarea><a onclick=commonfun.insFgf("tpfd_script")>分割</a><br/>(一个脚本文件，后面需要加一个分隔符@@)</div></td></tr></table>';
		$('#showtype').val('config');
		$('#table').html(html);
		}else if(type=='view'){
			$('#showtype').val('view');
			var int_data = localStorage.getItem("json_data");
			var desc_data = JSON.parse(int_data);
			commonfun.showadd(desc_data,'add',true);
		}else{
			$('#showtype').val('other');
			$('#att').html(fb_set(type,id,parent_code));
			return;
		}
		var layer = $('#pop'),
			layerwrap = layer.find('.tpfd-wrap');
			layer.fadeIn();
		if(type=='view'){
			layerwrap.removeAttr("style");
			layerwrap.css({
				'width':'90%',
				'margin-left': '-10px',
				'top': '5%',
				'left': '5%',
				'height': '450px'
			});
		}else{
			layerwrap.removeAttr("style");
			layerwrap.css({'margin-top': -layerwrap.outerHeight() / 2});
		}
	}
	$('.tpfd-close').on('click', function() {
		$('.tpfd-pop').fadeOut();
	});
	function select_type(){
		var mycars = new Array()
			mycars['int'] = 11
			mycars['varchar'] = 255
			mycars['time'] = 0
			mycars['datetime'] = 0
			mycars['longtext'] = 0
		var tpfd_dblx = $("select[name='tpfd_dblx']").val();
		$("input[name='tpfd_dbcd']").val(mycars[tpfd_dblx]);
	}
	
	function recovery_input(old_data){
		var td_data = old_data.data;
		//如果有设计数据，则开始恢复表单数据
		if(!$.isEmptyObject(old_data.data)){
			for (x in td_data){
				var type = td_data[x]['td_type'];
				var parent_code = old_data['tr'];
				if($('#'+old_data['tr']).children('td').eq(td_data[x]['td']-1).html().indexOf("code") >= 0 ) { 
					var class_code = '<span class="code">'+parent_code+'</span><span class="code2">x</span>';
				}else{
					var class_code = '';
				}
				var html =fb_tpl(type,class_code,parent_code,td_data[x]['td'],td_data[x]['tpfd_id']);
				$('#'+parent_code).children('td').eq(td_data[x]['td']-1).removeClass("fb-fz");
				$('#'+parent_code).children('td').eq(td_data[x]['td']-1).removeClass("ui-sortable");
				$('#'+parent_code).children('td').eq(td_data[x]['td']-1).addClass("fb-disabled");
				$('#'+parent_code).children('td').eq(td_data[x]['td']-1).html(html);
				$( ".fb-disabled" ).sortable( "disable" );//阻止排序
				commonfun.delTr();
				if(td_data[x]['tpfd_name']!=undefined){
					fb_set_return(td_data[x]);
				} 
			}
		}
	}
	//用于初始化设计
	function int_data(int_data){
		if(int_data==1){
			var local_data = localStorage.getItem("json_data");
			if(local_data!=null){
				var desc_data = local_data;
			}
		}else{
			var desc_data = int_data;
		}
		if(int_data==null||int_data==1){
			localStorage.setItem("json_data",JSON.stringify(fb_config_data));
		}else{
				localStorage.setItem("json_data",JSON.stringify(int_data));
				$('#fb_name').html(desc_data.name+'(DbTable:'+desc_data.name_db+')');
				 for (x in desc_data.list){
					addtr(desc_data.list[x]['type'],desc_data.list[x]);//恢复表单布局设计
					recovery_input(desc_data.list[x]);//用于恢复表单字段内容
				 }
		}
	}
	