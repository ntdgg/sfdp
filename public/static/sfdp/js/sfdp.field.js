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
				
				return '<label>上传控件：</label>上传';
			}else{
				return $.tpfd_default(type,data);
			}
			
			
			
			return html;
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

})