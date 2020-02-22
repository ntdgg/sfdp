	//Tpfd表单控件
	var fb_config_data = {fb_name:'测试表单',fb_ver:dateFormat(new Date(), "yyyy-MM-dd hh:mm:ss"),fb_json:"aaa@aaa.com"};



	// 格式化时间
	function dateFormat(oDate, fmt) {
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
	}
	//日志输出区
	function logout(info){
		var szInfo = "<div>[" + dateFormat(new Date(), "mm:ss")+'] ' + info+"</div>";
		$("#logout").html(szInfo + $("#logout").html());
	}
	//表单构建
	function addtr(id){ 
		if(!has_item('fb_name')){
			return;
		}
		var $targetTbody= $("#table_center tbody");
		var $tr = $targetTbody.children("tr[class='table_tr']:last");
		var code = 'Tr'+dateFormat(new Date(), "hhmmssS");
		switch(id) {
			case 1:
				logs ='新增1*1单元行';
				var html ='<tr class="table_tr" id='+code+'><td class="fb-fz x-1 code_td" colspan="4"><span class="code">'+code+'</span></td></tr>';
				break;
			case 2:
				logs ='新增1*2单元行';
				var html ='<tr class="table_tr" id='+code+'><td class="fb-fz x-2" colspan="2"></td><td colspan="2"class="fb-fz code_td" ><span class="code">'+code+'</span></td></tr>';
				break;
			case 3:
				logs ='新增1*3单元行';
				var html ='<tr class="table_tr" id='+code+'><td class="fb-fz x-4"></td><td class="fb-fz x-4"></td><td colspan="2"class="fb-fz  x-2 code_td" ><span class="code">'+code+'</span></td></tr>';
				break;
			case 4:
				logs ='新增1*4单元行';
				var html ='<tr class="table_tr" id='+code+'><td class="fb-fz x-4"></td><td class="fb-fz x-4"></td><td class="fb-fz x-4"></td><td class="fb-fz x-4 code_td"><span class="code">'+code+'</span></td></tr>';
				break;
			 default:
				var html ='';
		} 
		
		$tr.after(html);
		$( ".fb-fz" ).sortable({
				opacity: 0.5,
				revert: true,
				stop: function( event, ui ) {
					var type = $(this).children('a').attr("data");
					var parent_code = $(this).parent().attr("id"); //获取Tr的ID值 
					if($(this).html().indexOf("code") >= 0 ) { 
						var code = '<span class="code">'+parent_code+'</span>';
					}else{
						var code = '';
					}
					var html =fb_tpl(type,code);
					//禁止本单元格再放置其他控件
					$(this).removeClass("fb-fz");
					$(this).removeClass("ui-sortable");
					//$(this).children('a').attr("data");
					$(this).html(html);
					//console.log($(this).children('a').attr("data"));
				}
				
		});
		logout(logs);
		$(".table_tr").dblclick(function(){
			logout('删除了单元行');
			$(this).remove();
		});
	}
	//文本转换
	function fb_tpl(type,code){
		switch(type) {
			case 'text':
				var html ='<label>单行文本：</label><input type="text"  placeholder="" >';
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
				var html ='<label>时间日期：</label><input type="text"  placeholder="" >';
				break;
			case 'dropdown':
				var html ='<label>下拉选择：</label><select><option value ="volvo">Volvo</option><option value ="saab">Saab</option></select>';
				break;
			case 'email':
				var html ='<label>邮箱控件：</label><input type="text"  placeholder="" >';
				break;
			 default:
				var html ='';
		} 
		
		return html+code;
	}
	//修改配置项
	function fb_config(Item){
	  var name=prompt("请输入设计的表单姓名","测试表单");
	  if (name!=null && name!=""){
		 $('#fb_name').html(name);
		 var json_data = JSON.parse(localStorage.getItem("json_data"));
			json_data[Item]=name;
			localStorage.setItem("json_data",JSON.stringify(json_data));
			//console.log(json_data)
		}else{
			alert('请配置参数' + Item + '的信息');
		}
	}
	
	//配置项判断
	function has_item(Item){
		var json_data = JSON.parse(localStorage.getItem("json_data"));
		if(json_data[Item]==''){
			alert('参数' + Item + '未配置！');
			return ; 
		}
		return true;
	}