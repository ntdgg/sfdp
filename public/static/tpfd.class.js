$(function(){
    $.extend({
		tpfd_return:function(type,data){
			switch(type) {
				case 'text':
					var html = $.tpfd_common(data)+$.tpfd_fun(data);
					break;
				case 'checkboxes':
					var html = $.tpfd_common(data)+$.tpfd_checkboxes(data);
					break;
				case 'radio':
					var html ='<label  onclick=showLayer("radio")>单选控件：</label>选项1<input type="radio"  placeholder="" disabled> 选项2<input type="radio"  placeholder="" disabled>';
					break;
				case 'date':
					var html ='<label  onclick=showLayer("date")>时间日期：</label><input type="text"  placeholder="" disabled >';
					break;
				case 'dropdown':
					var html ='<label  onclick=showLayer("dropdown")>下拉选择：</label><select disabled><option value ="请选择">请选择</option></select>';
					break;
				case 'email':
					var html ='<label  onclick=showLayer("email")>邮箱控件：</label><input type="text"  placeholder="" disabled >';
					break;
				case 'upload':    
					var html ='<label  onclick=showLayer("upload")>上传控件：</label>上传';
					break;
				 default:
					var html ='';
			}
			
			
			
			return html;
        },
		tpfd_common:function(data){
			return '<input name="tpfd_id" type="hidden" value="'+data.tpfd_id +'"><input name="tr_id" type="hidden" value="'+data.tr_id +'"><div>字段标识：<input name="tpfd_db" type="text" value="'+data.tpfd_db +'">  字段标题：<input name="tpfd_name" type="text"  value="'+data.tpfd_name +'"></div>';
        },
        tpfd_text:function(data){
			
            return 123;
        },
		tpfd_checkboxes:function(data){
			if(data.tpfd_data==undefined){
				var default_data =[{cid:0,clab:'选项1',checked:'checked'},{cid:1,clab:'选项2',checked:'checked'}];
			}else{
				var datas = [];
				for (x in data.tpfd_data){
					if(isInArray(data.tpfd_check,x)){
						var check='checked';
					}else{
						var check='';
					}
					datas[x] = { cid:x,clab:data.tpfd_data[x],checked:check};
				}
				var default_data =JSON.parse(JSON.stringify(datas));
			}
			//console.log(default_data);
            return '<div><input '+((data.checkboxes_type) == '0' ? 'checked' : 'checked') +'  name="checkboxes_type" value=0 type="radio">静态数据：</div>'+$.tpfd_checkboxes_clss(default_data)+
			'<div><input '+((data.checkboxes_type) == '1' ? 'checked' : '') +' name="checkboxes_type" value=1 type="radio">动态数据：<br/><input name="checkboxes_func" type="text" value="'+((data.tr_id) == '' ? 'selected' : 'func_aaaa')+'"></div>';
        },
		tpfd_fun:function(data){
			return '<div style="font-size: 16px;font-weight: 800;">高级设置</div><div></div>';
        },
		tpfd_moren:function(data){
			return '<div>占位内容：<input type="text" name="tpfd_zanwei" value="'+data.tpfd_zanwei +'">  设置默认：<input name="tpfd_moren" type="text" value="'+data.tpfd_moren +'"></div>';
        },
		tpfd_list:function(data){
			return '<div>查询设置：'+$.tpfd_select('','tpfd_chaxun','yes')+'  列表设置：'+$.tpfd_select('','tpfd_list','no')+'</div>';
        },
		tpfd_select:function(data,field,value){
			if(data==''){
				return '<select name="'+field+'" style="width: 80px"><option value="yes" '+((value) == 'yes' ? 'selected' : '') +'>是</option><option value="no" '+((value) == 'no' ? 'selected' : '') +'>否</option></select>';
			}
        },
		tpfd_checkboxes_clss:function(data){
			var html ='';
			for (x in data){
				if(x == data.length-1){
					var btn ='<span onclick="addoption('+x+')">Add</span>';
				}else{
					var btn ='<span onclick="editoption('+x+')">Del</span>';
				}
				html += '<div id="checkboxes'+x+'"><input '+data[x]['checked']+' name="tpfd_check" value='+x+' type="checkbox"><input name="tpfd_data" type="text" value="'+data[x]['clab']+'">'+btn+'</div>';
			}
			return html;
		}
		
    })
	function isInArray(arr,value){
		for(var i = 0; i < arr.length; i++){
		if(value === arr[i]){
		return true;
		}
		}
		return false;
	}
})