$(function(){
    $.extend({
		tpfd_common:function(data){
			return '<div>字段标识：<input name="tpfd_id" type="text" value="'+((data.text_bs) == null ? '' : (data.text_bs)) +'">  字段标题：<input name="tpfd_name" type="text"  ></div>'+
		 '<div>占位内容：<input type="text" name="tpfd_zanwei">  设置默认：<input name="tpfd_moren" type="text" ></div>'+
		'<div>查询设置：<select name="tpfd_chaxun" style="width: 80px"><option value="yes">是</option><option value="no">否</option></select>  列表设置：<select  name="tpfd_list" style="width: 80px"> <option value="yes">是</option><option value="no">否</option></select></div>';
        },
        tpfd_text:function(data){
            return 123;
        },
		tpfd_upload:function(data){
            return 123;
        },
		tpfd_fun:function(data){
			return '<div style="font-size: 16px;font-weight: 800;">高级设置</div><div>字段标识：<input name="tpfd_id" type="text" value="'+((data.text_bs) == null ? '' : (data.text_bs)) +'">  字段标题：<input name="tpfd_name" type="text"  ></div>';
        },
		
    })
})