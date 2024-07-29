function install(type){
    var old = editor.getValue();
    editor.setValue(old+type);
    $('#code').val(editor.getValue());
}
function sys_fun(data){
    layer.open({
        type: 1,
        shade: false,
        area: ['420px', '240px'], //宽高
        title: false, //是否显示标题
        content: data,
    });

}
sys_data_int
//系统变量
var sys_data_api = `
    <div style="padding: 15px">
    //同步请求<br/>
    sfdp.sFuns('/gadmin/sapi?fun=get_sfuns_id',{id:1}).then(res => {<br/>
      console.log(res)<br/>
    })<br/>
    //异步请求<br/>
    sfdp.sFun('/gadmin/sapi?fun=get_info_id',{id:id},set_yf);' +<br/>
    
    function set_yf(res){<br/>
    <br/>
    }<br/>
     </div>
`;
//系统变量
var sys_data_int = `
    <div style="padding: 15px">
    /*父级变量*/<br/>
     parent.g_soft_uid  //登入账号id<br/>
     parent.g_soft_username //登入账号名<br/>
     parent.g_soft_realname //登入真实姓名<br/>
     parent.g_soft_rolename //登入角色名<br/>
     parent.g_soft_roleid   //登入角色id<br/>
   /*页面变量*/<br/>
    g_bill_id 单据id<br/>
     showtype 页面类型，比如add 查看 edit修改<br/>
     is_wf  是否工作流信息<br/>
     g_uid   用户id<br/>
     g_role  角色id<br/>
     g_saas  saasid信息<br/>
     g_username 用户姓名<br/>
     g_sid  设计sid<br/>
     </div>
`;

//超级回调
var load_suphelp_fun = `
/**
 * obj 超级组件的id
 * rowData 选择行的数据
 * Param 选中单据的内容
 * */
function load_suphelp_fun(obj,rowData,Param){



}`;

//子表事件
var load_sub_check = `
/**
 * act add_before子表添加前   add_after子表添加后  del_before 删除前 del_after删除后
 * id 选择行的数据
 * */
function load_sub_check(act,id){
\tif(act=='add_before'){
\t\tif($('#'+id+' tr').length >4){
\t\t\talert('只能三个');
\t\t\tthrow "Parameter is not a number!";
\t\t}
\t}
}`;


//编辑页面加载前
var load_satr_fun = `
/**
 * showtype add页面为添加  edit页面为编辑
 * */
function load_satr_fun(showtype){

\t
\t
}
`;

/*表单提交前校验*/
var load_form_check='\n\rfunction load_form_check(curform){\r' +
    '//Eg:load_form_check 校验表单规则，此处一般不需要设置；\r' +
    '' +
    '}';

/*表单提交前校验*/
var load_list_after='\n\rfunction load_list_after(data){\r' +
    '//Eg:data 为加载后的事件；\r' +
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
    '//    }\r' +
    '//   sfdp.sFuns(\'/gadmin/sapi?fun=get_sfuns_id\',{id:1}).then(res => {\r' +
    '//       console.log(res)\r' +
    '//   })\r' +
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

/*列表事件*/
var load_end_time_done='\n\rfunction load_end_time_done(id,val){'+
    '\r' +
    '//Eg:id 日期组件回调id;\r' +
    '//Eg:val 日期组件回调值;\r' +
    '' +
    '}';
