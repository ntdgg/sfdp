/*!
 * SFDP 表单设计器--字段构建
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
		tpfd_field_return:function(type,data){
			if (typeof(data['tpfd_name']) == 'undefined') {
				return $.tpfd_default(type,data);
			}else{
				switch(type) {
					case 'text':
					var html ='<label>'+data.tpfd_name+'：</label><input type="text" name="'+data.tpfd_db+'"  placeholder="" id="'+data.tpfd_id+'">';
					break;
					case 'radio':
					var html ='<label>'+data.tpfd_name+'：</label>'+tpfd_checkboxes_clss(data,'radio');
					case 'checkboxes':
					var html ='<label>'+data.tpfd_name+'：</label>'+tpfd_checkboxes_clss(data);
					break;
					case 'dropdown':
					var html ='<label>'+data.tpfd_name+'：</label>'+$.tpfd_select(data.tpfd_data,data.tpfd_db,0);
					break;
					case 'textarea':
					var html ='<label>'+data.tpfd_name+'：</label><textarea  name="'+data.tpfd_db+'"  placeholder="" ></textarea>';
				}
			}
			return html;
        },
		tpfd_select:function(data,field,value){
			var datas = [];
			for (y in data){
				
				datas[y] = { cid:y,clab:data[y]};
			}
			var json_data =JSON.parse(JSON.stringify(datas));
				var html ='<select name="'+field+'" style="width: 80px">';
				for (z in json_data){
					html += '<option value="'+json_data[z]['cid']+'" '+((value) == 'yes' ? 'selected' : '') +'>'+json_data[z]['clab']+'</option>';
				}
				return html+'</select>';
			
        },
		tpfd_default:function(type,data){
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
		}
		
    })
	function tpfd_checkboxes_clss(data,type='checkbox'){
			var datas = [];
			for (y in data.tpfd_data){
				if(isInArray(data.tpfd_check,y)){
					var check='checked';
				}else{
					var check='';
				}
				datas[y] = { cid:y,clab:data.tpfd_data[y],checked:check};
			}
			var json_data =JSON.parse(JSON.stringify(datas));
			var html ='';
			for (z in json_data){
				html += '<input '+json_data[z]['checked']+' name="tpfd_check" value='+z+' type="'+type+'">'+json_data[z]['clab']+'';
			}
			return html;
		}
	function isInArray(arr,value){
		for(var i = 0; i < arr.length; i++){
		if(value === arr[i]){
		return true;
		}
		}
		return false;
	}
})