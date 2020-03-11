/*!
 * SFDP 表单设计器--组件构建器
 * http://cojz8.com
 *
 * 
 * Released under the MIT license
 * http://cojz8.com
 *
 * Date: 2020年3月4日23:34:39
 */
$(function(){
    $.extend({
		tpfd_return:function(type,data){
			switch(type) {
				case 'text':
					var html = $.tpfd_common(data)+$.tpfd_moren(data);
					break;
				case 'checkboxes':
					var html = $.tpfd_common(data)+$.tpfd_checkboxes(data);
					break;
				case 'radio':
					var html = $.tpfd_common(data)+$.tpfd_checkboxes(data,'radio');
					break;
				case 'date':
					var html = $.tpfd_common(data)+$.tpfd_date(data);
					break;
				case 'dropdown':
					var html = $.tpfd_common(data)+$.tpfd_checkboxes(data,'radio');
					break;
				case 'textarea':
					var html = $.tpfd_common(data)+$.tpfd_moren(data);
					break;
				case 'html':
					var html = $.tpfd_common(data)+$.tpfd_xianshi(data);
					break;
				case 'wenzi':
					var html = $.tpfd_common(data)+$.tpfd_xianshi(data);
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
			var default_field = [{cid:'int',clab:'int',checked:''},{cid:'varchar',clab:'varchar',checked:'checked'},{cid:'datetime',clab:'datetime',checked:''},{cid:'longtext',clab:'longtext',checked:''}];
			return '<div><input name="tpfd_id" type="hidden" value="'+data.tpfd_id +'"><input name="tr_id" type="hidden" value="'+data.tr_id +'"><div>数据表段：<input style="width:40px" name="tpfd_db" type="text" value="'+data.tpfd_db +'">长度<input style="width:40px" name="tpfd_dbcd" type="text" value="'+data.tpfd_dbcd +'">类型:'+$.tpfd_select(default_field,'tpfd_dblx','varchar')+'  字段标题：<input name="tpfd_name" type="text"  value="'+data.tpfd_name +'"></div>'+$.tpfd_list(data);
        },
        tpfd_xianshi:function(data){
			
			return '<div>显示类型：<textarea name="tpfd_moren">'+data.tpfd_moren +'</textarea>';
        },
        tpfd_date:function(data){
			var default_data =[{cid:0,clab:'yyyy'},{cid:1,clab:'MM-dd'},{cid:2,clab:'yyyy-MM-dd'},{cid:3,clab:'yyyyMMdd'},{cid:4,clab:'yyyy-MM'}];
			return '<div>显示类型：'+$.tpfd_select(default_data,'xx_type','2');
        },
		tpfd_checkboxes:function(data,type='checkbox'){
			if(data.tpfd_data==undefined){
				var default_data =[{cid:0,clab:'选项1',checked:''},{cid:1,clab:'选项2',checked:'checked'}];
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
            return '<div><input '+((data.xx_type) == '0' ? 'checked' : '') +'  name="xx_type" value=0 type="radio">静态数据：</div>'+$.tpfd_checkboxes_clss(default_data,type)+
			'<div><input '+((data.xx_type) == '1' ? 'checked' : '') +' name="xx_type" value=1 type="radio">动态数据：<br/><input name="checkboxes_func" type="text" value="'+((data.checkboxes_func) == '' ? '' : data.checkboxes_func)+'"></div>';
        },
		tpfd_fun:function(data){
			return '<div style="font-size: 16px;font-weight: 800;">高级设置</div><div></div>';
        },
		tpfd_moren:function(data){
			return '<div>占位内容：<input type="text" name="tpfd_zanwei" value="'+data.tpfd_zanwei +'">  设置默认：<input name="tpfd_moren" type="text" value="'+data.tpfd_moren+'"></div>';
        },
		tpfd_list:function(data){
			return '<div>列表设置：'+$.tpfd_select('','tpfd_list','yes')+'  查询设置：'+$.tpfd_select('','tpfd_chaxun','no')+'  字段隐藏：'+$.tpfd_select('','tpfd_show','no')+'</div>';
        },
		tpfd_select:function(data,field,value){
			if(data==''){
				return '<select name="'+field+'" style="width: 80px"><option value="yes" '+((value) == 'yes' ? 'selected' : '') +'>是</option><option value="no" '+((value) == 'no' ? 'selected' : '') +'>否</option></select>';
			}else{
				var html ='<select name="'+field+'" style="width: 80px">';
				for (x in data){
					
					html += '<option value="'+data[x]['cid']+'" '+((value) == 'yes' ? 'selected' : '') +'>'+data[x]['clab']+'</option>';
				}
				return html+'</select>';
			}
        },
		tpfd_checkboxes_clss:function(data,type='checkbox'){
			var html ='';
			for (x in data){
				if(x == data.length-1){
					var btn ='<span onclick=addoption('+x+',"'+type+'")>Add</span>';
				}else{
					var btn ='<span onclick="editoption('+x+')">Del</span>';
				}
				html += '<div id="checkboxes'+x+'"><input '+data[x]['checked']+' name="tpfd_check" value='+x+' type="'+type+'"><input name="tpfd_data" type="text" value="'+data[x]['clab']+'">'+btn+'</div>';
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