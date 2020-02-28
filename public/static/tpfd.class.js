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
            return '<div onclick="editoption()">静态数据：</div><div onclick="editoption()"><input name="tpfd_check[]" value=0 type="checkbox"><input name="tpfd_data[]" type="text" value="'+((data.tr_id) == '' ? 'selected' : '选项一')+'"></div>'+
			'<div id="optiondata"><input type="checkbox"   name="tpfd_check[]" value=0 ><input name="tpfd_data[]" type="text" value="'+((data.tr_id) == '' ? 'selected' : '选项2')+'"><span onclick="addoption()">增加</span></div>'+
			'<div>动态数据：<br/><input name="tpfd_db" type="text" value="'+((data.tr_id) == '' ? 'selected' : 'func_aaaa')+'"></div>';
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
        }
		
    })
})