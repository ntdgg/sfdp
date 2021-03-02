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
var commonfun = {
	showview : function(int_data) {
		//console.log(int_data);
		if(int_data==null){
			layer.msg('对不起，没有任何数据~');
		}else{
			$('#table').html('<table id="table_view"><tbody><tr class="table_tr"><td>'+int_data.name+'</td></tr></tbody></table>');
			 for (x in int_data.list){
				var table = commonfun.table_build(int_data.list[x]['type'],int_data.list[x],'show');//恢复表单布局设计
				var $targetTbody= $("#table_view tbody");
				var $tr = $targetTbody.children("tr[class='table_tr']:last");
				$tr.after(table);
			 } 
			$(document).attr("title",int_data.name);//修改页面标题
		}
		
	},
	showlist : function(int_data,Debug=false) {
		var td_data = int_data;
			var html ='';
			for (x in td_data){
				var type = td_data[x]['td_type'];
				 html += '   '+commonfun.view_field_return(type,td_data[x]);
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
				var btn = '<a  class="btn" type="submit">&nbsp;&nbsp;保存&nbsp;&nbsp;</a><a class="btn" type="button" onclick=commonfun.layer_close()>&nbsp;&nbsp;取消&nbsp;&nbsp;</a>';
			}
			$('#table').html('<form action="" method="post" name="form" id="form"><input type="hidden" readonly name="name_db" value="'+int_data.name_db+'"><table id="table_view"><tbody><tr class="table_tr"><th  colspan="4">'+int_data.name+'</th></tr> <tr><td style="text-align: center;" colspan=4> '+btn+' </td></tr></tbody></table></form>');
			 for (x in int_data.list){
				var table = commonfun.table_build(int_data.list[x]['type'],int_data.list[x],showtype);//恢复表单布局设计
				var $targetTbody= $("#table_view tbody");
				var $tr = $targetTbody.children("tr[class='table_tr']:last");
				$tr.after(table);
			 } 
			$(document).attr("title",int_data.name);//修改页面标题
		}
		commonfun.setDate();//初始化日期选择器
	},
	table_build : function(id,old_data='',types){
		var code = old_data['tr'];
		var td_data = old_data.data;
			var json = {1:'',2:'',3:'',4:''};
			for (x in td_data){
				var type = td_data[x]['td_type'];
				if(types=='add'){
					var html =commonfun.view_field_return(type,td_data[x]);
				}else if(types=='edit'){
					var html =commonfun.edit_field_return(type,td_data[x]);
				}else{
					var html =commonfun.show_field_return(type,td_data[x]);
				}
				json[td_data[x]['td']] = html;
			}
		switch(id) {
			case 1:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-1 code_td" colspan="4">'+json[1]+'</td></tr>';
				break;
			case 2:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-2" colspan="2">'+json[1]+'</td><td id="2" colspan="2"class="fb-fz code_td" >'+json[2]+'</td></tr>';
				break;
			case 3:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-4">'+json[1]+'</td><td id="2" class="fb-fz x-4">'+json[2]+'</td><td id="3" colspan="2"class="fb-fz  x-2 code_td" >'+json[3]+'</td></tr>';
				break;
			case 4:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-4">'+json[1]+'</td><td id="2" class="fb-fz x-4">'+json[2]+'</td><td id="3" class="fb-fz x-4">'+json[3]+'</td><td id="4" class="fb-fz x-4 code_td">'+json[4]+'</td></tr>';
				break;
			 default:
				var html ='';
		} 
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
		$('#checkboxes'+id).children('span').attr("onclick","editoption("+id+")");
		$('#checkboxes'+id).children('span').html('Del');
		var html ='<div id="checkboxes'+(id+1)+'"><input type="'+type+'" name="tpfd_check" value='+(id+1)+' ><input name="tpfd_data" type="text" value="选项'+(id+2)+'"><span onclick=addoption('+(id+1)+',"'+type+'")>Add</span></div>';
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
		return commonfun.openpage(title, url, $.extend({w: "100%", h: "100%"}, opt))
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
			commonfun.sGet(url);
		});
	},
	H5uploadhtml : function(ids){
		var html = '<label for="file-input"><img  width="120px"src="'+commonfun.Ico(0)+'"></label></span>'+
		'<input type="file" accept="*/*" name="file[]" data-attr="'+ids+'" id="file-input" multiple  style="display: none">';
		layer.open({
		  type: 1,
		  title: false,
		  area: ['125px', '85px'], //宽高
		  content: html
		});
		commonfun.H5upload(ids);
		
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
						$('#'+ret.attr_id).val(ret.msg);
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
	dataSave : function(data,key,type){
		//读取当前的JSON缓存
		//save_json(type,parent_code,'tr_data');
		var json_data = JSON.parse(localStorage.getItem("json_data"));
		switch(type) {
			case 'tr':
			json_data.list[key]=data;
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
	delTr :function(){
		$(".code2").unbind('click').click(function(){
			var tr_id = $(this).parent().parent().attr("id");
			commonfun.dataSave('',tr_id,'tr_del');
			logout('删除了单元行'+tr_id);
			$(this).parent().parent().remove();
		});
		
	},
	edit_field_return:function(type,data){
			//console.log(data);
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
				return commonfun.view_default(type,data);
			}else{
				switch(type) {
					case 'text':
					var html ='<label>'+data.tpfd_name+'：</label><input type="text" '+field_att+' value="'+data.value+'" name="'+data.tpfd_db+'"  placeholder="" id="'+data.tpfd_id+'">';
					break;
					case 'radio':
					var html ='<label>'+data.tpfd_name+'：</label>'+view_checkboxes_clss(data,'radio',field_att);
					break;
					case 'checkboxes':
					var html ='<label>'+data.tpfd_name+'：</label>'+view_checkboxes_clss(data,'checkbox',field_att);
					break;
					case 'dropdown':
					var html ='<label>'+data.tpfd_name+'：</label>'+commonfun.view_select(data.tpfd_data,data.tpfd_db,data.value,field_att,data.tpfd_id);
					break;
					case 'textarea':
					var html ='<label>'+data.tpfd_name+'：</label><textarea  name="'+data.tpfd_db+'" '+field_att+' placeholder="" >'+data.value+'</textarea>';
					break;
					case 'upload':
					var html ='<label>'+data.tpfd_name+'：</label>'+commonfun.view_upload(data,data.tpfd_db,0);
					break;
					case 'date':
					var rqtype =['yyyy','MM-dd','yyyy-MM-dd','yyyyMMdd','yyyy-MM'];
					var html ='<label>'+data.tpfd_name+'：</label><input value="'+data.value+'" '+field_att+' '+field_att+' data-type="'+rqtype[data.xx_type]+'" class="datetime" type="text" name="'+data.tpfd_db+'"  id="'+data.tpfd_id+'">';
					break;
					case 'html':
					var html ='<label>'+data.tpfd_name+'：</label>'+data.tpfd_moren;
					break;
					case 'wenzi':
					var html ='<label>'+data.tpfd_name+'：</label>'+data.tpfd_moren;
					break;
				}
			}
			return html;
        },
	view_field_return:function(type,data){
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
				return commonfun.view_default(type,data);
			}else{
				switch(type) {
					case 'text':
					var html ='<label>'+data.tpfd_name+'：</label><input type="text" '+field_att+' value="'+data.tpfd_zanwei+'" name="'+data.tpfd_db+'"  placeholder="" id="'+data.tpfd_id+'">';
					break;
					case 'radio':
					var html ='<label>'+data.tpfd_name+'：</label>'+view_checkboxes_clss(data,'radio',field_att);
					break;
					case 'checkboxes':
					var html ='<label>'+data.tpfd_name+'：</label>'+view_checkboxes_clss(data,'checkbox',field_att);
					break;
					case 'dropdown':
					var html ='<label>'+data.tpfd_name+'：</label>'+commonfun.view_select(data.tpfd_data,data.tpfd_db,0,field_att,data.tpfd_id);
					break;
					case 'textarea':
					var html ='<label>'+data.tpfd_name+'：</label><textarea  name="'+data.tpfd_db+'" '+field_att+' placeholder="" ></textarea>';
					break;
					case 'upload':
					var html ='<label>'+data.tpfd_name+'：</label>'+commonfun.view_upload(data,data.tpfd_db,0);
					break;
					case 'date':
					var rqtype =['yyyy','MM-dd','yyyy-MM-dd','yyyyMMdd','yyyy-MM'];
					var html ='<label>'+data.tpfd_name+'：</label><input '+field_att+' '+field_att+' data-type="'+rqtype[data.xx_type]+'" class="datetime" type="text" name="'+data.tpfd_db+'"  id="'+data.tpfd_id+'">';
					break;
					case 'html':
					var html ='<label>'+data.tpfd_name+'：</label>'+data.tpfd_moren;
					break;
					case 'wenzi':
					var html ='<label>'+data.tpfd_name+'：</label>'+data.tpfd_moren;
					break;
				}
			}
			return html;
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
				var html = '<input type="text" name="'+data.tpfd_db+'" readonly id="'+data.tpfd_id+'" value='+data.value+'><span id="drag" style="width:80px;margin-left:5px">'+
						'<label onclick=commonfun.H5uploadhtml("'+data.tpfd_id+'")><img src="'+commonfun.Ico(2)+'"></label></span>';
				
			}else{
				if(data.value == undefined){
					data.value ='';
				}
				if(data.tpfd_upload_type == 1){
					var html = '<input type="text" name="'+data.tpfd_db+'" readonly id="'+data.tpfd_id+'" value='+data.value+'><span id="drag" style="width:80px;margin-left:5px">'+ '<label id="label_'+data.tpfd_id+'" onclick=commonfun.openpage("多文件上传","'+data.tpfd_upload_action+'?id='+data.tpfd_id+'&value="'+data.value+',{w:"50%",h:"60%"})>附件</label></span>';
				}else{
					var html = '<input type="text" name="'+data.tpfd_db+'" readonly id="'+data.tpfd_id+'" value='+data.value+'><span id="drag" style="width:80px;margin-left:5px">'+ '<label id="label_'+data.tpfd_id+'" onclick=commonfun.openpage("文件上传","'+data.tpfd_upload_action+'?id='+data.tpfd_id+'&value="'+data.value+',{w:"18%",h:"25%"})>附件</label></span>';
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
					var html ='<label)>多选控件：</label>选项1<input type="checkbox"  placeholder="" > 选项2<input type="checkbox"  placeholder="" >';
					break;
				case 'radio':
					var html ='<label)>单选控件：</label>选项1<input type="radio"  placeholder="" > 选项2<input type="radio"  placeholder="" >';
					break;
				case 'date':
					var html ='<label)>时间日期：</label><input type="text"  placeholder=""  >';
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
	show_field_return:function(type,data){
			var html ='';
			if (typeof(data['tpfd_name']) == 'undefined') {
				return commonfun.view_default(type,data);
			}else{
				switch(type) {
					case 'text':
						html ='<label>'+data.tpfd_name+'：</label>'+data.value;
					break;
					case 'radio':
						html ='<label>'+data.tpfd_name+'：</label>'+data.value;
					break;
					case 'checkboxes':
						html ='<label>'+data.tpfd_name+'：</label>'+data.value;
					break;
					case 'dropdown':
						html ='<label>'+data.tpfd_name+'：</label>'+data.value;
					break;
					case 'textarea':
						html ='<label>'+data.tpfd_name+'：</label>'+data.value;
					break;
					case 'upload':
						if(data.tpfd_upload_api==0){
							/*自带模式*/
							var html = '<label>下载：</label><span id="drag" style="width:80px;margin-left:5px">'+
									'<label onclick=commonfun.H5uploadhtml("'+data.tpfd_id+'")><img src="'+commonfun.Ico(2)+'"></label></span>';
							
						}else{
							/*自定义开发模式*/
							if(data.tpfd_upload_type == 1){
								var html = '<label>下载：</label><span id="drag" style="width:80px;margin-left:5px">'+
							'<label id="label_'+data.tpfd_id+'" onclick=commonfun.openpage("多文件上传","'+data.tpfd_upload_action+'?act=view&id='+data.tpfd_id+'&value="'+data.value+',{w:"50%",h:"60%"})>附件</label></span>';
							}else{
								var html = '<label>下载：</label><a target="_blank" href="/'+data.value+'" download="filename"><img src="'+commonfun.Ico(1)+'"></a>';
							}
						}
					break;
					case 'date':
						html ='<label>'+data.tpfd_name+'：</label>'+data.value;
					break;
					case 'html':
						html ='<label>'+data.tpfd_name+'：</label>'+data.value;
					break;
					case 'wenzi':
						html ='<label>'+data.tpfd_name+'：</label>'+data.value;
					break;
				}
			}
			return html;
	},
	tpfd_tableui:function(code,type){
			switch(type) {
			case 1:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-1 code_td" colspan="4"><span class="code">'+code+'</span><span class="code2">x</span></td></tr>';
				break;
			case 2:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-2" colspan="2"></td><td id="2" colspan="2"class="fb-fz code_td" ><span class="code">'+code+'</span><span class="code2">x</span></td></tr>';
				break;
			case 3:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-4"></td><td id="2" class="fb-fz x-4"></td><td id="3" colspan="2"class="fb-fz  x-2 code_td" ><span class="code">'+code+'</span><span class="code2">x</span></td></tr>';
				break;
			case 4:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-4"></td><td id="2" class="fb-fz x-4"></td><td id="3" class="fb-fz x-4"></td><td id="4" class="fb-fz x-4 code_td"><span class="code">'+code+'</span><span class="code2">x</span></td></tr>';
				break;
			 default:
				var html ='';
			} 
			return html;
	},
	 tpfd_xianshi:function(data){
			return '<tr><td><div>显示类型：</td><td><textarea name="tpfd_moren">'+data.tpfd_moren +'</textarea></td></tr>';
        },
    tpfd_date:function(data){
			var default_data =[{cid:0,clab:'yyyy'},{cid:1,clab:'MM-dd'},{cid:2,clab:'yyyy-MM-dd'},{cid:3,clab:'yyyyMMdd'},{cid:4,clab:'yyyy-MM'}];
			return '<tr><td>显示类型：</td><td>'+commonfun.tpfd_select(default_data,'xx_type','2') +'</td></tr>';
        },
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
            return '<tr><td><div><input '+((data.xx_type) == '0' ? 'checked' : '') +'  name="xx_type" value=0 type="radio">静态：</div></td><td>'+commonfun.tpfd_checkboxes_clss(default_data,type)+
			'<div></td></tr><tr><td><input '+((data.xx_type) == '1' ? 'checked' : '') +' name="xx_type" value=1 type="radio">动态：</td><td><input name="checkboxes_func" type="text" value="'+((data.checkboxes_func) == '' ? '' : data.checkboxes_func)+'">*方法函数名</div></td></tr>';
        },
		tpfd_gaoji:function(data){
			var default_data =[{cid:0,clab:'是'},{cid:1,clab:'否'}];
			if(data.tpfd_read=='undefined'){
				var tpfd_read = 0;
				var tpfd_must = 0;
			}else{
				var tpfd_read = data.tpfd_read;
				var tpfd_must = data.tpfd_must;
			}
			return '<tr><td><div>高级设置</div><div></td><td>只读：'+commonfun.tpfd_select(default_data,'tpfd_read','1')+'必填：'+commonfun.tpfd_select(default_data,'tpfd_must',tpfd_must)+'</div></td></tr>'; 
		},
		tpfd_moren:function(data){
			return '<tr><td><div>占位内容：</td><td><input type="text" name="tpfd_zanwei" value="'+data.tpfd_zanwei +'">  </td></tr><tr><td>设置默认：</td><td><input name="tpfd_moren" type="text" value="'+data.tpfd_moren+'"></div></td></tr>';
        },
		tpfd_list:function(data){
			return '<tr><td>列表组件：</td><td>列表：'+commonfun.tpfd_select('','tpfd_list',data.tpfd_list)+' 查询：'+commonfun.tpfd_select('','tpfd_chaxun',data.tpfd_chaxun)+'</td></tr>';
        },
		tpfd_select:function(data,field,value){
			if(data==''){
				return '<select name="'+field+'" style="width: 80px"><option value="yes" '+((value) == 'yes' ? 'selected' : '') +'>是</option><option value="no" '+((value) == 'no' ? 'selected' : '') +'>否</option></select>';
			}else{
					var on ='';
				if(field=='tpfd_dblx'){
					var on ='onchange=select_type()';
				}
				
				var html ='<select name="'+field+'" style="width: 120px" '+on+'>';
				for (x in data){
					html += '<option value="'+data[x]['cid']+'" '+((data[x]['cid']) == value ? 'selected' : '') +'>'+data[x]['clab']+'</option>';
				}
				return html+'</select>';
			}
        },
		tpfd_checkboxes_clss:function(data,type='checkbox'){
			var html ='';
			for (x in data){
				if(x == data.length-1){
					var btn ='<span onclick=commonfun.addoption('+x+',"'+type+'")>Add</span>';
				}else{
					var btn ='<span onclick=commonfun.editoption('+x+')>Del</span>';
				}
				html += '<div id="checkboxes'+x+'"><input '+data[x]['checked']+' name="tpfd_check" value='+x+' type="'+type+'"><input name="tpfd_data" type="text" value="'+data[x]['clab']+'">'+btn+'</div>';
			}
			return html;
		},
	tpfd_common:function(data){
			var default_field = [{cid:'int',clab:'int',checked:''},{cid:'time',clab:'time',checked:''},{cid:'varchar',clab:'varchar',checked:'checked'},{cid:'datetime',clab:'datetime',checked:''},{cid:'longtext',clab:'longtext',checked:''}];
			return '<div><input name="tpfd_id" type="hidden" value="'+data.tpfd_id +'"><input name="tr_id" type="hidden" value="'+data.tr_id +'"><div><tr><td>字段标题：</td><td><input  name="tpfd_name" type="text"  value="'+data.tpfd_name +'"></td></tr><tr><td>数据表段:</td><td><input name="tpfd_db" style="width:160px" type="text" value="'+data.tpfd_db +'"> 类型:'+commonfun.tpfd_select(default_field,'tpfd_dblx','varchar')+' 长度<input style="width:80px" name="tpfd_dbcd" type="text" value="'+data.tpfd_dbcd +'"></td></tr>'+commonfun.tpfd_list(data) +'</div>';
        },
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
			
			return '<tr><td><div>上传配置</div><div></td><td>属性：'+commonfun.tpfd_select(default_data,'tpfd_upload_type',tpfd_upload_type)+''+
				   ' 类型：'+commonfun.tpfd_select(word_type,'tpfd_upload_xz',tpfd_upload_xz)+'</div></td></tr><tr><td><div>上传接口</div><div></td><td>方法：'+commonfun.tpfd_select(action_data,'tpfd_upload_api',tpfd_upload_api)+''+
				   ' API地址：<input style="width:80px" name="tpfd_upload_action" type="text" value="'+tpfd_upload_action +'"></div></td></tr>'; 
			
		},
	tpfd_return:function(type,data){
			switch(type) {
				case 'text':
					var html = '<table>'+commonfun.tpfd_common(data)+commonfun.tpfd_moren(data)+commonfun.tpfd_gaoji(data)+'</table>';
					break;
				case 'checkboxes':
					var html = '<table>'+commonfun.tpfd_common(data)+commonfun.tpfd_checkboxes(data)+commonfun.tpfd_gaoji(data)+'</table>';
					break;
				case 'radio':
					var html = '<table>'+commonfun.tpfd_common(data)+commonfun.tpfd_checkboxes(data,'radio')+commonfun.tpfd_gaoji(data)+'</table>';
					break;
				case 'date':
					var html = '<table>'+commonfun.tpfd_common(data)+commonfun.tpfd_date(data)+commonfun.tpfd_gaoji(data)+'</table>';
					break;
				case 'dropdown':
					var html = '<table>'+commonfun.tpfd_common(data)+commonfun.tpfd_checkboxes(data,'radio')+commonfun.tpfd_gaoji(data)+'</table>';
					break;
				case 'textarea':
					var html = '<table>'+commonfun.tpfd_common(data)+commonfun.tpfd_moren(data)+commonfun.tpfd_gaoji(data)+'</table>';
					break;
				case 'html':
					var html = '<table>'+commonfun.tpfd_common(data)+commonfun.tpfd_xianshi(data)+'</table>';
					break;
				case 'wenzi':
					var html = '<table>'+commonfun.tpfd_common(data)+commonfun.tpfd_xianshi(data)+'</table>';
					break;
				case 'upload':    
					data.tpfd_list = 'no';
					data.tpfd_chaxun = 'no';
					data.tpfd_show = 'no';
					var html ='<table>'+commonfun.tpfd_common(data)+commonfun.tpfd_upload(data)+commonfun.tpfd_gaoji(data)+'</table>';
					break;
				 default:
					var html ='';
			}
			return html;
        },
	tpfd_change:function(labid,type){
			switch(type) {
			case 'text':
				var html ='<label '+labid+'>文本控件：</label><input  type="text"  placeholder="请输入信息~" disabled>';
				break;
			case 'upload':    
				var html ='<label '+labid+'>上传控件：</label>上传';
				break;
			case 'checkboxes':
				var html ='<label '+labid+')>多选控件：</label>选项1<input type="checkbox"  placeholder="" disabled> 选项2<input type="checkbox"  placeholder="" disabled>';
				break;
			case 'radio':
				var html ='<label '+labid+')>单选控件：</label>选项1<input type="radio"  placeholder="" disabled> 选项2<input type="radio"  placeholder="" disabled>';
				break;
			case 'date':
				var html ='<label '+labid+')>时间日期：</label><input type="text"  placeholder="" disabled >';
				break;
			case 'dropdown':
				var html ='<label '+labid+'>下拉选择：</label><select disabled><option value ="请选择">请选择</option></select>';
				break;
			case 'textarea':
				var html ='<label '+labid+'>多行控件：</label><textarea  disabled ></textarea>';
				break;
			case 'html':
				var html ='<label '+labid+'>HTML控件：</label><b style="color: blue;">Look this is a HTML</b>';
				break;
			case 'wenzi':
				var html ='<label '+labid+'>文字控件：</label>默认现实的文本';
				break;
			 default:
				var html ='';
			}
			return html;
		},
	Ico : function(id=0) {
		var data = ['data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAABQCAYAAADRAH3kAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABmJLR0QAAAAAAAD5Q7t/AAAACXBIWXMAAOpgAADqYAGEyd52AAAAB3RJTUUH5AMRBjgqJq2wgQAAFHVJREFUeNrtnXt03VWVxz/7/O69SdraJ5QWC6S5aZKbloL4WoIKw4wK4oyOg4rzUIdxRpaIM8irPJbjqCgPFZ1xluMoIDjgLJU1jjDqGgWRUVxVKLQ0NwlNQkux9GFJXzS5ufd39vyxfzdJ7yPvm5tAvmtlNb05v9/9/c7e55x99v7ufWAOc5jDyxdS7QeoBuqbOgmcB9GoF6J/dQzdoUIQy/H01nW8FLpv9r/BGNDYuI2BNduIb68v9f4JRGuBRajMAxJADPDAAHAUOKTwoohmUfHDr+5Op1i2Yg/7d6+o9mtOCC9ZBVizdit4h4gSDo3sOLAcaAZS0b9J4ATgFUBt1CYAFMgC/cBhYB/QA3QCaaAD0T0CGVVBARcLIRfQ1d5a7dcfM15yCtCwdis+CAlyMVAhJhrLqSSBNwJ/AJwBvBJYOImvOQI8DzwJPAw8ovC02IwBAiJKV9vaanfHqHjJKMDatW0c8UIMEBXU+cWovBH4U+CPMKEHFfhqBXYBjwLfV+FBUfYDvHDc71m273i6OlLV7p6ymPUKsGrVTmqW9KJhgKggsETh7cCHgDOBedP4OP3AY8C3gR8Ae1FBvcPFcnSlZ96MMKsVIJlKIwgqHoUaUXkL8HHgzUBNFR8tB2wEvorKDzFDEgV6ZthsMCsVoLm5g9CZMe5VENEW4ErgvZgxNxb0Ay8AvwN2YkbefqAPW8sDbPZYghmOq4CTgKWMfVbpw2aCWyXIPaFhbPAP3TPEUJx1CtCQSg8+tEBc4T3AdcBY5tde4CngV9hUncYE/2I44PqDhC+6ICdKAAlRmQ8cDzQBr8GMyvXRZ6OhG7gJlW8jmomFjkw8ZPsMMBJnmQIoqdOfZCBTA7AMG/WXMvKoz2GCvh/4kUKbhu6gCwqELXAoG7A8kaMtEkxjcweBKLm8o2hYpykswLaS5wF/ApyGbSHL4SjwDRE+h2fv4bCO+S5DT2dLVXt01ihAav1mFiQG6D06D1WpB24B/gxwZS4JsVF+F3C/zwbPuXho+3VRgoWHCI8soGvrqeN4CuXNf/ggv9t1Ihp1nhOP4laoch7w18AbKK8ICvwIuBK0Q30AolW1C2aFApy0uof584+QySVw4luBr2J7+nLYBnxdlXsC3G4vHnE2iqdyb7461U7oA+IuB4DAcQoXAh9j5CXpUeBSEX0ysmHortIOYcYrQDKVJlHbz0CmFlHWKfwbcFaZ5v3A94CbnXdtXjxhEOJ8QE8Fja5ka5vNLCoWV1BZo3A58AFgfpnLNgGXoPJbL4p6x/YqLAczWgGS656CbBzM4m8Ebse2eKWwC/gccCdw1Ee7BER5ZlzT/MSxuqkT9Y4gngWhBuX9wD8C9WUu+Q1wMdAWi2fp3LKe6RbJjFaAhpZ2xAywFcDXMWOrFDYDVy6OZ392IGvLr0JFR/1ISKbSIIrYyD4LuBWzDUrhIYWLRXSHuY/XTeuzzlgFGLbdqwO+AHy0TNNHMOfP5nxQ5pmqO1uUZGsa1KGqiNAM/AvwljIX3KMqlwIHUZnWnYGb/C2mHvVNnQDMCwMwy/riMk1/AfwdymZFEGaC8AGE7vRaap0ntqseEe0EPoLtAErhfSJ6qWZqHMDKU7ZP45NWGE0t7fYlQchAGOAK9tQjPZjC64HvAieXaPIbTDnSBB5VoWcGOFYKcVJDN/VNT7NrxykgmkTlDkrbMfuAvwB+6pxn2zQtBRVRgFNSaeIqeMiv4eayhToRPR44DovOLcccKjGGyBf7gN0CR9WMuj8u8RXdwAcQfVS8Q5xn2wwMtOSRTKXJ5mLEghBx/nRUvg2UkvCDChcJ/B6mx108pQpwzjk/Z8fzKwmCEFVh0ZJeDhxYvFJUzsBG82uB1Zjg52P+dsfggMdjDpw+4BCwElOO4TgEfFSC8B4/kECcp3tGTPsjY/2rNvFiNo7kYqjoO4A7KHYjh8C1NfuX3ZpZtp/58SxbtpxW0eeaEgVobG3Dh+bVEgDnE3j3GkTfCbwVY97UTdEzf0lEN6iXrA9jPLOtqaIdNJVoaG0z3qF3Tpy/HvgUxXZYD/BOYKv3jmcqbBBOWgGSqbT9ooJXiTvnz8KMtvMYW6BkPNiI8B5VdsJktnlKMtWORlQx5zxd6dap6I5R0RD1l1jffBc4p0SzL8cDf0Xond+WruwyMOFdQENr25DwDc3O+duA+4C/YuqF3wd8Wbzs7Fm3hQXx7ARvY1s0BRQSqpJQFRpb0/ZJhdHT3opffADM1rkFi1AW4sJs6E71Gs0aFcSEFKC5uWP4f+PARYjeh0Xmlo7xNh4Tai9m9PRG//dl2v8UdQ+oKKu6mia4NkbCV0FV5jnRG5zzn1SVedOpBK53CSKKc/5BbMAUYhXw3hNr+wdnqUph3Hevb2knFoSod2DW/NXAJYxOxOgDdmDx+DbMkt8FHAXJgcYwosXxmNt3ffRzcnTtRcDPJr5FOkb4dU70BuCq6I9fVJXPiOhREZ2W5aAh1RZ5Lng98N8YM3k42oDzgZ3OKdsqtMWNjadxc3MHwfwjDBydB8aOuRVj4YzUW88C/ws8ILBJVHZ70RHnbwVENI7KCiyqtlBVfgmQy43rkQfvWCD864ErGArbfiJy234G5482tqbpSucDvpVBnYMBDwKbQvgxxmEcjibgbOA/KvYQ43nDVat2kljSC7kYAg1YSPb8ES7ZAdwLfMerpJ34EAQHeOfpLjOK16/fzIGBBHHnj8nUyf82fs79kPBRqRMT/pUUcwYHGJoJ+qZjJmhsaUfNT3IeFsVcUNDkXhH9EJCtFKF0TG+3prUNFc134iuxkOw7yjTvA74P3Na9f+ETyWWHUZTFiw+wvauRF/YtH+cjFq7J4xFIZO3D8JF/FZb9Uwp5JfjsdCwHa1ra8aYAS4AfYjSz4egG3gZ0q0pFiCNjMgI1ojaryiLg85QX/nPAFahcAjyxZtkhAhRfk2HTr8+cgPDBOn/4z1gxZO1jwr8OG/mJES5KYMvB9apSV2nDcFtHynIYRHuBn5VochJGNaMmW0tT09NT/gyjLqiro61eTMXlRC8D/rxM06eAf8ioPFSD0a62TdPeuhhFa/512MgfC1W8BrgysglurLhNIIrYUvcLzMs5PGMpAZzpwtgPBhJ9RopIpVGE7vapmQ1GfKP16zdzIBsnAaiN+m9hZMxCPI7KJSL6WDTiOGnFbh5+eCTWVqUwNO1Ha/54hD8cGeALqnJjpW2CZEs7wApEf0I04ofheeAB4NfArwR6FHLRLgycnxTvYcS3Wd3Sno/ercTW9TNLNEtjnr+N6h2h8+yoGuf9mJFfG438q5l4kogpgXc3ivMVU4I1NsvGvGUUXVSmWYjlLzwI3OO9e9Q5n8kH2yZqJI5oAzhRapwX4MOUZrTsxabLjeodAtUVfuTejab9a5mc8CG/HDh/vXpXMZvAA6FoDugYoVmAUcv+BrjPOf9N4PUx50VVSKbSNE3Aa1hWAfIuyH7vUljcvVDtc8CXFH7iVfCidFeN4z7MvWsjfwPlhX8Qc8MWYh9woMTneSW4rlJKUOsG7YBOyntCh2MJ8JfAfdkwuFphsQKB8zQe654fFWUVQEXpPqMTgfdjIdxC/BT4d4l6YnvVQrJF0/4G4Bos178QLwCfxDiEhdgM3IClhxWiBriqUkrQNuTl68G8owNjvPSVwGfF5NA4EAaoUxrWjn0mKKkAq9c9hfOO5Kbm1VjqVSH2A18U6G0VJVFhf3V5DIvqHSv8UiP/BeAa9e4ObPYqRE69uwubOV4o8fe8ElyrKrUVWg62AO/GmEGfAL6GGX+9I1wTw2T0LeBVhA5USI5xOSi9DTRuO4iei/nlC/E/qvKIirJFhR1VGv0NQ06e0UZ+L3ANKrcr1EppK04UQlG5M5qPb6E4sGVKIKrq3U0435dMtdPdPvl3idSoX+C3wG9VBedUVFkEnIoxo96NVTQphbOAryP6t6JslnBsLvOSM4DLxUC0Dtv6FRZVOAjcLaJZJ1pFo88cVN67mBO9AtjACMIPwuAOQEXKT1fR3zRqW24mqAU2iPNXhWEQG9yOTRI97a30tLfS3d5KTc0ACxcdRhXF7JL/W1ozcDVwAcaQ3l/mNq/F2Mf1GoQkU2le/erHRvzeoqdvaB00IuqBV5e45jGMkMmRw2PNxK4aeoFrnHe3e+f9WCZrBbzz6ry7k/JKUFGknzydJze+ju72VpxTAu84aAmxndFM9yGsPE0pvAn4J6Lo7IGjI2eyj6S+p2IJGYX4sYgeVhV27zyZaiKKqee8yheBm7DUsDwGha+iY7Gsj4GK+iAM7sSWleFrcD9wk3p3axCEOXHjvvW4sK1tLU93tvB0e8qIkyqhCA8QMYjLXHYR8IEwDAZJueVQpADOC7nafrDppDDLtRd4VPM5cFVGT9QpItqvKjcBN2POmwPABlW53TvvgXHV6cm3DYPQq9G4r47efQC4VVU+n3cMTZVLdkzP1d6KVxjIxBDRNMbDeLBE0wRweRCEa81YLW8QFlkKXpSgry6OVd0oxHZsq1K1tKtjIXS3D/oA+r3KzU60H9jvvLsjP+1PhDXc1ZGy7CRRH91LgRNU5TYR7Z8u4kghejpaAWXNaZvxmZoegcsRvZdimnkS+IgPg8tFNCx3v6IZIBpRi4ATS32/VmFNHBlCd7oVsVy8vlwudkuYi33Ti4aiMilF7WlvRczJ5cNc7I5cLnbTdHEFRnvnuAriPGJBuE9hgaRCXOiCcL04P0hGLUQ5G2ApRvcqxA4H2YOJsgpVtQ7pTreC5dp7cV69ypSUZ+vqSFlSi/Mqol5Vqix8Q3rLaXgit6HK/RjDuBArgQv3LDpY9mnLbRYXUMxOAdilQG1uJqYUCj0dk79LKVSamz/h50qvtUii6ABG0nk7xTP3+SccXPTPwJ7k2ja6C7iF5SSZoNibpljJVJ6bgTl4L1sENhurubJ/UqJFE1YdNU8/OwblFKAU/UaxkOQcZhC629ZxKFODmHv7hxglbzjmA68z6RWLe6QCS4XCtsrac5hxWFQ76P7YhJFxC3GGCIlSdXLLKUCGY50qYAqwBKC+dXwhxzlUFl3ptUbaFd0DlIpMnEQUMi5EOQU4hPn8C3FyjU5H7swcxguXdYjKAMYkLsRxRIO36LrijwQs2LC3RPuTM8i8mJ+xlWVetvBDVU53lfjzfLWfIhQpgKKg8iLGPyvEGkSXI0pj47Zqv/MchmFYdZQXKZ6kE5QpXlkcC7Ba+yGlWTMnAq0AWtvPHGYkSi3rUTL0GBr7ur48P+1xTJuGYx5wbt/e5ZCLcc45P6/2y84hwprUoO23kOItfIYyNLPiWEB2cKZIU3pL8Za65XtPRJRHHilXs3EO0w1RseXbLP5CHMaOuSlCkQJ056tqquzCslUKkcLy1Whav6Xa7z2HCKHziMh8SlP49iHsLxUQKLkN9LEcGIniAaLTLoYhDnxQVZYO9NcOpo7NoXpIptL5VX7QRivAM4IeciXMgJLBoKCvDjUl+DVGUjy7oMkbRPR9AXwtCzS3tNM5Cyp1RQMgi8o3FR5iyDASgR6xY+JmHaIEUzBiaKkw/kZVCUtZgSUVoKtrDU2v20h4ZEEvKndHNx7eNgFcFsLPHXSEM4AdNBZEDOKsc/77pTwZPjpncDbh7LMf5rm9CirzEH0XxTI9gA1iupfvKfITlvXoNDR0IzUZsJIt/0XpEu33isolKnoYZs45OC8n1Dd14lyICG/DOAGF5yE+DLwLOBjzjs6C0HbZwH5PT5IwnkVU9gFfobQV+R4VvUyEGCo0NFcoID+HkmhMpYkFIQ4WAZdRLHzFIoQHgSLhwyjJoUE2bmuLygOUrmYVB65R5cMZdSLiSbZUtqzZHAxrmu1sgufaU6hlZ7+1RLNOrABVSS4AjIHXlGxuBztupQX4T4rz18F4gjeo1284R86ro6a2n/YnX1XtfnpJYnVzBw7QIIeoOx8rO1tI4VfgelX/eRdI2aNyRuV2acyTSQzgVDqwxMpSWSlLgZvFyQaEhSJKZiBRlog4h4kjaUxlI4SqexNwG6XzNzYCd4mMLOIxhfXq124l8A5QB/IxrIp3qbNwcthS8dm4ytasKHvqBlicic/RyCaJ+tOfIH5kAT4xQN+OU6g7+dkLgC9hlK9C9GLLwg+CXIynR6inPOa4brK1DfEOvItrEF6LHdZYrvjCNuBfBb6Dyl4VRcOAIDFAmIvNuONTZzIaUumhA6kAnF+KysVYwasTSlwSAjd65z8jKjnn3Yjs6HEF9htb2vNhpToRvQrLmCl3KlYO23/eDfw4zNTsDGoyg0FrVcENJMB5urrWVLufZwzOPOuXrFq5l8fTzYN1EqOUsFcgeg52JN25lGd036Uqf4/oQUTpGaV0zLiZHVFBI0Qlrs5fgtkFx41wSQh0YSlMD2E58PtU5Yg4n6NqtQVmLlQFiYWO0C3Egjtvwg7MOovSdP08vofKxxHdzRjPIpxQreA8uTDm1OWUC4BPA6eP4fIMsAeLMj6LsVcO2eezzAVXMWgCE/JKLL1rDXbAxkjWXIgVmLoW0d04T3h0Htt7kqN+24SHX74WTeQnaMJKsb2XYmfEHCqLXuArAl9WOOidJ8jUjHlZndT829DcYetUEBKIJrzKBdgRbm9gctW55jA6PFY+5uaYyo/CKAG0xunwmkOjYtIL8Pr1m+0sHNF8ccalWIrSB7FS6DO+isQsg8fIOndjlcSfzxc9mUgu5JRZYMmWdtQ7JAhNGayu8GuwsibnYhXG55RhYlDMAfc4cD9wv1d51omC84jKhAtFTrEJrqyq387CJb1k+q1cjzgv6t0JWN3/0zBG0WrMe7UAWyqCCX7hSxEhQ0fo7cV2UI+h8jiQRvQIKgSiSBiQmXeU7ZNwuVd0D5Y/Q3dwq2dp1s6r1IhFsBYBtSBBpZ9l1kA0h5IBjqhKL1aMIhzsw6g/pyr0Pm2dnvdh58/3PfaL52Q/CBlKvVKrdwDeoaJzHtQ5zGEOU4z/B5QyQy1993XxAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDIwLTAyLTI2VDA5OjExOjEwKzAwOjAwmvYnqAAAACV0RVh0ZGF0ZTptb2RpZnkAMjAxOS0wNC0yNVQwMjoxMDoyMCswMDowMAG1iHYAAAAgdEVYdHNvZnR3YXJlAGh0dHBzOi8vaW1hZ2VtYWdpY2sub3JnvM8dnQAAABh0RVh0VGh1bWI6OkRvY3VtZW50OjpQYWdlcwAxp/+7LwAAABh0RVh0VGh1bWI6OkltYWdlOjpIZWlnaHQAMTkyQF1xVQAAABd0RVh0VGh1bWI6OkltYWdlOjpXaWR0aAAzMDjhP6cxAAAAGXRFWHRUaHVtYjo6TWltZXR5cGUAaW1hZ2UvcG5nP7JWTgAAABd0RVh0VGh1bWI6Ok1UaW1lADE1NTYxNTgyMjDnCLouAAAAEXRFWHRUaHVtYjo6U2l6ZQA0ODUxQoxAmPUAAABadEVYdFRodW1iOjpVUkkAZmlsZTovLy9kYXRhL3d3d3Jvb3Qvd3d3LmVhc3lpY29uLm5ldC9jZG4taW1nLmVhc3lpY29uLmNuL2ZpbGVzLzEyMy8xMjMyOTc5LnBuZ6ul+WcAAAAASUVORK5CYII=','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAPCAMAAADJev/pAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAABa1BMVEUAAAAAAP8jKdYiJ9gmJtkZHuEiKdYiJtkiKNckKtUlKtUjKNcjKtczM8wkJdojKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYkKdYjKdYjKdYjKdcjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdcjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYAAACRoT2eAAAAeHRSTlMAAAAAAAAAAAAAAAAAAAALNFVXOA4DT7LHubjGt1kGgMxrIQodY8mMDSwzeTEowHsFXLrIxdNEFznNMHzKXx8WIAS2AZ2HAUix71TZoAJ6mZcILo/EJhK/QBWGmhM9iEW+EKdwPiUcg58HkEYGZ887c7TCSh5LXUkCRTE5AAAAAWJLR0QAiAUdSAAAAAlwSFlzAADqYAAA6mABhMnedgAAAAd0SU1FB+QDEQMWFOoAZu0AAAEbSURBVBjTTU/5OwJRFH0OUraEIaIwYynMiEiWyWQrokY0SlKWGLJv/777ar4v55d73znn3nseYzU0AXD2uHr7+qlpZg0AwsCge2jYMzLqBVosspVcXt/Y+IQoSpNT0yJgmTHjD8x65lDDvKzYUOcXgotLoeUVIBwG2rAaWQPsJAjrG5tqdEuDFItJNLK9s7tHhWE/EqjtUOOJRFyFAweHST8JR8ljrtvalVQ6nVIcgH6SOT0DyxrnPIUNOTGfF3PoIJt2kelkBfmSC3Z6F4vgZ7uAq1KZadc3tJdxyefjaXjQW7nA4HLf1X+ASsVqcP8gMJiPxlNZN01Tr1Z5Mc3s88srxYXzLWS8/8dH6ZPOdtPk13fwp4Hgb5S4P2sdNjKhHYdUAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDIwLTAyLTI2VDA5OjExOjEwKzAwOjAwmvYnqAAAACV0RVh0ZGF0ZTptb2RpZnkAMjAxOS0wNC0yNVQwMjoxMDoyMCswMDowMAG1iHYAAAAgdEVYdHNvZnR3YXJlAGh0dHBzOi8vaW1hZ2VtYWdpY2sub3JnvM8dnQAAABh0RVh0VGh1bWI6OkRvY3VtZW50OjpQYWdlcwAxp/+7LwAAABh0RVh0VGh1bWI6OkltYWdlOjpIZWlnaHQAMTkyQF1xVQAAABd0RVh0VGh1bWI6OkltYWdlOjpXaWR0aAAzMDjhP6cxAAAAGXRFWHRUaHVtYjo6TWltZXR5cGUAaW1hZ2UvcG5nP7JWTgAAABd0RVh0VGh1bWI6Ok1UaW1lADE1NTYxNTgyMjDnCLouAAAAEXRFWHRUaHVtYjo6U2l6ZQA0ODUxQoxAmPUAAABadEVYdFRodW1iOjpVUkkAZmlsZTovLy9kYXRhL3d3d3Jvb3Qvd3d3LmVhc3lpY29uLm5ldC9jZG4taW1nLmVhc3lpY29uLmNuL2ZpbGVzLzEyMy8xMjMyOTc5LnBuZ6ul+WcAAAAASUVORK5CYII=','data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAPCAMAAADJev/pAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAABa1BMVEUAAAAAAP8jKdYiJ9gmJtkZHuEiKdYiJtkiKNckKtUlKtUjKNcjKtczM8wkJdojKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYkKdYjKdYjKdYjKdcjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdcjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYjKdYAAACRoT2eAAAAeHRSTlMAAAAAAAAAAAAAAAAAAAALNFVXOA4DT7LHubjGt1kGgMxrIQodY8mMDSwzeTEowHsFXLrIxdNEFznNMHzKXx8WIAS2AZ2HAUix71TZoAJ6mZcILo/EJhK/QBWGmhM9iEW+EKdwPiUcg58HkEYGZ887c7TCSh5LXUkCRTE5AAAAAWJLR0QAiAUdSAAAAAlwSFlzAADqYAAA6mABhMnedgAAAAd0SU1FB+QDEQMWFOoAZu0AAAEbSURBVBjTTU/5OwJRFH0OUraEIaIwYynMiEiWyWQrokY0SlKWGLJv/777ar4v55d73znn3nseYzU0AXD2uHr7+qlpZg0AwsCge2jYMzLqBVosspVcXt/Y+IQoSpNT0yJgmTHjD8x65lDDvKzYUOcXgotLoeUVIBwG2rAaWQPsJAjrG5tqdEuDFItJNLK9s7tHhWE/EqjtUOOJRFyFAweHST8JR8ljrtvalVQ6nVIcgH6SOT0DyxrnPIUNOTGfF3PoIJt2kelkBfmSC3Z6F4vgZ7uAq1KZadc3tJdxyefjaXjQW7nA4HLf1X+ASsVqcP8gMJiPxlNZN01Tr1Z5Mc3s88srxYXzLWS8/8dH6ZPOdtPk13fwp4Hgb5S4P2sdNjKhHYdUAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDIwLTAyLTI2VDA5OjExOjEwKzAwOjAwmvYnqAAAACV0RVh0ZGF0ZTptb2RpZnkAMjAxOS0wNC0yNVQwMjoxMDoyMCswMDowMAG1iHYAAAAgdEVYdHNvZnR3YXJlAGh0dHBzOi8vaW1hZ2VtYWdpY2sub3JnvM8dnQAAABh0RVh0VGh1bWI6OkRvY3VtZW50OjpQYWdlcwAxp/+7LwAAABh0RVh0VGh1bWI6OkltYWdlOjpIZWlnaHQAMTkyQF1xVQAAABd0RVh0VGh1bWI6OkltYWdlOjpXaWR0aAAzMDjhP6cxAAAAGXRFWHRUaHVtYjo6TWltZXR5cGUAaW1hZ2UvcG5nP7JWTgAAABd0RVh0VGh1bWI6Ok1UaW1lADE1NTYxNTgyMjDnCLouAAAAEXRFWHRUaHVtYjo6U2l6ZQA0ODUxQoxAmPUAAABadEVYdFRodW1iOjpVUkkAZmlsZTovLy9kYXRhL3d3d3Jvb3Qvd3d3LmVhc3lpY29uLm5ldC9jZG4taW1nLmVhc3lpY29uLmNuL2ZpbGVzLzEyMy8xMjMyOTc5LnBuZ6ul+WcAAAAASUVORK5CYII='];
		return data[id];
	}
}
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
	function isInArray(arr,value){
		for(var i = 0; i < arr.length; i++){
		if(value === arr[i]){
		return true;
		}
		}
		return false;
	}