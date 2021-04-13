/*!
 * SFDP 表单设计器---公用方法库
 * http://cojz8.com
 *
 * 
 * Released under the MIT license
 * http://cojz8.com
 *
 * Date: 2020年3月4日23:34:39
 */
var Debug = false;//是否开启打印模式
var NameExp = /^[\u4e00-\u9fa5]{0,}$/; //中文名称正则运算
var DbNameExp = /^(?!_)(?!.*?_$)[a-z0-9_]+$/; //数据库名称校验
var DbFieldExp = /^(?!_)(?!.*?_$)[a-z_]+$/; //数据库字段校验
var NumExp = /^[0-9]*[1-9][0-9]*$/; //数字正则运算

var sfdp = {
	sfdp_int_data : function(){
		return	{
			name:'',//表单名称
			name_db:'',//数据表名称
			tpfd_id:'SFDP'+ sfdp.dateFormat(new Date(), "mmssS"),//表单ID
			tpfd_btn:{},
			tpfd_script:'',//数据表脚本
			tpfd_class :'',//数据表脚本
			list:{
				//设计数据
			},
			sublist:{
				//子表数据
			},
			tpfd_time:sfdp.dateFormat(new Date(), "yyyy-MM-dd hh:mm:ss"),//表单设计时间
			tpfd_ver:'v5.0'//表单设计器版本
		};
	},
	showview : function(int_data) {
		if(int_data==null){
			layer.msg('对不起，没有任何数据~');
		}else{
			$('#table').html('<div id="table_view" style="margin:10px"></div>');
			 for (x in int_data.list){
				var table = sfdp.table_build(int_data.list[x]['type'],int_data.list[x],'show');//恢复表单布局设计
				$("#table_view").append(table);
			 } 
			 var xh = 1;
			 //恢复子表布局
			  for (x in int_data.sublist){
				 var table = sfdp.sublist_build(int_data.sublist[x]['type'],int_data.sublist[x],'show',int_data.name_db+'_d'+xh,true);
				 $("#table_view").append(table);
				 xh++
			 }
			
			 for (x in int_data.sublists){
				  for (y in int_data.sublists[x]){
					var table = sfdp.sublist_r(int_data.sublists[x][y],true);						
					$("#"+int_data.name_db+"_d"+(Number(x)+1)+" .table .title").after(table);						
				  }					  
			 }
			 
			 
			$(document).attr("title",int_data.name);//修改页面标题
		}
	},
	showlist2 : function(int_data) {
		var  td_data= int_data;
			var html ='';
			for (x in td_data){
				var type = td_data[x].type || 'text';
				 html += sfdp.search_build(type,td_data[x]);
			}
		$('#search').html(html);
		sfdp.setDate();//初始化日期选择器
	},
	search_build:function(type,data){
			var field_att = '';
			var lab ='<div style="float: left;"><label class="sfdp-label">'+data.name+'：</label><div class="sfdp-input-block">';
			switch(type) {
				case 'text':
					var html =lab+'<input type="text"  value="'+data.zanwei+'" name="'+data.field+'"   class="sfdp-input">';
				break;
				case 'number':
					var html =lab+'<input type="text" value="'+data.zanwei+'" name="'+data.field+'"   class="sfdp-input">';
				break;
				case 'radio':
					data.tpfd_db = data.field;
					data.tpfd_data = eval('(' + data.type_data + ')');
					var html =lab+view_checkboxes_clss(data,'radio',field_att);
				break;
				case 'checkboxes':
					data.tpfd_db = data.field;
					data.tpfd_data = eval('(' + data.type_data + ')');
					var html =lab+view_checkboxes_clss(data,'checkbox',field_att);
				break;
				case 'dropdown':
					data.tpfd_db = data.field;
					data.tpfd_data = data.type_data;
					var html =lab+sfdp.view_select(data.tpfd_data,data.tpfd_db,0,field_att,data.id);
				break;
				case 'textarea':
					var html =lab+'<input type="text"  value="'+data.zanwei+'" name="'+data.field+'"   class="sfdp-input">';
				break;
				case 'date':
					var rqtype =['yyyy','MM-dd','yyyy-MM-dd','yyyyMMdd','yyyy-MM'];
					var html =lab+'<input '+field_att+' data-type="'+rqtype[data.data]+'" class="datetime sfdp-input" autocomplete="off"  type="text" id="date'+data.field+data.id+'" name="'+data.field+'" >';
				break;
				default:
				var html ='';
			}
			return html + '</div></div>';
        },
	showlist : function(int_data,Debug=false) {
		var td_data = int_data;
			var html ='';
			for (x in td_data){
				var type = td_data[x]['td_type'];
				 html += '   '+sfdp.view_field_return(type,td_data[x]);
			}
		$('#search').html(html);
	},
	showadd : function(int_data,showtype='add',Debug=false) {
		if(int_data==null){
			layer.msg('对不起，没有任何数据~');
		}else{
			if(Debug==true){
				var btn ='';
				}else{
				var btn = '<div class="sfdp-rows-view" style="text-align: center;margin-top: 15px;"><a  class="button savedata" type="submit">&nbsp;&nbsp;保存&nbsp;&nbsp;</a><a class="button" onclick=sfdp.layer_close()>&nbsp;&nbsp;取消&nbsp;&nbsp;</a></div>';
			}
			$('#table').html('<form action="" method="post" name="form" id="form"><input type="hidden" readonly name="name_db" value="'+int_data.name_db+'"><div id="table_view" style="margin:10px"><input type="hidden" readonly name="@subdata" id="subdata_subdata"></div></form>');
			//恢复主表单设计
			 for (x in int_data.list){
				var table = sfdp.table_build(int_data.list[x]['type'],int_data.list[x],showtype);//恢复表单布局设计
				$("#table_view").append(table);
			 }
			 var xh = 1;
			 //恢复子表布局
			  for (x in int_data.sublist){
				var table = sfdp.sublist_build(int_data.sublist[x]['type'],int_data.sublist[x],showtype,int_data.name_db+'_d'+xh);
				 $("#table_view").append(table);
				 xh++
			 }
			 if(showtype=='edit'){ 
				var tr ='';
				  for (x in int_data.sublists){
					  for (y in int_data.sublists[x]){
						var table = sfdp.sublist_r(int_data.sublists[x][y]);						
						$("#"+int_data.name_db+"_d"+(Number(x)+1)+" .table .title").after(table);						
					  }					  
				 }
			 }
			$("#table_view").append(btn);
			$(document).attr("title",int_data.name);
		}
		sfdp.setDate();//初始化日期选择器
	},
	sublist_r : function (td_data,show_type=false){
		
		var json = {1:'',2:'',3:'',4:'',5:'',6:'',7:'',8:'',9:'',10:'',11:'',12:''};
		var lend = 0;
		for (z in td_data){
				var type = td_data[z]['td_type'];
				if(show_type){
					var html =sfdp.show_field_return(type,td_data[z],true);	
					}else{
					var html =sfdp.edit_field_return(type,td_data[z],true);	
				}
				json[td_data[z]['td']] = html;
				lend ++;
		}
		var htmls ='<tr class="text-c">';
		for (var i=1;i<=lend;i++){
			htmls +='<td>'+json[i]+'</td>';
		}
		if(show_type){
			return htmls+'</tr>';
			}else{
			return htmls+'<td><a onclick="sfdp.fz(this)">增</a> <a onclick="sfdp.dl(this)">删</a></td></tr>';
		}
		
	},
	sublist_build : function(id,data='',types,stable,show_type=false){
		var code = data['id'];
		var td_data = data.data;
		var json = {1:'',2:'',3:'',4:'',5:'',6:'',7:'',8:'',9:'',10:'',11:'',12:''};
		var heads = {};
		for (x in td_data){
				var type = td_data[x]['td_type'];
				heads[x] = td_data[x]['tpfd_name'];
				if(types=='add'){
					var html =sfdp.view_field_return(type,td_data[x],true);
				}else if(types=='edit'){
					var html =sfdp.view_field_return(type,td_data[x],true);
				}else{
					var html =sfdp.show_field_return(type,td_data[x],true);
				}
				json[td_data[x]['td']] = html;
			}
		var htmls ='<tr class="text-c">';
		for (var i=1;i<=id;i++){
			htmls +='<td>'+json[i]+'</td>';
		}
		var head_html ='';
		$.each(heads, function() {
			head_html +='<th>'+this+'</th>';
		});
		if(show_type){
			html = '<div id="'+stable+'"><fieldset  id="'+data.id+'_field"  mode="field" > <legend id="'+code+'">'+data['title']+'</legend><table class="table table-border table-bordered table-bg"><thead><tr class="text-c title">'+head_html+'</tr></thead></table></fieldset></div>';	
			}else{
			html = '<form id="'+stable+'" class="sub_list" data="'+stable+'"><fieldset  id="'+data.id+'_field"  mode="field" > <legend id="'+code+'">'+data['title']+'</legend><table class="table table-border table-bordered table-bg"><thead><tr class="text-c title">'+head_html+'<th>操作</th></tr>'+htmls+'<td><a onclick="sfdp.fz(this)">增</a> <a onclick="sfdp.dl(this)">删</a></td></tr></thead></table></fieldset></form>';	
		}
		
		return html;
	},
	fz : function (obj){
		var tr = $(obj).parent().parent();
		tr.after(tr.clone());
	},
	dl :function (obj){
		$(obj).parent().parent().remove();  
	},
	table_build : function(id,old_data='',types){
		var code = old_data['tr'];
		var td_data = old_data.data;
			var json = {1:'',2:'',3:'',4:'',5:'',6:'',7:'',8:'',9:'',10:'',11:'',12:''};
			for (x in td_data){
				var type = td_data[x]['td_type'];
				if(types=='add'){
					var html =sfdp.view_field_return(type,td_data[x]);
				}else if(types=='edit'){
					var html =sfdp.edit_field_return(type,td_data[x]);
				}else{
					var html =sfdp.show_field_return(type,td_data[x]);
				}
				json[td_data[x]['td']] = html;
			}
		var lan =parseInt(12 / id);
		var htmls ='';
		for (var i=1;i<=id;i++){ 
			htmls +='<div class="col-'+lan+' sfdp-field-con-view">'+json[i]+'</div>';
		}
			html = '<div id="'+code+'" class="sfdp-rows-view">'+htmls+'</div>';	
		return html;
	},
	ShowTip : function(tip) {
		layer.msg(tip);
	},
	common_return : function(data) {
		if (data.code == 0) {
			layer.msg(data.msg,{icon:1,time: 1500},function(){
					parent.location.reload(); // 父页面刷新
			});          
		} else {
		   layer.alert(data.msg || data.responseJSON.message, {title: "错误信息", icon: 2});
		}
	},
	insFgf : function(id){
		$('#'+id).val($("#"+id).val()+'@@');
	},
	addoption : function(id,type='checkbox'){
		$('#checkboxes'+id).children('span').attr("onclick","sfdp.editoption("+id+")");
		$('#checkboxes'+id).children('span').html('Del');
		var html ='<div id="checkboxes'+(id+1)+'"><input type="'+type+'" name="tpfd_check" value='+(id+1)+' ><input name="tpfd_data" type="text" value="选项'+(id+2)+'"><span onclick=sfdp.addoption('+(id+1)+',"'+type+'")>Add</span></div>';
		$('#checkboxes'+id).after(html);
	},
	editoption : function(id){
		$('#checkboxes'+id).remove();
	},
	dateFormat : function (oDate, fmt){
		var o = {
			"M+": oDate.getMonth() + 1, //月份
			"d+": oDate.getDate(), //日
			"h+": oDate.getHours(), //小时
			"m+": oDate.getMinutes(), //分
			"s+": oDate.getSeconds(), //秒
			"q+": Math.floor((oDate.getMonth() + 3) / 3), //季度
			"S": oDate.getMilliseconds()//毫秒
		};
		if (/(y+)/.test(fmt)) {
			fmt = fmt.replace(RegExp.$1, (oDate.getFullYear() + "").substr(4 - RegExp.$1.length));
		}
		for (var k in o) {
			if (new RegExp("(" + k + ")").test(fmt)) {
				fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
			}
		}
		return fmt;
	},
	fromdata : function (froms){
		var o = {};  
        var arr = froms.serializeArray();  
        $.each(arr,function(){
            if (o[this.name]) {  //返回json中有该属性
                if (!o[this.name].push) { //将已存在的属性值改成数组
                    o[this.name] = [ o[this.name] ];
                }
                o[this.name].push(this.value || ''); //将值存放到数组中
            } else {  //返回json中没有有该属性
                o[this.name] = this.value || '';  //直接将属性和值放入返回json中
            } 
			
        });  
        return o; 
	},
	openpage : function(title, url, opt){
		if (typeof opt === "undefined") opt = {nav: true};
		w = opt.w || "80vw";
		h = opt.h || "80vh";
		return layer.open({
			type: opt.type || 2,
			area: [w, h],
			fix: false, // 不固定
			maxmin: true,
			shade: 0.4,
			title: title,
			content: url,
			success: function (layero, index) {
				if (typeof opt.confirm !== "undefined" && opt.confirm === true) {
					layero.find(".layui-layer-close").off("click").on("click", function () {
						layer.alert('您确定要关闭当前窗口吗？', {
							btn: ['确定', '取消'] //按钮
						}, function (i) {
							layer.close(i);
							layer.close(index);
						});
					});
				}
				// 自动添加面包屑导航
				if (true === opt.nav) {
					layer.getChildFrame('#nav-title', index).html($('#nav-title').html() + ' <span class="c-gray en">&gt;</span> ' + $('.layui-layer-title').html());
				}
				if (typeof opt.fn === "function") {
					opt.fn(layero, index);
				}
			}
		});
	},
	openfullpage : function(title, url, opt){
		return sfdp.openpage(title, url, $.extend({w: "100%", h: "100%"}, opt))
	},
	returnShow : function(data, callback, param){
		 if (data.code == 0) {
			layer.msg(data.msg,{icon:1,time: 1500},function(){
					parent.location.reload(); // 父页面刷新
			});          
		} else {
		   layer.alert(data.msg, {title: "错误信息", icon: 2});
		}	
	},
	Askshow : function(url,msg){
		layer.confirm(msg,function(index){
			sfdp.sGet(url);
		});
	},
	H5uploadhtml : function(ids){
		var html = '<label for="file-input">'+sfdp.Ico(0)+'</label></span>'+
		'<input type="file" accept="*/*" name="file" data-attr="'+ids+'" id="file-input" multiple  style="display: none">';
		layer.open({
		  type: 1,
		  title: false,
		  area: ['125px', '85px'], //宽高
		  content: html
		});
		sfdp.H5upload(ids);
		
	},
	H5upload : function (){
		$("#file-input").tpUpload({
			url: uploadurl,
			start: function () {
				layer_msg = layer.msg('正在上传中…', {time: 100000000});
			},
			progress: function (loaded, total, file) {
				$('.layui-layer-msg .layui-layer-content').html('已上传' + (loaded / total * 100).toFixed(2) + '%');
			},
			success: function (ret) {
				$('#'+ret.attr_id).val(ret.data);
				layer.closeAll();
			},
			error: function (ret) {
				layer.alert(ret);
			},
			end: function () {
				layer.close(layer_msg);
			}
		});
	},
	setDate :function(){
		$(".datetime").click(function(){
			var format = $(this).attr('data-type');
			var id = $(this).attr('id');
			  laydate.render({
				elem: '#'+id,
				format:format,
				show: true 
				,closeStop: '.datetime' 
			 });
		});
	},
	sGet : function(url,msg='操作成功'){
		$.get(url,function(data,status){
			if(status=='success'){
					 if (data.code == 0) {
						layer.msg(msg,{icon:1,time: 1500},function(){
							location.reload();
						});          
					}else{
					   layer.alert(data.msg, {title: "错误信息", icon: 2});
					}	
			}else{
				 layer.alert("状态: " + status, {title: "错误信息", icon: 2});
			}
			
		});
    },
	sAjax : function(url,data){
		$.ajax({  
			 url:url,
			 data:data,  
			 type:'post',  
			 cache:true,  
			dataType:'json',			 
			 success:function(ret) {  
				 if (ret.code == 0) {
						layer.msg(ret.msg,{icon:1,time: 1500},function(){
							location.reload();
						});          
					}else{
					   layer.alert(ret.msg, {title: "错误信息", icon: 2});
					}
			  },  
			  error : function() {  
						layer.alert('请求出错！', {title: "错误信息", icon: 2});
			  }  
		 }); 
	},
	sFun : function(url,data,setActive){
		$.ajax({  
			url:url,
			data:data,  
			type:'post', 
			dataType:'json',			 
			 success:function(ret) {  
				 layer.msg('请求成功！');
				 setActive(ret); 
				 return ret;
			  },  
			  error : function() {  
						layer.alert('请求出错！', {title: "错误信息", icon: 2});
			  }  
		 }); 
	},
	/*5.0.1 数据存储*/
	dataSave : function(data,key,type){
		var json_data = JSON.parse(localStorage.getItem("json_data"));
		switch(type) {
			case 'tr':
				json_data.list[key]=data;
				break;
			case 'sublist':
				json_data.sublist[key]=data;
				break;
			case 'sublist_title':
				json_data['sublist'][key].title = data;
				break;
			case 'sublist_datasave':
				data.td = json_data['sublist'][key]['data'][data.tpfd_id]['td'];
				data.td_type = json_data['sublist'][key]['data'][data.tpfd_id]['td_type'];
				json_data['sublist'][key]['data'][data.tpfd_id] = data;
				break;
			case 'sublist_data':
				json_data['sublist'][key]['data'][data.tpfd_id] = data;
				break;
			case 'sublist_del':
				delete json_data.sublist[key];
				break;
			case 'tr_del':
				delete json_data.list[key];
				break;
			case 'tr_data':
				json_data['list'][key]['data'][data.tpfd_id] = data;
				break;
			case 'td_data':
				data.td = json_data['list'][key]['data'][data.tpfd_id]['td'];
				data.td_type = json_data['list'][key]['data'][data.tpfd_id]['td_type'];
				json_data['list'][key]['data'][data.tpfd_id] = data;
				break;
			default:
		} 
		localStorage.setItem("json_data",JSON.stringify(json_data));
	},
	layer_close : function(){
		var index = parent.layer.getFrameIndex(window.name);
		parent.layer.close(index);
	},
	/*编辑返回字段*/
	edit_field_return:function(type,data,is_sub=false){
		if(data.tpfd_must==0){
			var maste = 'datatype="*"';
		}else{
			var maste = '';
		}
		if(data.tpfd_read==0){
			var read = 'readonly';
		}else{
			var read = '';
		}
		var field_att = maste + read;
		if (typeof(data['tpfd_name']) == 'undefined') {
			return sfdp.view_default(type,data);
		}else{
				var lab ='<div><label class="sfdp-label">'+data.tpfd_name+'：</label><div class="sfdp-input-block">';
				if(is_sub){
						lab ='';
					}
			switch(type) {
				case 'text':
					var html =lab+'<input type="text" '+field_att+' value="'+(data.value || '')+'" name="'+data.tpfd_db+'"  placeholder="" id="'+data.tpfd_id+'">';
					break;
				case 'radio':
					var html =lab+view_checkboxes_clss(data,'radio',field_att);
					break;
				case 'number':
					var html =lab+'<input type="number" '+field_att+' value="'+(data.value || '')+'" name="'+data.tpfd_db+'"  placeholder="" id="'+data.tpfd_id+'" class="sfdp-input">';
					break;
				case 'checkboxes':
				var html =lab+view_checkboxes_clss(data,'checkbox',field_att);
				break;
				case 'dropdown':
				var html =lab+sfdp.view_select(data.tpfd_data,data.tpfd_db,data.value,field_att,data.tpfd_id);
				break;
				case 'textarea':
				var html =lab+'<textarea  id="'+data.tpfd_id+'" name="'+data.tpfd_db+'" '+field_att+' placeholder="" >'+(data.value || '')+'</textarea>';
				break;
				case 'upload':
				var html =lab+sfdp.view_upload(data,data.tpfd_db,0);
				break;
				case 'date':
				var rqtype =['yyyy','MM-dd','yyyy-MM-dd','yyyyMMdd','yyyy-MM'];
				var html =lab+'<input value="'+(data.value || '')+'" '+field_att+' '+field_att+' data-type="'+rqtype[data.xx_type]+'" class="datetime" type="text" name="'+data.tpfd_db+'"  id="'+data.tpfd_id+'">';
				break;
				case 'html':
				var html =lab+data.tpfd_moren;
				break;
				case 'wenzi':
				var html =lab+data.tpfd_moren;
				break;
			}
		}
		return html + '</div></div>';
	},
	/*添加返回字段*/
	view_field_return:function(type,data,is_sub=false){
			if(data.tpfd_must==0){
				var maste = 'datatype="*"';
			}else{
				var maste = '';
			}
			if(data.tpfd_read==0){
				var read = 'readonly';
			}else{
				var read = '';
			}
			var field_att = maste + read;
			if (typeof(data['tpfd_name']) == 'undefined') {
				return sfdp.view_default(type,data);
			}else{
					var lab ='<div><label class="sfdp-label">'+data.tpfd_name+'：</label><div class="sfdp-input-block">';
					if(is_sub){
						lab ='';
					}
				switch(type) {
					case 'text':
					var html =lab+'<input type="text" '+field_att+' value="'+data.tpfd_zanwei+'" name="'+data.tpfd_db+'"  placeholder="" id="'+data.tpfd_id+'" class="sfdp-input">';
					break;
					case 'number':
					var html =lab+'<input type="number" '+field_att+' value="'+data.tpfd_zanwei+'" name="'+data.tpfd_db+'"  placeholder="" id="'+data.tpfd_id+'" class="sfdp-input">';
					break;
					case 'radio':
					var html =lab+view_checkboxes_clss(data,'radio',field_att);
					break;
					case 'checkboxes':
					var html =lab+view_checkboxes_clss(data,'checkbox',field_att);
					break;
					case 'dropdown':
					var html =lab+sfdp.view_select(data.tpfd_data,data.tpfd_db,0,field_att,data.tpfd_id);
					break;
					case 'textarea':
					var html =lab+'<textarea id="'+data.tpfd_id+'" name="'+data.tpfd_db+'" '+field_att+' placeholder="" ></textarea>';
					break;
					case 'upload':
					var html =lab+sfdp.view_upload(data,data.tpfd_db,0);
					break;
					case 'date':
					var rqtype =['yyyy','MM-dd','yyyy-MM-dd','yyyyMMdd','yyyy-MM'];
					var html =lab+'<input '+field_att+' '+field_att+' data-type="'+rqtype[data.xx_type]+'" class="datetime sfdp-input" type="text" name="'+data.tpfd_db+'"  id="'+data.tpfd_id+'">';
					break;
					case 'html':
					var html =lab+data.tpfd_moren;
					break;
					case 'wenzi':
					var html =lab+data.tpfd_moren;
					break;
				}
			}
			return html + '</div></div>';
        },
	view_select:function(data,field,value,att,selectid){
				var html ='<select name="'+field+'" style="width: 80px" '+att+' id="'+selectid+'"><option value="">请选择</option>';
				for (y in data){
					html += '<option value="'+y+'" '+((value) == data[y] ? 'selected' : '') +'>'+data[y]+'</option>';
					
				}
				return html+'</select>';
    },
	view_upload:function(data,field,value){
		if(data.tpfd_upload_api==0){
			var html = '<input type="text" name="'+data.tpfd_db+'" readonly id="'+data.tpfd_id+'" value='+(data.value || '')+'><span id="drag" style="width:80px;margin-left:5px">'+
					'<label onclick=sfdp.H5uploadhtml("'+data.tpfd_id+'")>'+sfdp.Ico(2)+'</label></span>';
			
		}else{
			if(data.value == undefined){
				data.value ='';
			}
			if(data.tpfd_upload_type == 1){
				var html = '<input type="text" name="'+data.tpfd_db+'" readonly id="'+data.tpfd_id+'" value='+data.value+'><span id="drag" style="width:80px;margin-left:5px">'+ '<label id="label_'+data.tpfd_id+'" onclick=sfdp.openpage("多文件上传","'+data.tpfd_upload_action+'?id='+data.tpfd_id+'&value='+data.value+'",{w:"50%",h:"60%"})>附件</label></span>';
			}else{
				var html = '<input type="text" name="'+data.tpfd_db+'" readonly id="'+data.tpfd_id+'" value='+data.value+'><span id="drag" style="width:80px;margin-left:5px">'+ '<label id="label_'+data.tpfd_id+'" onclick=sfdp.openpage("文件上传","'+data.tpfd_upload_action+'?id='+data.tpfd_id+'&value='+data.value+'",{w:"18%",h:"25%"})>附件</label></span>';
			}
		}
		return html;
    },
	view_default:function(type,data){
			switch(type) {
				case 'text':
					var html ='<label>文本控件：</label><input type="text"  placeholder="请输入信息~" >';
					break;
				case 'upload':    
					var html ='<label>上传控件：</label>上传';
					break;
				case 'checkboxes':
					var html ='<label>多选控件：</label>选项1<input type="checkbox"  placeholder="" > 选项2<input type="checkbox"  placeholder="" >';
					break;
				case 'radio':
					var html ='<label>单选控件：</label>选项1<input type="radio"  placeholder="" > 选项2<input type="radio"  placeholder="" >';
					break;
				case 'date':
					var html ='<label>时间日期：</label><input type="text"  placeholder=""  >';
					break;
				case 'dropdown':
					var html ='<label>下拉选择：</label><select ><option value ="请选择">请选择</option></select>';
					break;
				case 'textarea':
					var html ='<label>多行控件：</label><textarea   ></textarea>';
					break;
				case 'html':
					var html ='<label>HTML控件：</label><b style="color: blue;">Look this is a HTML</b>';
					break;
				case 'wenzi':
					var html ='<label>文字控件：</label>默认现实的文本';
					break;
				 default:
					var html ='1';
				}
			return html;
	},
	show_field_return:function(type,data,is_sub=false){
			var html ='';
			if (typeof(data['tpfd_name']) == 'undefined') {
				return sfdp.view_default(type,data);
			}else{
					var lab ='<div><label class="sfdp-label">'+data.tpfd_name+'：</label><div class="sfdp-input-block">';
					if(is_sub){
						lab ='';
					}
				switch(type) {
					case 'text':
						html =lab+data.value;
					break;
					case 'number':
						var html =lab+data.value;
						break;
					case 'radio':
						html =lab+data.value;
					break;
					case 'checkboxes':
						html =lab+data.value;
					break;
					case 'dropdown':
						html =lab+data.value;
					break;
					case 'textarea':
						html =lab+data.value;
					break;
					case 'upload':
						/*自定义开发模式*/
							if(data.tpfd_upload_api == 1){
								var html = lab+'<span id="drag" style="width:80px;margin-left:5px">'+
							'<label id="label_'+data.tpfd_id+'" onclick=sfdp.openpage("多文件上传","'+data.tpfd_upload_action+'?act=view&id='+data.tpfd_id+'&value='+data.value+'",{w:"50%",h:"60%"})>附件</label></span>';
							}else{
								var html = lab+'<a target="_blank" href="/'+data.value+'" >'+sfdp.Ico(1)+'</a>';
							}
					break;
					case 'date':
						html =lab+data.value;
					break;
					case 'html':
						html =lab+data.value;
					break;
					case 'wenzi':
						html =lab+data.value;
					break;
				}
			}
			return html + '</div></div>';
	},
	/*5.0.1 显示组件*/
	tpfd_xianshi:function(data){
		return '<div class="sfdp-form-item"><label class="sfdp-label">显示类型：</label><div class="sfdp-input-block"><textarea name="tpfd_moren" class="sfdp-input">'+data.tpfd_moren +'</textarea> </div></div>';
    },
	/*5.0.1 日期组件*/
    tpfd_date:function(data){
			var default_data =[{cid:0,clab:'yyyy'},{cid:1,clab:'MM-dd'},{cid:2,clab:'yyyy-MM-dd'},{cid:3,clab:'yyyyMMdd'},{cid:4,clab:'yyyy-MM'},{cid:5,clab:'yyyy-MM-dd HH:mm:ss'}];
			return '<div class="sfdp-form-item"><label class="sfdp-label">日期格式</label><div class="sfdp-input-block">'+sfdp.tpfd_select(default_data,'xx_type','2') +'</div></div>';
    },
	/*5.0.1 多选组建*/
	tpfd_checkboxes:function(data,type='checkbox'){
			if(data.tpfd_data==undefined){
				var default_data =[{cid:0,clab:'选项1',checked:''},{cid:1,clab:'选项2',checked:'checked'}];
			}else{
				var datas = [];
				for (x in data.tpfd_data){
					if(data.tpfd_check != undefined && isInArray(data.tpfd_check,x)){
						var check='checked';
					}else{
						var check='';
					}
					datas[x] = { cid:x,clab:data.tpfd_data[x],checked:check};
				}
				var default_data =JSON.parse(JSON.stringify(datas));
			}
            return '<div class="sfdp-form-item"><label class="sfdp-label"><input '+((data.xx_type) == '0' ? 'checked' : '') +'  name="xx_type" value=0 type="radio">静态方法</label><div class="sfdp-input-block">'+sfdp.tpfd_checkboxes_clss(default_data,type)+
			'</div></div><div class="sfdp-form-item"><label class="sfdp-label"><input '+((data.xx_type) == '1' ? 'checked' : '') +' name="xx_type" value=1 type="radio">动态方法</label><div class="sfdp-input-block"><input name="checkboxes_func" placeholder="函数" type="text" value="'+((data.checkboxes_func) == '' ? data.checkboxes_func : '')+'" class="sfdp-input" style="width:50%"></div></div>';
    },
	/*5.0.1 高级组件*/
	tpfd_gaoji:function(data){
		var default_data =[{cid:0,clab:'是'},{cid:1,clab:'否'}];
		if(data.tpfd_read=='undefined'){
			var tpfd_read = 0;
			var tpfd_must = 0;
		}else{
			var tpfd_read = data.tpfd_read;
			var tpfd_must = data.tpfd_must;
		}
		return '<div class="sfdp-form-item"><label class="sfdp-label">字段只读</label><div class="sfdp-input-block">'+sfdp.tpfd_select(default_data,'tpfd_read','1')+'</div></div> <div class="sfdp-form-item"><label class="sfdp-label">字段必填</label><div class="sfdp-input-block">'+sfdp.tpfd_select(default_data,'tpfd_must',tpfd_must)+'</div></div>'; 
	},
	/*5.0.1 默认组件*/
	tpfd_moren:function(data){
		return '<div class="sfdp-form-item"><label class="sfdp-label">占位内容</label><div class="sfdp-input-block"><input type="text" name="tpfd_zanwei" value="'+data.tpfd_zanwei +'" class="sfdp-input"></div></div> <div class="sfdp-form-item"><label class="sfdp-label">默认内容</label><div class="sfdp-input-block"><input name="tpfd_moren" type="text" value="'+data.tpfd_moren+'" class="sfdp-input"></div></div>';
	},
	/*5.0.1 列表组件*/
	tpfd_list:function(data){
		return '<div class="sfdp-form-item"><label class="sfdp-label">列表组件</label><div class="sfdp-input-block">'+sfdp.tpfd_select('','tpfd_list',data.tpfd_list)+' </div></div><div class="sfdp-form-item"><label class="sfdp-label">查询组件</label><div class="sfdp-input-block">'+sfdp.tpfd_select('','tpfd_chaxun',data.tpfd_chaxun)+'</div></div>';
	},
	/*5.0.1 选择组件*/
	tpfd_select:function(data,field,value){
		if(data==''){
			return '<select name="'+field+'"><option value="yes" '+((value) == 'yes' ? 'selected' : '') +'>是</option><option value="no" '+((value) == 'no' ? 'selected' : '') +'>否</option></select>';
		}else{
				var on ='';
			if(field=='tpfd_dblx'){
				var on ='onchange=sfdp.select_type()';
			}
			
			var html ='<select name="'+field+'"  '+on+'>';
			for (x in data){
				html += '<option value="'+data[x]['cid']+'" '+((data[x]['cid']) == value ? 'selected' : '') +'>'+data[x]['clab']+'</option>';
			}
			return html+'</select>';
		}
	},
	/*5.0.1 通用多选框*/
	tpfd_checkboxes_clss:function(data,type='checkbox'){
			var html ='';
			for (x in data){
				if(x == data.length-1){
					var btn ='<span onclick=sfdp.addoption('+x+',"'+type+'")>Add</span>';
				}else{
					var btn ='<span onclick=sfdp.editoption('+x+')>Del</span>';
				}
				html += '<div id="checkboxes'+x+'"><input '+data[x]['checked']+' name="tpfd_check" value='+x+' type="'+type+'"><input name="tpfd_data" type="text" value="'+data[x]['clab']+'">'+btn+'</div>';
			}
			return html;
		},
	/*5.0.1 通用设置组件*/
	tpfd_common:function(data){
			var default_field = [{cid:'int',clab:'int',checked:''},{cid:'time',clab:'time',checked:''},{cid:'varchar',clab:'varchar',checked:'checked'},{cid:'datetime',clab:'datetime',checked:''},{cid:'longtext',clab:'longtext',checked:''}];
			return '<input name="tpfd_id" type="hidden" value="'+data.tpfd_id +'"><input name="tr_id" type="hidden" value="'+data.tr_id +'"><div  style="margin: 5px;"><div class="sfdp-title">字段设置</div></div><div class="sfdp-form-item"><label class="sfdp-label">唯一标识</label><div class="sfdp-input-block"><input type="text"  autocomplete="off" value="'+data.tpfd_id +'" readonly class="sfdp-input"></div></div><div class="sfdp-form-item"><label class="sfdp-label">字段标题</label><div class="sfdp-input-block"> <input  name="tpfd_name" type="text"  value="'+data.tpfd_name +'"  class="sfdp-input"> </div></div><div class="sfdp-form-item"><label class="sfdp-label">字段名称</label><div class="sfdp-input-block"> <input name="tpfd_db" type="text" value="'+data.tpfd_db +'"  class="sfdp-input"></div></div> <div class="sfdp-form-item"><label class="sfdp-label">字段类型</label><div class="sfdp-input-block">'+sfdp.tpfd_select(default_field,'tpfd_dblx','varchar')+' </div></div> <div class="sfdp-form-item"><label class="sfdp-label">字段长度</label><div class="sfdp-input-block"><input style="width:80px" name="tpfd_dbcd" type="text" value="'+data.tpfd_dbcd +'" class="sfdp-input"></div></div>'+sfdp.tpfd_list(data);
        },
	/*5.0.1 上传控制组件*/
	tpfd_upload:function(data){
			if(data.tpfd_upload_type=='undefined'){
				var tpfd_upload_type = 0;
				var tpfd_upload_xz = 0;
				var tpfd_upload_api = 0;
				var tpfd_upload_action = 0;
			}else{
				var tpfd_upload_type = data.tpfd_upload_type;
				var tpfd_upload_xz = data.tpfd_upload_xz;
				var tpfd_upload_api = data.tpfd_upload_api;
				var tpfd_upload_action = data.tpfd_upload_action;
			}
			var default_data =[{cid:0,clab:'单文件上传'},{cid:1,clab:'多文件上传'}];
			var action_data =[{cid:0,clab:'内置接口'},{cid:1,clab:'自定义API'}];
			var word_type =[{cid:0,clab:'不限制'},{cid:1,clab:'*.jpg/*.png/*.gif'},{cid:1,clab:'*.doc/*.txt/*.xlx/*.xlxs/*.docx'}];
			
			return '<div class="sfdp-form-item"><label class="sfdp-label">上传配置</label>'+sfdp.tpfd_select(default_data,'tpfd_upload_type',tpfd_upload_type)+''+
				   ' '+sfdp.tpfd_select(word_type,'tpfd_upload_xz',tpfd_upload_xz)+'</div><div class="sfdp-form-item"><label class="sfdp-label">上传接口</label>'+sfdp.tpfd_select(action_data,'tpfd_upload_api',tpfd_upload_api)+''+
				   ' API:<input style="width:80px;display: inline;" name="tpfd_upload_action" type="text" value="'+(tpfd_upload_action || '') +'"  class="sfdp-input"></div>'; 
			
		},
	/*5.0.1 返回基础数据*/
	tpfd_return:function(type,data){
			switch(type) {
				case 'text':
					var html = '<form id="fieldform"><div>'+sfdp.tpfd_common(data)+sfdp.tpfd_moren(data)+sfdp.tpfd_gaoji(data)+'</div>';
					break;
				case 'number':
					var html = '<form id="fieldform"><div>'+sfdp.tpfd_common(data)+sfdp.tpfd_moren(data)+sfdp.tpfd_gaoji(data)+'</div>';
					break;
				case 'checkboxes':
					var html = '<form id="fieldform"><div>'+sfdp.tpfd_common(data)+sfdp.tpfd_checkboxes(data)+sfdp.tpfd_gaoji(data)+'</div>';
					break;
				case 'radio':
					var html = '<form id="fieldform"><div>'+sfdp.tpfd_common(data)+sfdp.tpfd_checkboxes(data,'radio')+sfdp.tpfd_gaoji(data)+'</div>';
					break;
				case 'date':
					var html = '<form id="fieldform"><div>'+sfdp.tpfd_common(data)+sfdp.tpfd_date(data)+sfdp.tpfd_gaoji(data)+'</div>';
					break;
				case 'dropdown':
					var html = '<form id="fieldform"><div>'+sfdp.tpfd_common(data)+sfdp.tpfd_checkboxes(data,'radio')+sfdp.tpfd_gaoji(data)+'</div>';
					break;
				case 'textarea':
					var html = '<form id="fieldform"><div>'+sfdp.tpfd_common(data)+sfdp.tpfd_moren(data)+sfdp.tpfd_gaoji(data)+'</div>';
					break;
				case 'html':
					var html = '<form id="fieldform"><div>'+sfdp.tpfd_common(data)+sfdp.tpfd_xianshi(data)+'</div>';
					break;
				case 'wenzi':
					var html = '<form id="fieldform"><div>'+sfdp.tpfd_common(data)+sfdp.tpfd_xianshi(data)+'</div>';
					break;
				case 'upload':    
					data.tpfd_list = 'no';
					data.tpfd_chaxun = 'no';
					data.tpfd_show = 'no';
					var html ='<form id="fieldform"><div>'+sfdp.tpfd_common(data)+sfdp.tpfd_upload(data)+sfdp.tpfd_gaoji(data)+'</div>';
					break;
				 default:
					var html ='';
			}
			return html + '<div style="margin-left: 5%;" class="button" onclick="sfdp.save_field()">保存</div></form>';;
        },
	/*5.0.1 拖拽进去设计容器的时候改变为响应的控件样式*/
	btn_to_input:function(labid,type){
			
			switch(type) {
			case 'text':
				var html ='<label '+labid+' class="sfdp-label">文本控件</label><div class="sfdp-input-block"><input  type="text"  placeholder="请输入信息~" disabled class="sfdp-input"></div>';
				break;
			case 'number':
				var html ='<label '+labid+' class="sfdp-label">数字控件</label><div class="sfdp-input-block"><input  type="number"  placeholder="" disabled class="sfdp-input"></div>';
				break;
			case 'upload':    
				var html ='<label '+labid+' class="sfdp-label">上传控件</label>上传';
				break;
			case 'checkboxes':
				var html ='<label '+labid+' class="sfdp-label">多选控件</label><div class="sfdp-input-block">选项1<input type="checkbox"  placeholder="" disabled> 选项2<input type="checkbox"  placeholder="" disabled></div>';
				break;
			case 'radio':
				var html ='<label '+labid+' class="sfdp-label">单选控件</label>选项1<input type="radio"  placeholder="" disabled> 选项2<input type="radio"  placeholder="" disabled>';
				break;
			case 'date':
				var html ='<label '+labid+' class="sfdp-label">时间日期</label><div class="sfdp-input-block"><input type="text"  placeholder="" disabled class="sfdp-input"></div>';
				break;
			case 'dropdown':
				var html ='<label '+labid+' class="sfdp-label">下拉选择</label><div class="sfdp-input-block"><select disabled class="sfdp-input"><option value ="请选择">请选择</option></select></div>';
				break;
			case 'textarea':
				var html ='<label '+labid+' class="sfdp-label">多行控件</label><div class="sfdp-input-block"><textarea  disabled class="sfdp-input"></textarea></div>';
				break;
			case 'html':
				var html ='<label '+labid+' class="sfdp-label">HTML控件</label><b style="color: blue;">Look this is a HTML</b>';
				break;
			case 'wenzi':
				var html ='<label '+labid+' class="sfdp-label">文字控件</label>默认现实的文本';
				break;
			 default:
				var html ='';
			}
			return html;
		},
		/*5.0.1 数据库设计自动带数据*/
		sys_config : function(){
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
			var html = '<form id="configform"> <div class="sfdp-form-item"><label class="sfdp-label">表单标题</label><div class="sfdp-input-block"><input name="name" type="text" value="'+json_data.name+'" class="sfdp-input"></div></div>'+
			'<div class="sfdp-form-item"><label class="sfdp-label">数据表名</label><div class="sfdp-input-block"><input name="name_db" type="text" value="'+(json_data.name_db)+'"'+((look_db) == '1' ? 'readonly' : '') +' class="sfdp-input"></div></div>'+
			'<div class="sfdp-form-item"><label class="sfdp-label">列表控件</label><div class="sfdp-input-block"><input  name="tpfd_btn" value=add type="checkbox" checked  onclick="return false;">Add <input  name="tpfd_btn" value=Edit type="checkbox" '+xEdit+' >Edit <input  name="tpfd_btn" value=Del type="checkbox" '+xDel+' >Del <input  name="tpfd_btn" value=View type="checkbox" checked  onclick="return false;">View <input  name="tpfd_btn" value=Status '+xStatus+' type="checkbox">Status <input  name="tpfd_btn" value=WorkFlow type="checkbox" '+xWorkFlow+'>WorkFlow <br/>控件说明：status Workflow同时选中优先使用workflow</div></div>'+
			'<div class="sfdp-form-item"><label class="sfdp-label">表单样式</label><div class="sfdp-input-block"><textarea id="tpfd_class" name="tpfd_class">'+json_data.tpfd_class +'</textarea><a onclick=sfdp.insFgf("tpfd_class")>分割</a></div></div>'+
			'<div class="sfdp-form-item"><label class="sfdp-label">脚本控件</label><div class="sfdp-input-block"><tr><td><div><textarea id="tpfd_script" name="tpfd_script">'+json_data.tpfd_script +'</textarea><a onclick=sfdp.insFgf("tpfd_script")>分割</a></div></div>(一个脚本文件，后面需要加一个分隔符@@)</div></td></tr><tr><td><div style="margin-left: 15%;" class="button" onclick="sfdp.save_data()">保存</div></td></tr></table></form> ';
			layer.open({
			  type: 1,
			  title:'基础配置 Base Set',
			  area: ['620px', '365px'], //宽高
			  content: html
			});
		},
		/*5.0.1 数据库设计自动带数据*/
		select_type : function(){
			var mycars = new Array()
				mycars['int'] = 11
				mycars['varchar'] = 255
				mycars['time'] = 0
				mycars['datetime'] = 0
				mycars['longtext'] = 0
			var tpfd_dblx = $("select[name='tpfd_dblx']").val();
			$("input[name='tpfd_dbcd']").val(mycars[tpfd_dblx]);
		},
		/*5.0.1 初始化系统配置*/
		int_data : function(int_data){
			if(int_data==1){
				var local_data = localStorage.getItem("json_data");
				if(local_data!=null){
					var desc_data = local_data;
				}
			}else{
				var desc_data = int_data;
			}
			if(int_data==null||int_data==1){
				localStorage.setItem("json_data",JSON.stringify(sfdp.sfdp_int_data()));
			}else{
				localStorage.setItem("json_data",JSON.stringify(int_data));
				$('#fb_name').html(desc_data.name+'(DbTable:'+desc_data.name_db+')');
				 for (x in desc_data.list){
					sfdp.build_view(desc_data.list[x]['type'],desc_data.list[x]);//恢复表单布局设计
					sfdp.recovery_input(desc_data.list[x]);//用于恢复表单字段内容
				 }
				 for (y in desc_data.sublist){
					sfdp.recovery_ui(desc_data.sublist[y]['type'],desc_data.sublist[y]);//恢复表单布局设计
					sfdp.recovery_sub(desc_data.sublist[y]);
				 }
				 
			}
		},
		/*5.0.1 恢复UI布局*/
		recovery_ui : function(id,data){
			var lan =parseInt(12 / id);
			var html ='';
			for (var i=1;i<=id;i++){ 
				 html +='<div class="col-'+lan+' sfdp-field-con fb-fz" id='+i+'></div>';
			}
			html = '<fieldset  id="'+data.id+'_field"  mode="field" > <legend id="'+data.id+'_title">'+(data.title || '请设置子表单标题')+'</legend><div class="sfdp-rows " id="'+data.id+'" mode="zibiao"><div class="view-action"><b class="ico" id="del" data="'+data.id+'" >㊀</b></div>'+html+'</div></fieldset>';	
			$('#sfdp-main').append(html);
			$( ".fb-fz" ).sortable({
					opacity: 0.5,
					revert: true,
					animation: 150,
					stop: function( event, ui ) {
						var type =$(this).children('div').children('a').attr("data");
						var parent_code = $(this).parent().attr("id");
						$(this).removeClass("fb-fz");
						$(this).removeClass("ui-sortable");
						$(this).addClass("fb-disabled");
						$( ".fb-disabled" ).sortable( "disable" );
						$(this).html(sfdp.bulid_tpl(type,parent_code,$(this).attr("id")));
					}
			});
		},
		/*5.0.1 恢复子表数据*/
		recovery_sub : function(old_data){
			var td_data = old_data.data;
			
			if(!$.isEmptyObject(old_data.data)){
				for (x in td_data){
					var type = td_data[x]['td_type'];
					var parent_code = old_data['id'];
					var html =sfdp.bulid_tpl(type,parent_code,td_data[x]['td'],td_data[x]['tpfd_id']);
					$('#'+parent_code).children('div').eq(td_data[x]['td']).removeClass("fb-fz");
					$('#'+parent_code).children('div').eq(td_data[x]['td']).removeClass("ui-sortable");
					$('#'+parent_code).children('div').eq(td_data[x]['td']).addClass("fb-disabled");
					$('#'+parent_code).children('div').eq(td_data[x]['td']).html(html);
					$( ".fb-disabled" ).sortable( "disable" );//阻止排序
					if(td_data[x]['tpfd_name']!=undefined){
						sfdp.fb_set_return(td_data[x]);
					} 
			}
		}
		},
		/*5.0.1 字段信息设置*/
		field_config : function(type,id,parent_code){
			var all_data = JSON.parse(localStorage.getItem("json_data"));
			var mode = $('#'+parent_code).attr('mode') || 'zhubiao';
		
			if(mode=='zhubiao'){
				var default_data = all_data['list'][parent_code]['data'][id];
				}else{
				var default_data = all_data['sublist'][parent_code]['data'][id];
			}
			if(default_data.tpfd_db==undefined){
				var tpfd_db = {tpfd_id: id,tr_id:parent_code, tpfd_db:'',tpfd_name: "", tpfd_dbcd: "",tpfd_zanwei: "", tpfd_moren: "", tpfd_chaxun: "no",tpfd_list: "no"};
			}else{
				var tpfd_db =default_data;
			}
			return sfdp.tpfd_return(type,tpfd_db);
		},
		/*5.0.1 字段信息保存*/
		save_field : function(){
			var params = sfdp.fromdata($("#fieldform")); //将表单序列化为JSON对象 
			if(params.tpfd_db==undefined){
				sfdp.ShowTip('请选择要设置的字段信息~');
				return;
			}
			if(!DbFieldExp.test(params.tpfd_db)){
				sfdp.ShowTip('　数据表段有误　');
				return;
			}
			if(params.tpfd_name==''){
				sfdp.ShowTip('　标题不能为空　');
				return;
			}
			if(params.tpfd_dbcd==''){
				sfdp.ShowTip('　数据字段长度不能为空　');
				return;
			}
			sfdp.fb_set_return(params);
			var mode = $('#'+params.tr_id).attr('mode') || 'zhubiao';
		
			if(mode=='zhubiao'){
					sfdp.dataSave(params,params.tr_id,'td_data');
				}else{
					sfdp.dataSave(params,params.tr_id,'sublist_datasave');
			}
			sfdp.ShowTip('保存成功！');
		},
		/*5.0.1 配置信息保存功能*/
		save_data : function(){
			var params = sfdp.fromdata($("#configform")); //将表单序列化为JSON对象  
			if(params.name!=undefined){
					if(!NameExp.test(params.name)){
						sfdp.ShowTip(' Name格式不正确 ');
						return;
					}
					if(!DbNameExp.test(params.name_db)){
						sfdp.ShowTip(' Dbname格式不正确 ！');
						logout('仅允许使用小写字母和数字和下划线~');
						return;
					}
					var json_data = JSON.parse(localStorage.getItem("json_data"));
						json_data['name']=params.name;
						json_data['name_db']=params.name_db;
						json_data['tpfd_btn']=params.tpfd_btn;
						json_data['tpfd_class']=params.tpfd_class;
						json_data['tpfd_script']=params.tpfd_script;
						localStorage.setItem("json_data",JSON.stringify(json_data));
						sfdp.ShowTip(' 设置成功 ！');
						setTimeout(function () {
							layer.closeAll();
						}, 2000);
						logout('初始化配置成功！');
			}
		},
		/*5.0.1 排序组件*/
		setSortable : function(){
			$( ".fb-fz" ).sortable({
					opacity: 0.5,
					revert: true,
					animation: 150,
					stop: function( event, ui ) {
						var type =$(this).children('div').children('a').attr("data");
						var parent_code = $(this).parent().attr("id"); //code id
						var html =sfdp.bulid_tpl(type,parent_code,$(this).attr("id"));
						$(this).removeClass("fb-fz");
						$(this).removeClass("ui-sortable");
						$(this).addClass("fb-disabled");
						$( ".fb-disabled" ).sortable( "disable" );
						$(this).html(html);
					}
			});
		},
		fb_set_return : function(data){
			$('#label'+data.tpfd_id).html(data.tpfd_name+'：');
		},
		/*5.0.1 附表设计*/
		build_fb : function(old_data=''){
			var json_data = JSON.parse(localStorage.getItem("json_data"));
			if(json_data.name==''){
				sfdp.ShowTip(' 请先执行基础配置！');return;
			}
			var code = 'Id'+sfdp.dateFormat(new Date(), "hhmmssS");
			$('#sfdp-main').append('<fieldset id="'+code+'_field" mode="field"> <legend id="'+code+'_title">子表设计</legend><div class="sfdp-rows " mode="zibiao" id="'+code+'"><div class="view-action"><b class="ico" id="del" data="'+code+'" >㊀</b></div><div class="col-6 sfdp-field-con fb-fz"  id="1"></div><div class="col-6 sfdp-field-con fb-fz" id="2"></div></div></fieldset>');
			if(old_data==''){
				sfdp.dataSave({id:code,title:'',data:{},type:2},code,'sublist');
			}
			sfdp.setSortable();
		},
		/*5.0.1 布局模式设计*/
		build_bj : function(old_data=''){
			var json_data = JSON.parse(localStorage.getItem("json_data"));
			if(json_data.name==''){
				sfdp.ShowTip(' 请先执行基础配置！');return;
			}
			var code = 'Id'+sfdp.dateFormat(new Date(), "hhmmssS");
			$('#sfdp-main').append('<div class="sfdp-rows " id="'+code+'"><div class="view-action"><b class="ico" id="del" data="'+code+'" >㊀</b></div><div class="col-6 sfdp-field-con fb-fz"  id="1"></div><div class="col-6 sfdp-field-con fb-fz" id="2"></div></div>');
			if(old_data==''){
				sfdp.dataSave({tr:code,data:{},type:2},code,'tr');
			}
			sfdp.setSortable();
		},
		/*5.0.1 拖拽进去转换信息*/
		bulid_tpl : function(type,parent_code,td_xh,old_data=0){
			if(old_data==0){
				var td_id = type+'_'+ sfdp.dateFormat(new Date(), "mmssS");
			}else{
				var td_id =old_data;
			}
			var labid = ' data-type="'+type+'" data-id="'+td_id+'" data-code="'+parent_code+'" id="label'+td_id+'"';	
			var html =sfdp.btn_to_input(labid,type);	
			var mode = $('#'+parent_code).attr('mode') || 'zhubiao';
			if(mode=='zhubiao'){
				if(old_data==0){
					sfdp.dataSave({td:td_xh,td_type:type,tpfd_id:td_id},parent_code,'tr_data');
				}
				}else{
				if(old_data==0){
					
					sfdp.dataSave({td:td_xh,td_type:type,tpfd_id:td_id},parent_code,'sublist_data');
				}
			}
			return html;
		},
		/*5.0.1 构建样式布局*/
		build_view : function(id,old_data='',showtype=''){
			if(old_data==''){
				var code = 'Tr'+sfdp.dateFormat(new Date(), "hhmmssS");
			}else{
				var code = old_data['tr'];
			}
			var lan =parseInt(12 / id);
			var html ='';
			for (var i=1;i<=id;i++){ 
				 html +='<div class="col-'+lan+' sfdp-field-con fb-fz" id='+i+'></div>';
			}
			html = '<div class="sfdp-rows " id="'+code+'"><div class="view-action"><b class="ico" id="del" data="'+code+'" >㊀</b></div>'+html+'</div>';	
			var logs ='新增1*'+id+'单元行';
			if(old_data==''){
				sfdp.dataSave({tr:code,data:{},type:id},code,'tr');
				logout(logs);
			}else{
				logout('[恢复]'+logs);
			}
			if(showtype==''){
				$('#sfdp-main').append(html);
				$( ".fb-fz" ).sortable({
						opacity: 0.5,
						revert: true,
						animation: 150,
						stop: function( event, ui ) {
							var type =$(this).children('div').children('a').attr("data");
							var parent_code = $(this).parent().attr("id");
							$(this).removeClass("fb-fz");
							$(this).removeClass("ui-sortable");
							$(this).addClass("fb-disabled");
							$( ".fb-disabled" ).sortable( "disable" );
							$(this).html(sfdp.bulid_tpl(type,parent_code,$(this).attr("id")));
						}
				});
			}else{
				return html;
			}
			
		},
		/*5.0.1 恢复表单布局中的样式并初始化*/
		recovery_input : function(old_data){
			var td_data = old_data.data;
			if(!$.isEmptyObject(old_data.data)){
				for (x in td_data){
					var type = td_data[x]['td_type'];
					var parent_code = old_data['tr'];
					
					var html =sfdp.bulid_tpl(type,parent_code,td_data[x]['td'],td_data[x]['tpfd_id']);
					$('#'+parent_code).children('div').eq(td_data[x]['td']).removeClass("fb-fz");
					$('#'+parent_code).children('div').eq(td_data[x]['td']).removeClass("ui-sortable");
					$('#'+parent_code).children('div').eq(td_data[x]['td']).addClass("fb-disabled");
					$('#'+parent_code).children('div').eq(td_data[x]['td']).html(html);
					$( ".fb-disabled" ).sortable( "disable" );//阻止排序
					if(td_data[x]['tpfd_name']!=undefined){
						sfdp.fb_set_return(td_data[x]);
					} 
				}
		}
	},
	/*5.0.1 判断存储的数据是否存在*/
	has_item : function(Item){
		var json_data = JSON.parse(localStorage.getItem("json_data"));
		if(json_data[Item]==''){
			alert('参数' + Item + '未配置！');
			return ; 
		}
		return true;
	},
	Ico : function(id=0) {
		var data = ['<svg t="1616854567257" class="icon" viewBox="0 0 1352 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2095" width="120" height="80"><path d="M1086.603604 415.596184c-13.65934-211.566186-160.799312-415.596184-430.627602-415.596184-246.810151 0-437.119373 183.448833-447.215407 428.272544-135.037018 42.022439-208.760594 186.520649-208.760594 291.699618 0 161.946123 134.586485 303.986881 287.99296 303.986881l207.982401 0c8.82635 0 15.99392-7.16757 15.99392-15.99392s-7.16757-15.99392-15.99392-15.99392l-207.982401 0c-136.368138 0-255.984641-127.091255-255.984641-271.978561 0-97.622303 85.560306-271.978561 255.984641-271.978561l48.00224 0c8.82635 0 15.99392-7.16757 15.99392-15.99392s-7.16757-15.99392-15.99392-15.99392l-48.00224 0c-16.280623 0-31.680659 1.822611-46.691599 4.443893 12.553487-191.66082 158.239466-388.420855 414.654161-388.420855 274.804632 0 399.970882 215.641462 399.970882 415.985281l0 48.00224c0 8.82635 7.16757 15.99392 15.99392 15.99392s15.99392-7.16757 15.99392-15.99392l0-48.00224c0-0.102394 0-0.184309 0-0.286703 96.598364 10.587525 223.9968 114.271544 223.9968 272.265264 0 122.422095-118.101074 271.978561-255.984641 271.978561l-239.990721 0c-103.602104 0-143.986241-40.384137-143.986241-143.986241l0-438.921505 130.490731 130.224507c3.133252 3.133252 7.229006 4.710117 11.324761 4.710117s8.191509-1.576865 11.324761-4.66916c6.246025-6.246025 6.246025-16.423975 0-22.629042l-142.737036-142.450333c-19.0043-18.942863-33.749015-18.942863-52.732836 0l-142.737036 142.450333c-6.246025 6.205068-6.246025 16.383017 0 22.629042s16.383017 6.246025 22.629042 0l130.449773-130.265464 0 438.921505c0 121.725816 54.268744 175.99456 175.99456 175.99456l239.990721 0c155.085735 0 287.99296-167.16821 287.99296-303.986881 0-165.32512-135.118933-294.628082-257.33624-304.375977z" p-id="2096" fill="#2d6dcc"></path></svg>','<svg t="1616854233639" class="icon" viewBox="0 0 1352 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1148" width="20" height="20"><path d="M1086.667547 415.408012c-13.762285-211.51321-160.867183-415.408012-430.644827-415.408012-32.931181 0-65.411812 3.194816-96.581748 9.50253-8.662867 1.761245-14.233315 10.178356-12.51303 18.882182 1.761245 8.662867 10.055479 14.233315 18.882182 12.51303 29.060539-5.918602 59.431771-8.888142 90.212596-8.888142 274.815624 0 399.98688 215.650087 399.98688 416.00192l0 48.00416c0 8.826703 7.167857 15.99456 15.99456 15.99456s15.99456-7.167857 15.99456-15.99456l0-48.00416c0-0.163837-0.040959-0.348153-0.040959-0.51199 98.01532 8.437591 224.026239 93.817004 224.026239 272.50143 0 152.531989-112.453431 271.98944-255.99488 271.98944l-767.98464 0c-127.239695 0-255.99488-93.448371-255.99488-271.98944 0-131.00794 80.116158-271.98944 255.99488-271.98944l48.00416 0c8.826703 0 15.99456-7.167857 15.99456-15.99456s-7.167857-15.99456-15.99456-15.99456l-48.00416 0 0-63.99872c0-119.252655 73.562689-193.347813 191.99616-193.347813 107.640727 0 191.99616 84.928861 191.99616 193.347813l0 415.776644-133.117338-123.51241c-6.410112-6.021-16.547509-5.672847-22.588988 0.839663-6.021 6.471551-5.672847 16.608948 0.839663 22.588988l144.50399 134.038919c9.563969 9.543489 18.02204 14.233315 26.459631 14.233315 8.355673 0 16.649907-4.648867 25.845243-13.864683l144.934061-134.468991c6.49203-6.021 6.881142-16.137917 0.839663-22.588988-6.021-6.532989-16.199356-6.881142-22.588988-0.839663l-133.117338 123.553369 0-415.776644c0-126.359073-98.404432-225.336933-224.00576-225.336933-136.066399 0-224.00576 88.451351-224.00576 225.336933l0 65.637087c-168.157917 16.260795-255.99488 160.088958-255.99488 302.360673 0 147.350653 100.923422 303.99904 288.00448 303.99904l767.98464 0c161.50205 0 288.00448-133.526929 288.00448-303.99904 0-201.723966-145.814684-296.892622-257.346533-304.572469z" p-id="1149" fill="#2d6dcc"></path></svg>','<svg t="1616854567257" class="icon" viewBox="0 0 1352 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2095" width="20" height="20"><path d="M1086.603604 415.596184c-13.65934-211.566186-160.799312-415.596184-430.627602-415.596184-246.810151 0-437.119373 183.448833-447.215407 428.272544-135.037018 42.022439-208.760594 186.520649-208.760594 291.699618 0 161.946123 134.586485 303.986881 287.99296 303.986881l207.982401 0c8.82635 0 15.99392-7.16757 15.99392-15.99392s-7.16757-15.99392-15.99392-15.99392l-207.982401 0c-136.368138 0-255.984641-127.091255-255.984641-271.978561 0-97.622303 85.560306-271.978561 255.984641-271.978561l48.00224 0c8.82635 0 15.99392-7.16757 15.99392-15.99392s-7.16757-15.99392-15.99392-15.99392l-48.00224 0c-16.280623 0-31.680659 1.822611-46.691599 4.443893 12.553487-191.66082 158.239466-388.420855 414.654161-388.420855 274.804632 0 399.970882 215.641462 399.970882 415.985281l0 48.00224c0 8.82635 7.16757 15.99392 15.99392 15.99392s15.99392-7.16757 15.99392-15.99392l0-48.00224c0-0.102394 0-0.184309 0-0.286703 96.598364 10.587525 223.9968 114.271544 223.9968 272.265264 0 122.422095-118.101074 271.978561-255.984641 271.978561l-239.990721 0c-103.602104 0-143.986241-40.384137-143.986241-143.986241l0-438.921505 130.490731 130.224507c3.133252 3.133252 7.229006 4.710117 11.324761 4.710117s8.191509-1.576865 11.324761-4.66916c6.246025-6.246025 6.246025-16.423975 0-22.629042l-142.737036-142.450333c-19.0043-18.942863-33.749015-18.942863-52.732836 0l-142.737036 142.450333c-6.246025 6.205068-6.246025 16.383017 0 22.629042s16.383017 6.246025 22.629042 0l130.449773-130.265464 0 438.921505c0 121.725816 54.268744 175.99456 175.99456 175.99456l239.990721 0c155.085735 0 287.99296-167.16821 287.99296-303.986881 0-165.32512-135.118933-294.628082-257.33624-304.375977z" p-id="2096" fill="#2d6dcc"></path></svg>'];
		return data[id];
	}
}
/*5.0.1 日志输出信息*/
function logout(info){
	if(Debug){
		console.log(sfdp.dateFormat(new Date(), "mm:ss")+'] ' + info);
	}
}
/*5.0.1 查看时选择框状态*/
function view_checkboxes_clss(data,type='checkbox',att){
			var datas = [];
			var htmls ='';  
			if(data.value != undefined){
					var strs = [];
					strs=(data.value).split(",");
				}
			for (y in data.tpfd_data){
				if(data.tpfd_check != undefined && isInArray(data.tpfd_check,y)){
					var check='checked';
				}else{
					var check='';
				}
				
				if(data.value != undefined && isInArray(strs,data.tpfd_data[y])){
					var check='checked';
				}else{
					var check='';
				}
				htmls += '<input '+att+' '+check+' name="'+data.tpfd_db+'[]" value='+y+' type="'+type+'">'+data.tpfd_data[y]+'';
			}
			
			return htmls;
}
/*5.0.1 是否在数组里面判断*/
function isInArray(arr,value){
	for(var i = 0; i < arr.length; i++){
	if(value === arr[i]){
	return true;
	}
	}
	return false;
}
/*5.0.1 监听单元格点击状态*/
$("#sfdp-main").on("click",".sfdp-rows",function(e){
	var activeDom = $(this).attr('id');
	var mode = $(this).attr('mode') || 'zhubiao';
	
	var json_data = JSON.parse(localStorage.getItem("json_data"));
	if(mode=='zibiao'){
		var zbtitle = '<div class="sfdp-form-item"><label class="sfdp-label" >子表标题</label><div class="sfdp-input-block"><input id="row-title" type="text"  autocomplete="off"  class="sfdp-input" value="'+(json_data.sublist[activeDom].title || '请设置子表单标题')+'"></div></div>';
		var rownum = json_data.sublist[activeDom].type;
	}else{
		var zbtitle = '';
		var rownum =json_data.list[activeDom].type;
	}
	$('.sfdp-rows').removeClass('sfdp-ctrl-active');
	$('#'+activeDom).addClass('sfdp-ctrl-active');
	var html = '<div  style="margin: 5px;"><div class="sfdp-title">布局配置</div><div class="sfdp-form-item"><label class="sfdp-label">唯一标识</label><div class="sfdp-input-block"><input id="row-id" type="text" name="id" autocomplete="off" value='+activeDom+' class="sfdp-input"></div></div><div class="sfdp-form-item"><label class="sfdp-label">布局栅格</label><div class="sfdp-input-block"><input id="row-num" type="number" name="id" autocomplete="off" value='+rownum+' class="sfdp-input"></div></div>'+zbtitle+'</div>';
	$('.sfdp-att').html(html);
});
/*5.0.1 监听删除点击事件*/
$("#sfdp-main").on("click","#del",function(event){
	var mode = $('#'+$(this).attr("data")).attr('mode') || 'zhubiao';
	if(mode=='zhubiao'){
		$('#'+$(this).attr("data")).remove();
		sfdp.dataSave('',$(this).attr("data"),'tr_del');
		}else{
		$('#'+$(this).attr("data")+'_field').remove();
		sfdp.dataSave('',$(this).attr("data"),'sublist_del');
	}	
	
	
	
	event.stopPropagation();
});
/*5.0.1 监听设置属性*/
$("#sfdp-main").on("click",".sfdp-label",function(event){
	var dataId = $(this).attr("data-id");
	var dataCode = $(this).attr("data-code");
	var dataType = $(this).attr("data-type");
	var html = sfdp.field_config(dataType,dataId,dataCode);
	$('.sfdp-att').html(html);
	event.stopPropagation();
});
/*5.0.1 监听栅格变化设计*/
$(document).on("input propertychange", "#row-num", function (e) {
		var p_id = $('#row-id').val();
		var num = $(this).val();
		if(num<0 || num >12){
			$(this).val(12);
			num = 12;
			layer.msg('Tip:栅格布局只能在1~12区间。');return;
		}
		var sl = num;
		var lan =parseInt(12 / num);
		var html ='';
		for (var i=1;i<=sl;i++){ 
			 html +='<div class="col-'+lan+' sfdp-field-con fb-fz" id='+i+'></div>';
		}
		var mode = $('#'+p_id).attr('mode') || 'zhubiao';
		
		$('#'+p_id+' .sfdp-field-con').remove();
		$('#'+p_id+' .view-action').after(html);
		if(mode=='zhubiao'){
			sfdp.dataSave({tr:p_id,data:{},type:sl},p_id,'tr');	
			}else{
			sfdp.dataSave({id:p_id,title:'',data:{},type:sl},p_id,'sublist');	
		}	
		sfdp.setSortable();
});
/*5.0.1 监听子表标题设置*/
$(document).on("input propertychange", "#row-title", function (e) {
		var title = $('#row-title').val();
		var p_id = $('#row-id').val();
		$('#'+p_id+'_title').html(title);
		sfdp.dataSave(title,p_id,'sublist_title');
});