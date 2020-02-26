$(function(){
    $.extend({
		tpfd_common:function(data){
			return '<input name="tpfd_id" type="hidden" value="'+data.tpfd_id +'"><input name="tr_id" type="hidden" value="'+data.tr_id +'"><div>字段标识：<input name="tpfd_db" type="text" value="'+data.tpfd_db +'">  字段标题：<input name="tpfd_name" type="text"  value="'+data.tpfd_name +'"></div>'+
			 '<div>占位内容：<input type="text" name="tpfd_zanwei" value="'+data.tpfd_zanwei +'">  设置默认：<input name="tpfd_moren" type="text" value="'+data.tpfd_moren +'"></div>'+
			'<div>查询设置：'+$.tpfd_select('','tpfd_chaxun','yes')+'  列表设置：'+$.tpfd_select('','tpfd_list','no')+'</div>';
        },
        tpfd_text:function(data){
            return 123;
        },
		tpfd_upload:function(data){
            return 123;
        },
		tpfd_fun:function(data){
			return '<div style="font-size: 16px;font-weight: 800;">高级设置</div></div>';
        },
		tpfd_select:function(data,field,value){
			if(data==''){
				return '<select name="'+field+'" style="width: 80px"><option value="yes" '+((value) == 'yes' ? 'selected' : '') +'>是</option><option value="no" '+((value) == 'no' ? 'selected' : '') +'>否</option></select>';
			}
        }
		
    })
})