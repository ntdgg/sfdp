/*!
 * SFDP 表单设计器--view方法构建器
 * http://cojz8.com
 *
 * 
 * Released under the MIT license
 * http://cojz8.com
 *
 * Date: 2020年3月4日23:34:39
 */
	function showview(int_data){
		if(int_data==null){
			alert('对不起，您尚未开始设计！');
		}else{
			$('#table').html('<table id="table_view"><tr class="table_tr_view"><th  colspan="4">正在设计：<b id="fb_name_view"></b></th></tr> </table>');
			 for (x in int_data.list){
				var table = table_build(int_data.list[x]['type'],int_data.list[x]);//恢复表单布局设计
				$('.table_tr_view').after(table);
				
			 } 
			$(document).attr("title",int_data.name);//修改页面标题
		}
		
	}	
	//表单构建
	function table_build(id,old_data=''){
		var code = old_data['tr'];
		var td_data = old_data.data;
			var json = {1:'',2:'',3:'',4:''};
			for (x in td_data){
				
				var type = td_data[x]['td_type'];
				var html =$.tpfd_field_return(type,td_data[x]);
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
	}

	