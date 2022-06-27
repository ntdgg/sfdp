function install(type){
    var old = editor.getValue();
    editor.setValue(old+type);
    $('#code').val(editor.getValue());
}
//编辑页面加载前
var load_satr_fun = '\n\rfunction load_satr_fun(showtype){\r' +
    '//Eg:showtype 状态为 add  edit ；\r' +
    '' +
    '}';

/*表单提交前校验*/
var load_form_check='\n\rfunction load_form_check(curform){\r' +
    '//Eg:load_form_check 校验表单规则，此处一般不需要设置；\r' +
    '' +
    '}';

/*编辑页面加载后事件*/
var load_end_fun='\n\rfunction load_end_fun(act){\n\r' +
    '//Eg:去读某个字段的值：$(\'#dropdown_2021233\').val()；\r' +
    '//Eg:模拟一些点击事件，以及下拉获取事件：\r' +
    '//if(act==\'add\'){\n' +
    '//        $(\'#dropdown_2021233\').val([0]).trigger("change");\n' +
    '//    }' +
    '// $(\'#dropdown_2021233\').on(\'change\',function(){\n' +
    '//        var id=$(this).children(\'option:selected\').val()\n' +
    '//         sfdp.sFun(\'/gadmin/sapi?fun=get_info_id\',{id:id},set_yf);\n' +
    '//    });\n' +
    '    \r' +
    '    \r' +
    '//    /*确保修改时候正确*/\n' +
    '//    if($(\'#dropdown_2021233\').get(0).selectedIndex!==0){\n' +
    '//        var zdid=$("#dropdown_2021233").val();\n' +
    '//      \tsfdp.sFun(\'/gadmin/sapi?fun=get_info_id\',{id:id},set_yf);\n' +
    '//    }' +
    '    \r' +
    '    \r' +
    '}';

/*查看页面事件*/
var load_end_view='\n\rfunction load_end_view(data){\r' +
    '//data 为详细信息的Json对象\r'+
    '//以下为参考Demo，需要自行删除\r'+
    '//Eg:获取Id值：var id = g_getUrlParam(\'id\');\r' +
    '//Eg:插入一个Tab选项：$(".layui-col-md10 .layui-tab-title").append("<<li>结账方式</li><li>合同产品</li>");\r' +
    '//$(\'.layui-col-md10 .layui-tab-content\').append(`<div class="layui-tab-item jzfs"></div><div class="layui-tab-item cnt"></div>`);' +
    '\r' +
    '\r' +
    '//通过延时执行，插入一个Tab内容'+
    '//setTimeout(function (){\r' +
    '//  sfdp.sFun(\'/gadmin/sapi?fun=get_con_byid\',{id:id},setzj);\r' +
    '//}, 500);' +
    '\r' +
    '\r' +
    '}';
/*列表事件*/
var load_list_fun='\n\rfunction load_list_fun(){'+
    '\r' +
    '//Eg:隐藏脚本添加按钮：$(\'.addbtn\').hide();\r' +
    '//Eg:插入一个按钮:$(\'.addbtn\').after(\'<a>按钮</a>\');\r' +
    '' +
    '}';