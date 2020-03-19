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
	ShowTip : function(tip) {
		layer.msg(tip);
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
		var html = '<label for="file-input"><img  width="120px"src="data:image/png;base64,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AAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDIwLTAyLTI2VDA5OjExOjEwKzAwOjAwmvYnqAAAACV0RVh0ZGF0ZTptb2RpZnkAMjAxOS0wNC0yNVQwMjoxMDoyMCswMDowMAG1iHYAAAAgdEVYdHNvZnR3YXJlAGh0dHBzOi8vaW1hZ2VtYWdpY2sub3JnvM8dnQAAABh0RVh0VGh1bWI6OkRvY3VtZW50OjpQYWdlcwAxp/+7LwAAABh0RVh0VGh1bWI6OkltYWdlOjpIZWlnaHQAMTkyQF1xVQAAABd0RVh0VGh1bWI6OkltYWdlOjpXaWR0aAAzMDjhP6cxAAAAGXRFWHRUaHVtYjo6TWltZXR5cGUAaW1hZ2UvcG5nP7JWTgAAABd0RVh0VGh1bWI6Ok1UaW1lADE1NTYxNTgyMjDnCLouAAAAEXRFWHRUaHVtYjo6U2l6ZQA0ODUxQoxAmPUAAABadEVYdFRodW1iOjpVUkkAZmlsZTovLy9kYXRhL3d3d3Jvb3Qvd3d3LmVhc3lpY29uLm5ldC9jZG4taW1nLmVhc3lpY29uLmNuL2ZpbGVzLzEyMy8xMjMyOTc5LnBuZ6ul+WcAAAAASUVORK5CYII="></label></span>'+
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
	}
}