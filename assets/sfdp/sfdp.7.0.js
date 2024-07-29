/*!
 * SFDP 表单设计器---核心设计器
 *
 * Released under the MIT license
 * https://cojz8.com
 *
 * Date: 2022年4月01日
 * 使用声明：本版权不可删除，删除将承担侵权责任
 */
var Debug = false;//是否开启打印模式
var NameExp = /^[\u4e00-\u9fa5]{0,}$/; //中文名称正则运算
var DbNameExp = /^[a-zA-Z]([_a-zA-Z0-9]{0,1000})$/; //数据库名称校验
var DbFieldExp = /^[a-zA-Z]([_a-zA-Z0-9]{0,1000})$/; //数据库字段校验
var selectTree= [];

var sfdp = {
    sfdp_int_data: function (type=0) {
        if(type==1){
            var listcon = {
                Editor:{
                    data:{}
                }
            }
        }else{
            var listcon = {
            }
        }
        return {
            name: '',//表单名称
            name_db: '',//数据表名称
            tpfd_id: 'SFDP' + sfdp.dateFormat(new Date(), "yyyyMMddhhmmssS"),//表单ID
            tpfd_btn: {},
            tpfd_script: '',//数据表脚本
            tpfd_class: '',//数据表脚本
            list: listcon,
            sublist: {
                //子表数据
            },
            tpfd_content:'',//6.0版本新增设计器内容
            tpfd_time: sfdp.dateFormat(new Date(), "yyyy-MM-dd hh:mm:ss"),//表单设计时间
            tpfd_ver: 'v6.0'//表单设计器版本
        };
    },
    showview: function (int_data) {
        if (int_data == null) {
            layer.msg('对不起，没有任何数据~');
        } else {
            if(s_type==1){
                $('#table').html('<div id="table_view" style="margin:10px"><div id="sfdp_html2">'+int_data.tpfd_content+'</div></div>');
                var html = sfdpEditor.convert_data_view(int_data);
            }else{
                $('#table').html('<div id="table_view" style="margin:10px"></div>');
                for (x in int_data.list) {
                    if(int_data.list.hasOwnProperty(x)){
                        let table = sfdp.table_build(int_data.list[x]['type'], int_data.list[x], 'show');
                        $("#table_view").append(table);
                    }
                }
                //恢复子表布局
                if( Object.keys(int_data.sublist).length>0){
                    $("#table_view").append(`<div class="table_card"><ul class="tab" id="tab"></ul><div class="tabCon" id="tabCon"></div>`);
                }
                var xh = 1;
                //恢复子表布局
                for (x in int_data.sublist) {
                    var divclass='';
                    var activ='';
                    if(xh==1){
                        divclass='on';
                        activ='activ';
                    }
                    $("#tab").append(`<li class="${activ}">${int_data.sublist[x]['title']}</li>`);
                    let table = sfdp.sublist_build(int_data.sublist[x]['type'], int_data.sublist[x], 'show', int_data.name_db + '_d' + xh, true);
                    $("#tabCon").append(`<div class="${divclass} tabdata">${table}</div>`);
                    xh++
                }
                for (x in int_data.sublists) {
                    for (y in int_data.sublists[x]) {
                        let table = sfdp.sublist_r(int_data.sublists[x][y], true,int_data.sublists[x].length - y);

                        $("#" + int_data.name_db + "_d" + (Number(x) + 1) + " .table .title").after(table);
                    }
                }
            }
            $(document).attr("title", int_data.name);//修改页面标题
            sfdp.setTips();
        }
    },
    // 生成过滤条件组
    generateFilterGroup: function (list) {
        let htmlTags = ``;
        for (const item of list) {
            let type = item.type || 'text';
            if(type=='datetime'||type=='date'){
                htmlTags += `<div class='layui-input-inline' style="min-width:180px;max-width: 180px;">` +sfdpPlug.PlugList(type,item) + `</div>`;
                }else{
                htmlTags += `<div class='layui-input-inline' style="min-width:90px;max-width: 110px;">` +sfdpPlug.PlugList(type,item) + `</div>`;
            }
        }
        $('#search').html(htmlTags);
        sfdp.setDate();
    },
    showlist2: function (int_data) {
        var td_data = int_data;
        var html = '';
        for (x in td_data) {
            var type = td_data[x].type || 'text';
            html += sfdpPlug.PlugInit(type,td_data[x]);
        }
        $('#search').html(html);
        sfdp.setDate();
    },
    imp:function(){
        var str = $('#tabCon .on .title a').attr("onclick");
        var parts = str.split(',"');
        parts = parts[1].split('",');
        sfdp.openpage('导入数据', '/gadmin/sfdp/imp?zid='+parts[0]);
    },
    showadd: function (int_data, showtype = 'add') {
        if(s_type ==0 || s_type ==2){
            if (int_data == null) {
                layer.msg('对不起，没有任何数据~');
            }else
            {
                if(int_data.tpfd_style==undefined || int_data.tpfd_style==0){
                    var form_style = sfdp.ui.form;
                }else{
                    var form_style = '';
                }
                var s_form_submit ='';
                if(int_data.s_form_submit !=undefined || int_data.s_form_submit==1){
                    s_form_submit = 'onkeydown="if(event.keyCode===13) {return false;}"';
                }
                $('#table').html('<form '+s_form_submit+' action="" method="post" name="form" id="form" class="'+ form_style +'"><input type="hidden" readonly name="name_db" value="' + int_data.name_db + '"><input type="hidden" readonly name="@saas_id" value="' + int_data.tpfd_saas + '"><div id="table_view" style="margin:10px"><input type="hidden" readonly name="@subdata" id="subdata_subdata"></div></form>');
                //恢复主表单设计
                for (x in int_data.list) {
                    let table = sfdp.table_build(int_data.list[x]['type'], int_data.list[x], showtype);//恢复表单布局设计
                    $("#table_view").append(table);
                }
                var xh = 1;
                //恢复子表布局
                if( Object.keys(int_data.sublist).length>0){
                    $("#table_view").append(`<div class="table_card"><ul class="tab" id="tab"></ul><span class="sfdp_ksdr" style="margin-right: 50px;float: right;margin-top: -20px;font-size: 13px" ><input type="checkbox" id="kslr">↓快速录入</span><span style="float: right;margin-top: -20px;font-size: 13px" onclick="sfdp.imp()">导入引擎</span><div class="tabCon" id="tabCon"></div>`);
                    $(document).keydown(function(event){
                        if($('#kslr').is(":checked")){
                            if(event.keyCode==40){
                                $('#tabCon div').each(function (i){
                                    if($(this).css("display")=="block"){
                                        $(this).find("a").filter(".layui-btn").click();
                                    }
                                });
                            }
                        }
                    });
                }
                for (x in int_data.sublist) {
                    var divclass='',
                        activ='';
                    if(xh==1){
                        divclass='on';
                        activ='activ';
                    }
                    $("#tab").append(`<li class="${activ}">${int_data.sublist[x]['title']}</li>`);
                    let table = sfdp.sublist_build(int_data.sublist[x]['type'], int_data.sublist[x], showtype, int_data.name_db + '_d' + xh);
                    $("#tabCon").append(`<div class="${divclass}">${table}</div>`);
                    xh++
                }
                if (showtype === 'edit') {
                    for (x in int_data.sublists) {
                        for (y in int_data.sublists[x]) {
                            let htmls = sfdp.sublist_r(int_data.sublists[x][y],'',int_data.sublists[x].length - y);
                            var length= $("#" + int_data.name_db + "_d" + (Number(x) + 1) + " .table tbody").children("tr").length;
                            var str=new RegExp('dropdown_');
                            if(str.test(htmls)){
                                var reg = new RegExp("dropdown_","g");//g,表示全部替换。
                                htmls = htmls.replace(reg,'Etr'+length+"_dropdown_");
                            }
                            var str2=new RegExp('dropdowns_');
                            if(str2.test(htmls)){
                                var reg = new RegExp("dropdowns_","g");//g,表示全部替换。
                                htmls = htmls.replace(reg,'Etr'+length+"_dropdowns_");
                            }
                            var str3=new RegExp('upload_');
                            if(str3.test(htmls)){
                                var reg = new RegExp("upload_","g");//g,表示全部替换。
                                htmls = htmls.replace(reg,'EF'+length+"_upload_");
                            }
                            $("#" + int_data.name_db + "_d" + (Number(x) + 1) + " .table .title").after(htmls);
                        }
                    }
                }
                let btn = '<div class="' + sfdp.ui.rows + '" style="text-align: center;"><div class="layui-input-block"><a is_post="0"  class="'+sfdp.ui.btn_ok+' savedata" type="submit">&nbsp;&nbsp;保存&nbsp;&nbsp;</a><a class="'+sfdp.ui.btn_close+'" onclick=sfdp.layer_close()>&nbsp;&nbsp;取消&nbsp;&nbsp;</a></div></div>';
                $("#table_view").append(btn);
                $(document).attr("title", int_data.name);
            }
        }
        if(s_type ==1){
            $('#table').html('<style>.layui-input, .layui-textarea {display: inline-block;}</style><form action="" method="post" name="form" id="form" class="'+ sfdp.ui.form +'"><input type="hidden" readonly name="name_db" value="' + int_data.name_db + '"><div id="table_view" style="margin:10px"><input type="hidden" readonly name="@subdata" id="subdata_subdata"><input type="hidden" readonly name="@saas_id" value="' + int_data.tpfd_saas + '"><div id="sfdp_html2">'+int_data.tpfd_content+'</div></div></form>');
            let btn = '<div class="' + sfdp.ui.rows + '" style="text-align: center;"> <div class="layui-input-block"><a  class="'+sfdp.ui.btn_ok+' savedata" type="submit">&nbsp;&nbsp;保存&nbsp;&nbsp;</a><a class="'+sfdp.ui.btn_close+'" onclick=sfdp.layer_close()>&nbsp;&nbsp;取消&nbsp;&nbsp;</a></div></div>';
            var html = sfdpEditor.convert_data_html(int_data,showtype);
            $("#table_view").append(btn);

        }
        sfdp.Treefun(selectTree);//设置日期格式
        sfdp.setDate();//初始化日期选择器
        sfdpPlug.sliderSet();
        sfdp.setTips();
        sfdp.setRule();
    },
    setRule:function(type=''){
        // 遍历所有拥有 "data-rule" 属性的输入元素
        $('input[data-rule],select[data-rule]').each(function() {
            var $input = $(this); // 输入元素 jQuery 对象
            var formClass = $input.closest("form").attr("class");
            var mode = 'zhubiao';

            if (formClass.includes("sub_list")) {
                mode = 'zibiao'
            }
            var rule = $input.data('rule'); // 获取 "data-rule" 属性的值
            if(rule.includes('set(')){
                $input.on('change', function() {
                    sfdp.setRuleAjax($input,rule,mode);
                });
                return;
            };
            //子表求和计算
            if(rule.includes('sum(')){
                var sFuns = rule.match(/sum\((.*)\)/)[1];
                var inputname = $('#' + sFuns).attr('name')
                $('body').on('input propertychange', "input[name='"+inputname+"']", function(event) {
                    var sum = 0;
                    $('input[name="'+inputname+'"]').each(function() {
                        var value = parseFloat($(this).val());
                        if (!isNaN(value)) {
                            sum += value;
                        }
                    });
                    $input.val(sum).trigger("change");
                });
                if(type=='del'){
                    var sum = 0;
                    $('input[name="'+inputname+'"]').each(function() {
                        var value = parseFloat($(this).val());
                        if (!isNaN(value)) {
                            sum += value;
                        }
                    });
                    $input.val(sum).trigger("change");
                }

                return;
            };

            if(rule){
                var fieldsInRule = rule.split(/[+\-*/|]/);
                fieldsInRule = fieldsInRule.filter(function(el) {
                    return el.includes('_');
                });
                var rulename = $input.attr('name')
                // 为规则中的每个字段添加输入事件监听器，当字段值发生变化时，更新值
                $(fieldsInRule).each(function(index, fieldName) {
                    fieldName =  fieldName.replace(/\(/g, '').replace(/\)/g, '')
                    if(mode=='zhubiao'){
                        $('#' + fieldName).on('input', function() {
                            sfdp.setRuleValue($input,rule,fieldsInRule);
                        });
                    }else{
                        var inputname = $('#' + fieldName).attr('name')
                        //字表改监听跟传递TR
                        $('body').on('input propertychange', "input[name='"+inputname+"']", function(event) {
                            sfdp.setRuleValueSub(rulename,$(this).closest('tr'),rule,fieldsInRule);
                        });
                    }
                });
            }
        });
    },
    setRuleAjax:function($input,rule,mode){
        var sFuns = rule.match(/set\((.*)\)\|/)[1];
        // 用JavaScript的split函数以"|"分割字符串
        var items = rule.split("|");
        items = items.map(function(item) {
            return item.trim();
        });
        // 使用filter函数过滤出非空字符串
        var nonEmptyItems = items.filter(function(item) {
            return item != "";
        });
        if(nonEmptyItems[0]!=''){
            //创建一个元素请求
            nonEmptyItems.shift();

            sfdp.sFuns('/gadmin/sapi?fun='+sFuns,{id:$input.val()}).then(res => {
                nonEmptyItems.forEach(function(item) {
                    var obj ='';
                    var obj_name ='';
                    //判断是否包含转换函数
                    if (item.includes("@")) {
                        var myArr = item.split("@");
                        obj = myArr[0];
                        obj_name = myArr[1];
                    } else {
                        obj = item;
                        obj_name = $('#' + item).attr('name');
                    }
                    var element = $('#' + obj);
                    if (element.is('input')) {
                        if(mode=='zibiao'){
                            var tr =  $input.closest('tr')
                            tr.find("input[id='"+obj+"']").val(res.data[obj_name] || '');
                        }else{
                            $('#' + obj).val(res.data[obj_name] || '');
                        }
                    }else if (element.is('select')) {
                        if(mode=='zibiao'){
                            var tr =  $input.closest('tr')
                            tr.find("select[id='"+obj+"']").val(res.data[obj_name] ?? '').trigger("change");
                            //如果设置了readonly 那么清空全部只保留为选中
                            if(tr.find("select[id='"+obj+"']").attr('readonly')){
                                tr.find("select[id='"+obj+"']").find('option').each(function() {
                                    if (!$(this).is(':selected')) {
                                        $(this).attr('disabled',true);
                                    }else{
                                        $(this).attr('disabled',false);
                                    }
                                });
                            }
                        }else{
                            $('#' + obj).val(res.data[obj_name] || '').trigger("change");
                        }
                    }else if (element.is('radio')) {
                        $('input[id='+obj+'][value='+res.data[obj_name]+']').prop('checked', true);
                    }else if (element.is('checkbox')) {
                        $('input[id='+obj+'][value='+res.data[obj_name]+']').prop('checked', true);
                    }
                });

            })
        }
    },
    setRuleValue:function($input,rule,fieldsInRule){
        var fun = '';
        // 将规则中的字段名替换为实际的输入字段值
        $(fieldsInRule).each(function(index, fieldName) {
             fieldName =  fieldName.replace(/\(/g, '').replace(/\)/g, '')
            var fieldValue = $('#' + fieldName).val() || '0';
            if (fieldName.includes("FUN1") || fieldName.includes("FUN2")) {
                fun = fieldName;
            }
            rule = rule.replace(new RegExp(fieldName, 'g'), fieldValue);
        });
        rule = rule.replace('|0', '');
        var calc = math.evaluate(rule);
        if (fun!='') {
            var fun_val = fun.split("_");
            if (fun.includes("FUN1")) {
                calc = calc.toFixed(fun_val[1]);
            }
            if (fun.includes("FUN2")) {
                calc = sfdp.calc_sswr(calc,fun_val[1]);
            }
        }
        $input.val(calc).trigger("change");
    },
    /**
     * value: 数据源
     * n: 保留几位小数
     */
     calc_sswr:function(value, ws=0) {
        if(ws === 0) return parseInt(value);
        let tran = Math.round(value * Math.pow(10, ws)) / Math.pow(10, ws);
        let tranV = tran.toString();
        let newVal = tranV.indexOf('.');
        if(newVal < 0) {
            tranV += '.'
        };
        for(let i = tranV.length - tranV.indexOf('.'); i <= ws; i++) {
            tranV += '0';
        };
        return tranV
    },
    setRuleValueSub:function($input,tr,rule,fieldsInRule){
        // 将规则中的字段名替换为实际的输入字段值
        $(fieldsInRule).each(function(index, fieldName) {
             fieldName =  fieldName.replace(/\(/g, '').replace(/\)/g, '')

            var fieldValue =  tr.find("input[id='"+fieldName+"']").val() || '0';
            rule = rule.replace(new RegExp(fieldName, 'g'), fieldValue);
        });
        tr.find("input[name='"+$input+"']").removeAttr('readonly').val(math.evaluate(rule)).trigger("propertychange").prop('readonly', true);
    },
    setTips:function(){
        $(".sfdp_tips").mouseover(function(){
            var con = $(this).attr('sfdp-tip-data');
            layer.tips(con, this,{
                tips: [2, '#3d3b3b']
            });
        });
        //监听鼠标移入事件
        $(".sfdp_tips").mouseout(function(){
            layer.close(layer.tips());
        });
    },
    Treefun: function (data){
        for (x in data) {
            $("#"+x).empty();
            $("#"+x).select2ToTree({treeData: {dataArr: sfdp.arrytotree(data[x])}});
            $("#"+x).select2().val($('#'+x).attr('data-value')).trigger("change");
            $("#"+x).select2ToTree();
        }
    },
    arrytotree:function(oldArr){
        oldArr.forEach(element => {
            let parentId = element.pid;
            if(parentId !== 0){
                oldArr.forEach(ele => {
                    if(ele.id == parentId){
                        if(!ele.inc){
                            ele.inc = [];
                        }
                        ele.inc.push(element);
                    }
                });
            }
        });
        oldArr = oldArr.filter(ele => ele.pid === 0);
        return oldArr;
    },
    sublist_r: function (td_data, show_type = false ,xh='') {
        var id = td_data.id;
        var json = {1: '', 2: '', 3: '', 4: '', 5: '', 6: '', 7: '', 8: '', 9: '', 10: '', 11: '', 12: ''};
        var lend = 0;
        for (z in td_data) {
            if(z != 'id'){
                var type = td_data[z]['td_type'];
                if (show_type) {
                    json[td_data[z]['td']] = sfdpPlug.view_show(type,td_data[z],true);
                } else {
                    json[td_data[z]['td']] = sfdpPlug.PlugInit(type,td_data[z],true,'edit');
                }
                lend++;
            }
        }

        var htmls = `<tr class="text-c"><td>${xh}</td>`;
        for (var i = 1; i <= lend; i++) {
            htmls += '<td>' + json[i] + '</td>';
        }
        if (show_type) {
            return htmls + '</tr>';
        } else {
            return htmls + '<td><a style="color: chocolate;"onclick="sfdp.dl(this)">删</a><input name="id" value="'+id+'" type="hidden"></td></tr>';
        }
    },
    sublist_build: function (id, data = '', types, stable, show_type = false) {
        var code = data['id'];
        var td_data = data.data;
        var json = {1: '', 2: '', 3: '', 4: '', 5: '', 6: '', 7: '', 8: '', 9: '', 10: '', 11: '', 12: ''};
        var heads = {};
        var heads_width = {};
        var html = '';
        for (x in td_data) {
            var type = td_data[x]['td_type'];
            heads[td_data[x]['td']] = td_data[x]['tpfd_name'];
            heads_width[td_data[x]['td']] = td_data[x]['tpfd_width'];
            if (types === 'add') {
                html = '';// sfdpPlug.PlugInit(type,td_data[x],true);
            } else if (types === 'edit') {
                html = sfdpPlug.PlugInit(type,td_data[x],true,);
            } else {
                html = sfdpPlug.view_show(type,td_data[x],true);
            }
            json[td_data[x]['td']] = html;
        }
        var head_html = '';
        $.each(heads, function (i,val) {
            if(heads_width[i]==120){
                var mrwidth = 160
            }else{
                var mrwidth = heads_width[i]
            }
            head_html += `<th style="min-width: ${mrwidth}px">${val}</th>`;
        });
        if (show_type) {
            html = '<div id="' + stable + '" class="sub_list"><table class="'+sfdp.ui.table+' table"><tr class="text-c title"><th style="width: 65px">序号</th>' + head_html + '</tr></table></div>';
        } else {
            localStorage.setItem("sub"+code, JSON.stringify(td_data));
            html = '<form id="' + stable + '" class="sub_list  layui-form" data="' + stable + '">' +
                '<table class="'+sfdp.ui.table+' table" style="margin: 5px;width: 99%;" id="' + code + '_table"><tr class="text-c title"><th style="width: 50px"><a class="layui-btn layui-btn-xs" onclick=sfdp.add_sub("' + code + '","sub'+code+'",'+Object.keys(td_data).length+')>+</a></th></th>' + head_html + '<th style="width: 80px">操作</th></tr></table></fieldset></form>';
        }
        return html;
    },
    add_sub: function (id,Item,id2) {
        try{
            load_sub_check('add_before',id);//后置函数
        }catch(e){
            if(!e){
                return;
            }
            if(Debug){
                console.log(e);
            }
        }
        let td_data = JSON.parse(localStorage.getItem(Item));
        var json = {1: '', 2: '', 3: '', 4: '', 5: '', 6: '', 7: '', 8: '', 9: '', 10: '', 11: '', 12: ''};
        for (x in td_data) {
            var type = td_data[x]['td_type'];
            var html = sfdpPlug.PlugInit(type,td_data[x],true);
            json[td_data[x]['td']] = html;
        }
        var length= $('#'+id+'_table tbody').children("tr").length;
        var htmls = `<tr class="text-c"><td>${length}</td>`;

        for (var i = 1; i <= id2; i++) {
            htmls += '<td>' + json[i] + '</td>';
        }

        var str=new RegExp('dropdown_');
        if(str.test(htmls)){
            var reg = new RegExp("dropdown_","g");//g,表示全部替换。
            htmls = htmls.replace(reg,'Atr'+length+"_dropdown_");
        }
        var str2=new RegExp('dropdowns_');
        if(str2.test(htmls)){
            var reg = new RegExp("dropdowns_","g");//g,表示全部替换。
            htmls = htmls.replace(reg,'Atr'+length+"_dropdowns_");
        }
        var str3=new RegExp('upload_');
        if(str3.test(htmls)){
            var reg = new RegExp("upload_","g");//g,表示全部替换。
            htmls = htmls.replace(reg,'F'+length+"_upload_");
        }
        $('#'+id+'_table tbody').append(htmls+'<td> <a onclick="sfdp.dl(this)" style="color: chocolate;">删</a><input name="id" value="" type="hidden"></td>');
        $('.this-select').select2({width:'100%'});
        $('.tagInput').tagsInput({defaultText: '回车输入..',width:'100%'});
        sfdp.setDate();
        sfdp.setRule();
        try{
            load_sub_check('add_after',id);//后置函数
        }catch(e){
            if(Debug){
                console.log(e);
            }
        }
    },
    fz: function (obj) {
        var tr = $(obj).parent().parent();
        tr.next().find("input").val("");
        tr.find('[data-widget="select2"]').each(function(){
            $(this).select2('destroy');
        });
        tr.after(tr.clone());
        tr.find('[data-widget="select2"]').each(function(){
            $(this).select2({
                minimumResultsForSearch: -1,
                width: 'resolve'
            });
            $(this).select2();
        });
        tr.next().find('[data-widget="select2"]').each(function(){
            $(this).select2({
                minimumResultsForSearch: -1,
                width: 'resolve'
            });
            $(this).select2();
        });
        sfdp.setDate();
    },
    dl: function (obj) {
        try{
            load_sub_check('del_before',obj);//后置函数
        }catch(e){
            if(!e){
                return;
            }
            if(Debug){
                console.log(e);
            }
        }
        //var length= $(obj).parent().parent().parent().children("tr").length;
        //if(length!=2){
        $(obj).parent().parent().remove();
        //}else{
        //sfdp.ShowTip('最后一行，无法删除！');
        // }
        try{
            load_sub_check('del_after',obj);//后置函数
        }catch(e){
            if(Debug){
                console.log(e);
            }
        }
        sfdp.setRule('del');
    },
    table_build: function (id, old_data = '', types) {
        var code = old_data['tr'];
        var td_data = old_data.data;
        var html ='';
        var json = {1: '', 2: '', 3: '', 4: '', 5: '', 6: '', 7: '', 8: '', 9: '', 10: '', 11: '', 12: ''};
        for (x in td_data) {
            var type = td_data[x]['td_type'];
            if (types === 'add') {
                html = sfdpPlug.PlugInit(type,td_data[x]);
            } else if (types === 'edit') {
                html = sfdpPlug.PlugInit(type,td_data[x],false,'edit');
            } else {
                html = sfdpPlug.view_show(type, td_data[x]);
            }
            json[td_data[x]['td']] = html;
        }
        var lan = parseInt(12 / id);
        var htmls = '';
        for (var i = 1; i <= id; i++) {
            htmls += '<div class="' +sfdp.ui.rows_xs+ lan + '">' + json[i] + '</div>';
        }
        var show ='';
        if(old_data.show === '0'){
            show = `style='display:none'`;
        }
        html = `<div id="${code}" class='${sfdp.ui.rows}'  ${show}>${htmls}</div>`;
        return html;
    },
    ShowTip: function (tip) {
        layer.msg(tip);
    },
    common_return: function (data) {
        if (data.code === 0) {
            layer.msg(data.msg, {icon: 1, time: 500}, function () {
                var index = parent.layer.getFrameIndex(window.name);
                if(parent.$('#tabledata').length){
                    parent.$('#searchsubmit').trigger("click");
                    parent.layer.close(index);
                }else{
                    parent.location.reload();
                }
            });
        } else {
            layer.alert(data.msg || data.responseJSON.message, {title: "错误信息", icon: 2});
        }
    },
    insFgf: function (id) {
        $('#' + id).val($("#" + id).val() + '@@');
    },
    addoption: function (id, type = 'checkbox') {
        $('#checkboxes' + id).children('span').attr("onclick", "sfdp.editoption(" + id + ")");
        $('#checkboxes' + id).children('span').html('━');
        var html = '<div id="checkboxes' + (id + 1) + '"><input name="tpfd_data" type="text" value="选项' + (id + 2) + '"><span style="padding: 8px;font-size: 16px;font-weight: 800;" onclick=sfdp.addoption(' + (id + 1) + ',"' + type + '")>✚</span></div>';
        $('#checkboxes' + id).after(html);
    },
    editoption: function (id) {
        $('#checkboxes' + id).remove();
    },
    dateFormat: function (oDate, fmt) {
        var o = {
            "M+": oDate.getMonth() + 1, //月份
            "d+": oDate.getDate(), //日
            "h+": oDate.getHours(), //小时
            "m+": oDate.getMinutes(), //分
            "s+": oDate.getSeconds(), //秒
            "q+": Math.floor((oDate.getMonth() + 3) / 3), //季度
            "S": oDate.getMilliseconds()//毫秒
        };
        if (/(y+)/.test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (oDate.getFullYear() + "").substr(4 - RegExp.$1.length));
        }
        for (var k in o) {
            if (new RegExp("(" + k + ")").test(fmt)) {
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
            }
        }
        return fmt;
    },
    fromdata: function (froms) {
        var o = {};
        var arr = froms.serializeArray();
        $.each(arr, function () {
            if (o[this.name]) {  //返回json中有该属性
                if (!o[this.name].push) { //将已存在的属性值改成数组
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || ''); //将值存放到数组中
            } else {  //返回json中没有有该属性
                o[this.name] = this.value || '';  //直接将属性和值放入返回json中
            }

        });
        return o;
    },
    openpage: function (title, url, opt) {
        if (typeof opt === "undefined") opt = {nav: true};
        w = opt.w || "80vw";
        h = opt.h || "80vh";
        return layer.open({
            type: opt.type || 2,
            area: [w, h],
            fix: false, // 不固定
            maxmin: true,
            shade: 0.1,
            title: title,
            content: url,
            success: function (layero, index) {
                if (typeof opt.confirm !== "undefined" && opt.confirm === true) {
                    layero.find(".layui-layer-close").off("click").on("click", function () {
                        layer.alert('您确定要关闭当前窗口吗？', {
                            btn: ['确定', '取消'] //按钮
                        }, function (i) {
                            layer.close(i);
                            layer.close(index);
                        });
                    });
                }
                // 自动添加面包屑导航
                if (true === opt.nav) {
                    layer.getChildFrame('#nav-title', index).html($('#nav-title').html() + ' <span class="c-gray en">&gt;</span> ' + $('.layui-layer-title').html());
                }
                if (typeof opt.fn === "function") {
                    opt.fn(layero, index);
                }
            }
        });
    },
    openfullpage: function (title, url, opt) {
        return sfdp.openpage(title, url, $.extend({w: "98%", h: "98%"}, opt))
    },
    Askshow: function (url, msg) {
        layer.confirm(msg, function (index) {
            sfdp.sGet(url);
        });
    },
    AskshowFix: function (url, msg) {
        layer.confirm(msg, function (index) {
            $.get(url, function (data, status) {
                if (status === 'success') {
                    if (data.code === 0) {
                        layer.msg(msg, {icon: 1, time: 1500}, function () {
                            location.reload();
                        });
                    } else {
                        if(data.msg=='备份数据表已经存在，请先删除！'){
                            sfdp.Askshow(url+'&delbak=1',"<font color='red'>【警告】</font>是否删除备份表并且执行更新?")
                        }else{
                            layer.alert(data.msg, {title: "错误信息", icon: 2});
                        }

                    }
                } else {


                    layer.alert("状态: " + status, {title: "错误信息", icon: 2});
                }

            });
        });
    },
    H5uploadhtml: function (ids,img='') {
        var html = '<label for="file-input">' + sfdp.Ico(0) + '</label></span>' +
            '<input type="file" accept="*/*" name="file" data-attr="' + ids + '" id="file-input" multiple  style="display: none">';
        layer.open({
            type: 1,
            title: false,
            closeBtn: 0,
            shadeClose: true,
            area: ['125px', '85px'], //宽高
            content: html
        });
        sfdp.H5upload(img);
        $('#file-input').click();
    },
    H5upload: function (img) {
        $("#file-input").tpUpload({
            url: uploadurl,
            start: function () {
                layer_msg = layer.msg('正在上传中…', {time: 100000000});
            },
            progress: function (loaded, total, file) {
                $('.layui-layer-msg .layui-layer-content').html('已上传' + (loaded / total * 100).toFixed(2) + '%');
            },
            success: function (ret) {
                if(ret.code==1){
                    layer.alert(ret.data); return;
                }
                $('#' + ret.attr_id).val(ret.data);
                if(img==1){
                    $('#' + ret.attr_id + '_img').html('<img src="/'+ret.data+'" width="100px" onclick=sfdp.view_img("/'+(ret.data).replace(/\\/g,"/")+'") alt="">');
                    $('#' + ret.attr_id+ '_text').html('重新上传');
                }
                layer.closeAll();
            },
            error: function (ret) {
                layer.alert(ret);
            },
            end: function () {
                layer.close(layer_msg);
            }
        });
    },
    view_img:function(url){
        layer.open({
            type: 1,
            title: false,
            closeBtn: 0,
            shadeClose: true,
            area: ['80%', '80%'], //宽高
            content: "<img  src=" + url + " />"
        });
    },
    setDate: function () {
        $(".datetime").click(function () {
            var type = $(this).attr('data-type');
            var format = $(this).attr('data-format');
            var keyid = $(this).attr('id');
            if(type!='date' && type!='datetime'){
                var fs = {'yyyy':'year','yyyy-MM':'month','yyyy-MM-dd':'date','yyyyMMdd':'date','yyyy-MM-dd':'date','MM-dd':'date','yyyy-MM-dd HH:mm:ss':'datetime'}
                type=fs[format];
            }
            if($(this).attr('readonly') == 'readonly'){
                return;
            }
            $(this).removeAttr("lay-key");
            if(typeof laydate === 'undefined' && typeof layui != 'undefined'){
                var layObj = layui.laydate;
            }else{
                var layObj = laydate;
            }
            layObj.render({
                elem: this,
                format: format,
                show: true,
                type: type,
                zIndex: 99999999,
                trigger: 'click',
                done: function(value, date, endDate){
                    if($.isFunction(window.load_end_time_done)){
                        load_end_time_done(keyid,value);
                    }
                }
            });
        });
        $(".time_range").click(function () {
            if(typeof laydate === 'undefined' && typeof layui != 'undefined'){
                var layObj = layui.laydate;
            }else{
                var layObj = laydate;
            }
            var format = $(this).attr('data-format');
            $(this).removeAttr("lay-key");
            var keyid = $(this).attr('id');
            layObj.render({
                elem: this,
                show: true,
                type: format,
                range: true,
                zIndex: 99999999,
                trigger: 'click',
                done: function(value){
                    if($.isFunction(window.load_end_time_done)){
                        load_end_time_done(keyid,value);
                    }
                }
            });
        });
    },

    sGet: function (url, msg = '操作成功') {
        $.get(url, function (data, status) {
            if (status === 'success') {
                if (data.code === 0) {
                    layer.msg(msg, {icon: 1, time: 1500}, function () {
                        location.reload();
                    });
                } else {
                    layer.alert(data.msg, {title: "错误信息", icon: 2});
                }
            } else {
                layer.alert("状态: " + status, {title: "错误信息", icon: 2});
            }

        });
    },
    sAjax: function (url, data) {
        $.ajax({
            url: url,
            data: data,
            type: 'post',
            cache: true,
            dataType: 'json',
            success: function (ret) {
                if (ret.code === 0) {
                    layer.msg(ret.msg, {icon: 1, time: 1500}, function () {
                        location.reload();
                    });
                } else {
                    layer.alert(ret.msg, {title: "错误信息", icon: 2});
                }
            },
            error: function () {
                layer.alert('请求出错！', {title: "错误信息", icon: 2});
            }
        });
    },
    sFuns:function(url, data){
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: url,
                data: data,
                type: 'post',
                dataType: 'json',
                success: function(res) {
                    resolve(res);
                },
                error: function(err) {
                    reject(err);
                }
            });
        });
    },
    sFun: function (url, data, setActive) {
        $.ajax({
            url: url,
            data: data,
            type: 'post',
            dataType: 'json',
            success: function (ret) {
                if($.isFunction(setActive)){
                    setActive(ret);
                }else{
                    layer.msg('请求成功！');
                }
                return ret;
            },
            error: function () {
                layer.alert('请求出错！', {title: "错误信息", icon: 2});
            }
        });
    },
    /*5.0.1 数据存储*/
    dataSave: function (data, key, type) {
        var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        switch (type) {
            case 'con':
                json_data.tpfd_content = data;
                break;
            case 'tr':
                json_data.list[key] = data;
                break;
            case 'sublist':
                json_data.sublist[key] = data;
                break;
            case 'sublist_title':
                json_data['sublist'][key].title = data;
                break;
            case 'sublist_num':
                json_data['sublist'][key].type = data;
                break;
            case 'sublist_datasave':
                data.td = json_data['sublist'][key]['data'][data.tpfd_id]['td'];
                data.td_type = json_data['sublist'][key]['data'][data.tpfd_id]['td_type'];
                json_data['sublist'][key]['data'][data.tpfd_id] = data;
                break;
            case 'sublist_data':
                json_data['sublist'][key]['data'][data.tpfd_id] = data;
                break;
            case 'sublist_del':
                delete json_data.sublist[key];
                break;
            case 'tr_del':
                delete json_data.list[key];
                break;
            case 'sublist_td_del':
                delete json_data['sublist'][key]['data'][data.tpfd_id];
                break;
            case 'td_del':
                delete json_data['list'][key]['data'][data.tpfd_id];
                break;
            case 'tr_data':
                json_data['list'][key]['data'][data.tpfd_id] = data;
                break;
            case 'td_data':
                data.field_id = json_data['list'][key]['data'][data.tpfd_id]['field_id'];
                data.td = json_data['list'][key]['data'][data.tpfd_id]['td'];
                data.td_type = json_data['list'][key]['data'][data.tpfd_id]['td_type'];
                json_data['list'][key]['data'][data.tpfd_id] = data;
                break;
            default:
        }
        localStorage.setItem("json_data_"+design_main_id, JSON.stringify(json_data));
    },
    layer_close: function () {
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    },
    /*5.0.1 显示组件*/
    tpfd_xianshi: function (data) {
        return '<div class="sfdp-form-item"><label class="sfdp-label">显示类型：</label><div class="sfdp-input-block"><textarea name="tpfd_moren" >' + data.tpfd_moren + '</textarea> </div></div><br/><br/>';
    },
    /*5.0.1 日期组件*/
    tpfd_date: function (data) {
        var default_data = [{cid: 0, clab: 'yyyy'}, {cid: 1, clab: 'MM-dd'}, {cid: 2, clab: 'yyyy-MM-dd'}, {cid: 3,clab: 'yyyyMMdd'}, {cid: 4, clab: 'yyyy-MM'}, {cid: 5, clab: 'yyyy-MM-dd HH:mm:ss'}];
        return '<div class="sfdp-form-item"><label class="sfdp-label">日期格式</label><div class="sfdp-input-block">' + sfdp.tpfd_select(default_data, 'xx_type', data.xx_type || "2") + '</div></div>';
    },
    /*5.0.1 日期组件*/
    tpfd_date_type: function (data) {
        var default_data = [{cid: 0, clab: 'date'}, {cid: 1, clab: 'datetime'}, {cid: 2, clab: 'time'}, {cid: 3,clab: 'year'}, {cid: 4, clab: 'month'}];
        return '<div class="sfdp-form-item"><label class="sfdp-label">日期格式</label><div class="sfdp-input-block">' + sfdp.tpfd_select(default_data, 'xx_type', data.xx_type || "2") + '</div></div>';
    },
    /*5.0.1 多选组建*/
    tpfd_checkboxes: function (data, type = 'checkbox') {
        if (data.tpfd_data === undefined) {
            var default_data = [{cid: 0, clab: '选项1', checked: ''}, {cid: 1, clab: '选项2', checked: 'checked'}];
        } else {
            var datas = [];
            for (x in data.tpfd_data) {
                if (data.tpfd_check != undefined && isInArray(data.tpfd_check, x)) {
                    var check = 'checked';
                } else {
                    var check = '';
                }
                datas[x] = {cid: x, clab: data.tpfd_data[x], checked: check};
            }
            var default_data = JSON.parse(JSON.stringify(datas));
        }
        return '<div class="sfdp-form-item"><label class="sfdp-label"><input ' + ((data.xx_type) === '0' ? 'checked' : '') + '  name="xx_type" value=0 type="radio">静态方法</label><div class="sfdp-input-block">' + sfdp.tpfd_checkboxes_clss(default_data, type) + '</div></div><div class="sfdp-form-item"><label class="sfdp-label"><input ' + ((data.xx_type) === '1' ? 'checked' : '') + ' name="xx_type" value=1 type="radio">动态方法</label><div class="sfdp-input-block"><input name="checkboxes_func" placeholder="函数" type="text" value="' +(data.checkboxes_func || '') + '" class="sfdp-input" style="width:70%;display: inline;" id="checkboxes_func"><span class="button" onclick=sfdp.openpage("元数组件","'+sfdp.url.fun+'?id=checkboxes_func&value=' + (data.checkboxes_func || '') + '",{w:"50%",h:"80%"})>关联</span></div></div>';
    },
    /*5.0.1 高级组件*/
    tpfd_gaoji: function (data) {
        var default_data = [{cid: 0, clab: '是'}, {cid: 1, clab: '否'}];
        var sub =  $('#row-id').val();
        var html = '<div class="sfdp-form-item"><label class="sfdp-label">字段只读</label><div class="sfdp-input-block">' + sfdp.tpfd_select(default_data, 'tpfd_read', (data.tpfd_read || 1)) + '</div></div> <div class="sfdp-form-item"><label class="sfdp-label">字段必填</label><div class="sfdp-input-block">' + sfdp.tpfd_select(default_data, 'tpfd_must', (data.tpfd_must  || 1)) + '</div></div><div class="sfdp-form-item"><label class="sfdp-label">字段记忆</label><div class="sfdp-input-block">' + sfdp.tpfd_select(default_data, 'tpfd_auto', (data.tpfd_auto  || 1)) + '</div></div><div class="sfdp-form-item"><label class="sfdp-label">内容唯一</label><div class="sfdp-input-block">' + sfdp.tpfd_select(default_data, 'tpfd_key', (data.tpfd_key  || 1)) + '</div></div>';
        if(sub){
            return html;
        }else{
            return html + '<div class="sfdp-form-item"><label class="sfdp-label">lable隐藏</label><div class="sfdp-input-block">' + sfdp.tpfd_select(default_data, 'tpfd_lable', (data.tpfd_lable  || 1)) + '</div></div>';
        }
    },
    /*5.0.1 默认组件*/
    tpfd_moren: function (data) {
        var def = '<div class="sfdp-form-item"><label class="sfdp-label">默认内容</label><div class="sfdp-input-block" style="display: flex;"><input type="text" name="tpfd_zanwei" value="' + (data.tpfd_zanwei || '') + '" class="sfdp-input" style="width: 85%;"> <a onclick=sfdp.showInform(this,0,"tpfd_zanwei"); style="float: right;font-size: 15px;padding: 6px;color: red;">配<a/></div></div>'
        if (isInArray(['text','number'],data.td_type)) {
            def = def+' <div class="sfdp-form-item"><label class="sfdp-label">占位内容</label><div class="sfdp-input-block"><input name="tpfd_moren" type="text" value="' + (data.tpfd_moren  || '') + '" class="sfdp-input"></div></div>';
        }
        def = def+  '<div class="sfdp-form-item"><label class="sfdp-label">规则引擎</label><div class="sfdp-input-block" style="display: flex;"><input type="text" name="tpfd_rule" value="' + (data.tpfd_rule || '') + '" class="sfdp-input" style="width: 85%;"><a onclick="sfdp.set_rule()" style="font-size: 15px;padding: 6px;color: red;">配</a></div></div>'
        return def;
    },
    set_rule_val:function(val){
        var oldval = $('#roule_val').html();
        if(val=='FUN1'){
            layer.prompt({title: '请填写保留位数',value:'2'}, function(value, index, elem){
                if(value === '') return elem.focus();
                $('#roule_val').html(oldval + `<span class="editable">${val}_${value}</span>`);
                layer.close(index);
            });
            return;
        }
        if(val=='FUN2'){
            layer.prompt({title: '请填写保留位数',value:'2'}, function(value, index, elem){
                if(value === '') return elem.focus();
                $('#roule_val').html(oldval + `<span class="editable">${val}_${value}</span>`);
                layer.close(index);
            });
            return;
        }
        if(val=='FUN3'){
            layer.prompt({title: '请填写运算数',value:''}, function(value, index, elem){
                if(value === '') return elem.focus();
                $('#roule_val').html(oldval + `<span class="editable">${value}</span>`);
                layer.close(index);
            });
            return;
        }


        let re = /set\(.+\)/;
        if(re.test(oldval) && val!= '|'){
            layer.prompt({title: '请确认参数信息，如字段不一致需要@字段名',value:val}, function(value, index, elem){
                if(value === '') return elem.focus();
                $('#roule_val').html(oldval + `<span class="editable">${value}</span>`);
                layer.close(index);
            });
            }else{
            $('#roule_val').html(oldval + `<span class="editable">${val}</span>`);
        }
    },
    set_rule: function () {
        var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        var field_html = '';
        for (x in json_data.list) {
            for (x2 in json_data.list[x]['data']) {
                field_html += '<span class="button" onclick=sfdp.set_rule_val("' + json_data.list[x]['data'][x2]['tpfd_id'] + '")>' + json_data.list[x]['data'][x2]['tpfd_name'] + '</span>'
            }
        }
        for (y in json_data.sublist) {
            field_html += `<fieldset style="padding: 0px;"><legend>${y}</legend>`;
            for (y2 in json_data.sublist[y]['data']) {
                field_html += '<span class="button" onclick=sfdp.set_rule_val("' + json_data.sublist[y]['data'][y2]['tpfd_id'] + '")>' + json_data.sublist[y]['data'][y2]['tpfd_name'] + '</span>'
            }
            field_html += `</fieldset>`;
        }
        var oldval = $("input[name='tpfd_rule']").val() //旧数据
        let symbols = ["\\+", "-", "\\*", "/", "\\(", "\\)", "\\|", "set\\(.+\\)", "\\w+@\\w+"]; // add any other symbols
        let re = new RegExp("(" + symbols.join('|') + "|\\w+)", 'g');
        let result = oldval.replace(re, '<span class="editable">$1</span>');
        var html = '<style>.editable{font-size: 16px;padding: 3px;}</style><div class="layui-form">\n' +
            '  <div id="roule_val" class="sfdp-textarea" style="height: 185px;margin: 10px;word-wrap: break-word;overflow-y: auto;">'+result+'</div>\n' +
            '<div class="layui-row layui-col-space15">\n' +
            '    <div class="col-6">\n' +
            '      <div class="sfdp-card">\n' +
            '        <div class="sfdp-card-head" style="">运算公式</div>\n' +
            '        <div class="sfdp-card-body"><span class="button" onclick=sfdp.set_rule_val("+")>加法</span><span class="button" onclick=sfdp.set_rule_val("-")>减法</span><span class="button" onclick=sfdp.set_rule_val("*")>乘法</span><span class="button" onclick=sfdp.set_rule_val("/")>除法</span><br/><span class="button" onclick=sfdp.set_rule_val("(")>左括号</span><span class="button" onclick=sfdp.set_rule_val("sum")>子表求和</span><span class="button" onclick=sfdp.set_rule_val(")")>右括号</span> </div>\n' +
            '        <div class="sfdp-card-head" style="">函数方法</div>\n' +
            '        <div class="sfdp-card-body"><span class="button" onclick=sfdp.set_rule_val("FUN1")>保留位数</span><span class="button" onclick=sfdp.set_rule_val("FUN2")>四舍五入</span><span class="button" onclick=sfdp.set_rule_val("FUN3")>数值</span> </div>\n' +
            '        <div class="sfdp-card-head" style="">数据方法</div>\n' +
            '        <div class="sfdp-card-body"><span class="button" onclick=sfdp.openpage("元数组件","' + sfdp.url.fun + '?id=roule_val&value=",{w:"50%",h:"80%"})>元素赋值</span> <span class="button" onclick=sfdp.set_rule_val("|")>元素分割符</span></div>\n' +
            '        <div class="sfdp-card-body">Tip:元素作用于本字段，用于本字段传值或者赋值给其他字段；</div>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '    <div class="col-6">\n' +
            '      <div class="sfdp-card">\n' +
            '        <div class="sfdp-card-head">字段数据</div>\n' +
            '        <div class="sfdp-card-body" style="height: 320px;overflow-y: auto;">' + field_html  + '</div>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div></div>';

        layer.open({
            type: 1,
            title: '规则设置 Rule Set',
            area: ['620px', '665px'], //宽高
            btn: ['保存', '取消'],
            // 按钮1 的回调
            btn1: function (index, layero, that) {
                var data = $('#roule_val').html().replace(/<span class="editable">|<\/span>/g, '');
                $("input[name='tpfd_rule']").val(data)
                $('#save_fields').click();
                layer.close(index);
            },
            content: html
        });
        sfdp.roule_val_del();
    },
    roule_val_del: function () {
        $("#roule_val").on("click", ".editable", function (e) {
           $(this).remove();
        });
    },
    /*5.0.1 列表组件*/
    tpfd_list: function (data) {
        return '<div class="sfdp-form-item" style="display: none"><label class="sfdp-label">列表组件</label><div class="sfdp-input-block">' + sfdp.tpfd_select('', 'tpfd_list', data.tpfd_list) + ' </div></div><div class="sfdp-form-item" style="display: none"><label class="sfdp-label" >查询组件</label><div class="sfdp-input-block">' + sfdp.tpfd_select('', 'tpfd_chaxun', data.tpfd_chaxun) + '</div></div>';
    },
    /*5.0.1 选择组件*/
    tpfd_select: function (data, field, value) {
        if (data == '') {
            return '<select name="' + field + '"><option value="yes" ' + ((value) == 'yes' ? 'selected' : '') + '>是</option><option value="no" ' + ((value) == 'no' ? 'selected' : '') + '>否</option></select>';
        } else {
            if (field == 'tpfd_dblx') {
                var on = 'onchange=sfdp.select_type(this)';
            }
            var html = '<select name="' + field + '"  ' + (on || '') + '>';
            for (x in data) {
                html += '<option value="' + data[x]['cid'] + '" ' + ((data[x]['cid']) == value ? 'selected' : '') + '>' + data[x]['clab'] + '</option>';
            }
            return html + '</select>';
        }
    },
    /*5.0.1 通用多选框*/
    tpfd_checkboxes_clss: function (data, type = 'checkbox') {
        var html = '';
        for (x in data) {
            if (x == data.length - 1) {
                var btn = '<span style="padding: 8px;font-size: 16px;font-weight: 800;" onclick=sfdp.addoption(' + x + ',"' + type + '")>✚</span>';
            } else {
                var btn = '<span style="padding: 8px;font-size: 16px;font-weight: 800;" onclick=sfdp.editoption(' + x + ')>━</span>';
            }
            html += '<div id="checkboxes' + x + '"><input name="tpfd_data" type="text" value="' + data[x]['clab'] + '">' + btn + '</div>';
        }
        return html;
    },
    /*5.0.1 通用设置组件*/
    tpfd_common: function (data) {
        var default_field = [{cid: 'int', clab: 'int', checked: ''}, {cid: 'time', clab: 'time', checked: ''}, {cid: 'varchar', clab: 'varchar', checked: 'checked'}, {cid: 'decimal', clab: 'decimal', checked: 'checked'}, {cid: 'datetime', clab: 'datetime',checked: ''}, {cid: 'date', clab: 'date',checked: ''}, {cid: 'longtext', clab: 'longtext', checked: ''}];
        return '<input name="tpfd_id" type="hidden" value="' + data.tpfd_id + '"><input name="tr_id" type="hidden" value="' + data.tr_id + '"><div  style="margin: 5px;"><div class="sfdp-title">字段设置</div></div><div class="sfdp-form-item wybs"><label class="sfdp-label">唯一标识</label><div class="sfdp-input-block" style="display: flex;"><input type="text"  autocomplete="off" value="' + data.tpfd_id + '" readonly class="sfdp-input" style="width: 85%"><a onclick=sfdp.sfdp_user_filed("'+data.tpfd_id+'"); style="float: right;font-size: 15px;padding: 6px;color:red;">S</a></div></div><div class="sfdp-form-item"><label class="sfdp-label">字段标题</label><div class="sfdp-input-block"> <input id="tpfd_name"  name="tpfd_name" type="text"  value="' + data.tpfd_name + '"  class="sfdp-input"> </div></div><div class="sfdp-form-item"><label class="sfdp-label">字段名称</label><div class="sfdp-input-block"> <input id="tpfd_db" name="tpfd_db" type="text" value="' + data.tpfd_db + '"  class="sfdp-input"></div></div> <div class="sfdp-form-item"><label class="sfdp-label">字段类型</label><div class="sfdp-input-block">' + sfdp.tpfd_select(default_field, 'tpfd_dblx', (data.tpfd_dblx || 'varchar')) + ' </div></div> <div class="sfdp-form-item"><label class="sfdp-label">字段长度</label><div class="sfdp-input-block"><input style="width:80px" name="tpfd_dbcd" type="text" value="' + (data.tpfd_dbcd || '255')  + '" class="sfdp-input"></div></div><div class="sfdp-form-item"><label class="sfdp-label ">组件宽度</label><div class="sfdp-input-block"><input style="width:80px" name="tpfd_width" type="text" value="' + (data.tpfd_width || '120')  + '" class="sfdp-input"></div></div>' + sfdp.tpfd_list(data);
    },
    /*5.0.1 上传控制组件*/
    tpfd_upload: function (data) {
        var default_data = [{cid: 0, clab: '单文件上传'}, {cid: 1, clab: '多文件上传'}];
        var action_data = [{cid: 0, clab: '内置接口'}, {cid: 1, clab: '自定义API'}];
        return '<div class="sfdp-form-item"><label class="sfdp-label">上传配置</label>' + sfdp.tpfd_select(default_data, 'tpfd_upload_type', (data.tpfd_upload_type||0)) + '</div><div class="sfdp-form-item"><label class="sfdp-label">上传接口</label>' + sfdp.tpfd_select(action_data, 'tpfd_upload_api', (data.tpfd_upload_api||0)) + '' +
            ' API:<input style="width:80px;display: inline;" name="tpfd_upload_action" type="text" value="' + (data.tpfd_upload_action||'') + '"  class="sfdp-input"></div>';
    },
    sfdp_user_filed:function(field_id){
        //询问是否执行
        layer.confirm('是否将当前字段保存为用户自定义字段？', {
            btn: ['确定', '取消'] //按钮
        }, function(){
            var tr_id = $('input[name="tr_id"]').val();
            var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
            var field_json =  json_data.list[tr_id].data[field_id];
            if(field_json.tpfd_name==''||field_json.tpfd_name==undefined){
                layer.msg("字段名称不能为空！");
                return false;
            }
            $.ajax({
                url:user_field_url,
                data:{field_name:field_json.tpfd_db,field_title:field_json.tpfd_name,field_json:JSON.stringify(field_json)},
                type:'post',
                cache:true,
                dataType:'json',
                success:function(data) {
                    if(data.code==0){
                        sfdp.ShowTip('success');
                    }else{
                        sfdp.ShowTip(data.msg);
                    }
                }
            });
        }, function(){
            layer.msg('已取消');
        });
    },
    /*5.0.2 上传控制组件*/
    tpfd_upload_img: function (data) {
        return '<div class="sfdp-form-item"><label class="sfdp-label">上传API</label> <div class="sfdp-input-block"><input  name="tpfd_upload_action" placeholder="不填写则为默认接口" type="text" value="' + (data.tpfd_upload_action || '') + '"  class="sfdp-input"></div></div>';
    },
    /*5.0.1 高级组件*/
    tpfd_suphelp: function (data) {
        if (data.tpfd_suphelp == undefined) {
            var tpfd_read = '-1';
        } else {
            var tpfd_read = data.tpfd_suphelp;
        }
        return '<div class="sfdp-form-item"><label class="sfdp-label">超级关联</label><div class="sfdp-input-block">' + sfdp.tpfd_select(server_yw_data, 'tpfd_suphelp', tpfd_read) + '</div></div> </div><div class="sfdp-form-item"><label class="sfdp-label">关联value</label> <div class="sfdp-input-block"><input  name="tpfd_suphelp_value" placeholder="不填写则为默认接口" type="text" value="' + (data.tpfd_suphelp_value || 'id') + '"  class="sfdp-input"></div></div><div class="sfdp-form-item"><label class="sfdp-label">关联label</label> <div class="sfdp-input-block"><input  name="tpfd_suphelp_label" placeholder="不填写则为默认接口" type="text" value="' + (data.tpfd_suphelp_label || 'name') + '"  class="sfdp-input"></div></div><div class="sfdp-form-item"><label class="sfdp-label">过滤条件</label> <div class="sfdp-input-block"><input  name="tpfd_suphelp_where" placeholder="使用WhereRaw过滤条件" type="text" value="' + (data.tpfd_suphelp_where || '') + '"  class="sfdp-input"></div></div>';
    },
    /*5.0.1 返回基础数据*/
    tpfd_return: function (type, data) {
        var html ='';
        if (isInArray(['text','number','money','process','textarea','billno','scan','sign','tag'],type)) {
        }
        if (isInArray(['radio','dropdown','dropdowns','cascade'],type)) {
            html = sfdp.tpfd_checkboxes(data, 'radio');
        }
        if (isInArray(['suphelp','suphelps'],type)) {
            html = sfdp.tpfd_suphelp(data)
        }
        if (isInArray(['html','wenzi'],type)) {
            html =  sfdp.tpfd_xianshi(data);
        }
        if (isInArray(['upload','upload_img'],type)) {
            data.tpfd_list = 'no';data.tpfd_chaxun = 'no';data.tpfd_show = 'no';
            html =  sfdp.tpfd_upload(data)
        }
        switch (type) {
            case 'checkboxes':
                html = `${sfdp.tpfd_checkboxes(data)}`;
                break;
            case 'date':
                html = sfdp.tpfd_date(data);
                break;
            case 'time_range':
                html = sfdp.tpfd_date_type(data)
                break;
            case 'links':
                data.tpfd_zanwei = '_blank'
                data.tpfd_moren = 'www.gadmin8.com';
                break;
        }
        if (!isInArray(['html','wenzi','links'],type)) {
            html =  html +sfdp.tpfd_gaoji(data);
        }
        html = html + `<div class="sfdp-form-item"><label class="sfdp-label ">帮助信息</label><div class="sfdp-input-block"><input name="tpfd_help" type="text" value="` + (data.tpfd_help || '')  + `" class="sfdp-input"></div></div>`;
        if (isInArray(['text','number','money'],type)) {
            html = html + `<div class="sfdp-form-item"><label class="sfdp-label ">验证规则</label><div class="sfdp-input-block" style="display: flex;"><input  name="tpfd_yztype" type="text" value="` + (data.tpfd_yztype || '')  + `" class="sfdp-input" style="width: 85%;"> <a onclick="sfdp.showInform(this,1,'tpfd_yztype');" style="float: right;font-size: 15px;padding: 6px;color: red;">配<a/></div></div>`;
            html = html + `<div class="sfdp-form-item"><label class="sfdp-label ">验证提示</label><div class="sfdp-input-block"><input  name="tpfd_yztip" type="text" value="` + (data.tpfd_yztip || '')  + `" class="sfdp-input"></div></div>`;
            html = html + `<div class="sfdp-form-item"><label class="sfdp-label ">后缀内容</label><div class="sfdp-input-block"><input  name="tpfd_prefix" type="text" value="` + (data.tpfd_prefix || '')  + `" class="sfdp-input"></div></div>`;
        }
        html = html + sfdp.tpfd_moren(data);
        return `<form id="fieldform" class="${data.tpfd_id}">${sfdp.tpfd_common(data)+html}</div><div class="sfdp-form-item"><div style="margin-left: 5%;" class="button" onclick=sfdp.save_field("${data.tpfd_id}") id="save_fields">保存</div></div></form>`;
    },
    /*5.0.1 拖拽进去设计容器的时候改变为响应的控件样式*/
    btn_to_input: function (labid, type ,mode) {
        var html ='';
        var field = {'text':'文本控件','edit':'编辑器','number':'数字控件','money':'金额控件','upload':'上传控件','upload_img':'单图上传',
            'checkboxes':'多选控件','radio':'单选控件','date':'时间日期','time_range':'时间范围','dropdown':'下拉选择','dropdowns':'下拉多选',
            'cascade':'级联组件','process':'进度组件','system_user':'系统用户','system_role':'系统角色','textarea':'多行控件','html':'HTML控件',
            'wenzi':'文字控件','links':'按钮控件','suphelp':'穿透帮助','suphelps':'穿透多选','billno':'编号规则','scan':'扫码组件','sign':'手写签名','tag':'tag输入信息'
        };
        var lable = `<label ${labid} class="sfdp-label">${field[type]}</label>`;
        switch (type) {
            case 'text':
                html = '<div class="sfdp-input-block"><input  type="text"  placeholder="请输入信息~" disabled class="sfdp-input"></div>';
                break;
            case 'billno':
                html = '<div class="sfdp-input-block"><input  type="text"  placeholder="系统自动编号规则" disabled class="sfdp-input"></div>';
                break;
            case 'scan':
                html = '<div class="sfdp-input-block"><input  type="text"  placeholder="调用摄像头进行扫码" disabled class="sfdp-input"></div>';
                break;
            case 'sign':
                html = '<div class="sfdp-input-block"><input  type="text"  placeholder="调用手写板签名" disabled class="sfdp-input"></div>';
                break;
            case 'tag':
                html = '<div class="sfdp-input-block"><input  type="text"  placeholder="tag输入信息" disabled class="sfdp-input"></div>';
                break;
            case 'edit':
                html = '<div class="sfdp-input-block"><textarea  disabled class="sfdp-input"></textarea></div> ';
                break;
            case 'number':
                html = '<div class="sfdp-input-block"><input  type="number"  placeholder="" disabled class="sfdp-input"></div>';
                break;
            case 'money':
                html = '<div class="sfdp-input-block"><input  type="money"  placeholder="" disabled class="sfdp-input"></div>';
                break;
            case 'upload':
                html = '<div class="sfdp-input-block">文件上传</div>';
                break;
            case 'upload_img':
                html = '<div class="sfdp-input-block">图片上传 </div>';
                break;
            case 'suphelp':
                html = '<div class="sfdp-input-block"><select disabled class="sfdp-input"><option value ="请选择">请选择</option></select></div> ';
                break;
            case 'suphelps':
                html = '<div class="sfdp-input-block"><select disabled class="sfdp-input"><option value ="请选择">请选择</option></select></div> ';
                break;
            case 'checkboxes':
                html = '<div class="sfdp-input-block">选项1<input type="checkbox"  placeholder="" disabled> 选项2<input type="checkbox"  placeholder="" disabled></div> ';
                break;
            case 'radio':
                html = '选项1<input type="radio"  placeholder="" disabled> 选项2<input type="radio"  placeholder="" disabled> ';
                break;
            case 'date':
                html = '<div class="sfdp-input-block"><input type="text"  placeholder="" disabled class="sfdp-input"></div> ';
                break;
            case 'time_range':
                html = '<div class="sfdp-input-block"><input type="text"  placeholder="" disabled class="sfdp-input"></div>';
                break;
            case 'dropdown':
                html = '<div class="sfdp-input-block"><select disabled class="sfdp-input"><option value ="请选择">请选择</option></select></div> ';
                break;
            case 'dropdowns':
                html = '<div class="sfdp-input-block"><select disabled class="sfdp-input"><option value ="请选择">请选择</option></select></div> ';
                break;
            case 'cascade':
                html = '<div class="sfdp-input-block"><select disabled class="sfdp-input"><option value ="请选择">请选择</option></select></div> ';
                break;
            case 'process':
                html = '<div class="sfdp-input-block"><div id="progressBar"><div id="progressBar_Track"></div></div></div>';
                break;
            case 'system_user':
                html = '<div class="sfdp-input-block"><select disabled class="sfdp-input"><option value ="请选择">请选择</option></select></div>';
                break;
            case 'system_role':
                html = '<div class="sfdp-input-block"><select disabled class="sfdp-input"><option value ="请选择">请选择</option></select></div> ';
                break;
            case 'textarea':
                html = '<div class="sfdp-input-block"><textarea  disabled class="sfdp-input"></textarea></div> ';
                break;
            case 'html':
                html = '<b style="color: blue;">Look this is a HTML</b>';
                break;
            case 'wenzi':
                html = '默认显示的文本';
                break;
            case 'links':
                html = '<a class="button">按钮<a/>*默认内容即为链接地址 ';
                break;
            case 'group':
                lable = '';
                html = '<fieldset ' + labid + '  class="layui-elem-field layui-field-title sfdp-label" style="text-align: left;border-width: 1px 0 0;float: left;width: 95%;"><legend>分组标题 </legend></fieldset>';
                break;

        }
        if(mode=='zhubiao'){
            return `<div class="move_field" >${lable+html}<b title="点击可删除组件" class="ico red" id="del_con">㊀</b></div>`;
        }else{
            return html;
        }
    },
    /*5.0.1 数据库设计自动带数据*/
    sys_config: function () {
        var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        var xEdit = '', xDel = '', xStatus = '', xWorkFlow = '', XImport = '', XDelAll = '', XCopy = '';
        if (json_data.tpfd_btn === 'undefined') {
            json_data.tpfd_btn = {};
        }
        var btnArray = json_data.tpfd_btn;
        if (isInArray(btnArray, 'Edit')) {
            xEdit = 'checked';
        }
        if (isInArray(btnArray, 'Del')) {
            xDel = 'checked';
        }
        if (isInArray(btnArray, 'Status')) {
            xStatus = 'checked';
        }
        if (isInArray(btnArray, 'WorkFlow')) {
            xWorkFlow = 'checked';
        }
        if (isInArray(btnArray, 'Import')) {
            XImport = 'checked';
        }
        if (isInArray(btnArray, 'DelAll')) {
            XDelAll = 'checked';
        }
        if (isInArray(btnArray, 'Copy')) {
            XCopy = 'checked';
        }

        if (json_data.tpfd_del=='0') {
            var s_del = 'checked';
        }else{
            var s_del2 = 'checked';
        }
        if (json_data.tpfd_style=='0') {
            var tpfd_style = 'checked';
        }else{
            var tpfd_style2 = 'checked';
        }
        if (json_data.tpfd_saas=='0') {
            var s_saas = 'checked';
        }else{
            var s_saas2 = 'checked';
        }
        if (json_data.s_form_submit=='0') {
            var s_form_submit = 'checked';
        }else{
            var s_form_submit2 = 'checked';
        }
        if (json_data.s_form_print=='0') {
            var s_form_print = 'checked';
        }else{
            var s_form_print2 = 'checked';
        }


        if (json_data.s_sys_check=='0' || json_data.s_sys_check==undefined ) {
            var s_sys_check = 'checked';
        }else{
            var s_sys_check2 = 'checked';
        }
        if (json_data.s_sys_edit=='0' || json_data.s_sys_edit==undefined ) {
            var s_sys_edit = 'checked';
        }else if(json_data.s_sys_edit=='1'){
            var s_sys_edit2 = 'checked';
        }else{
            var s_sys_edit3 = 'checked';
        }
        if (isInArray(btnArray, 'Import')) {
            XImport = 'checked';
        }
        var html = '<form id="configform"> <div class="sfdp-form-item"><label class="sfdp-label">表单标题</label><div class="sfdp-input-block"><input name="name" type="text" value="' + json_data.name + '" class="sfdp-input"></div></div>' +
            '<div class="sfdp-form-item"><label class="sfdp-label">数据表名</label><div class="sfdp-input-block"><input name="name_db" type="text" value="' + (json_data.name_db) + '"' + ((look_db) == '1' ? 'readonly' : '') + ' class="sfdp-input"></div></div>' +
            '<div class="sfdp-form-item"><label class="sfdp-label">列表控件</label><div class="sfdp-input-block"><input  name="tpfd_btn" value=add type="checkbox" checked  onclick="return false;">添加 <input  name="tpfd_btn" value=Edit type="checkbox" ' + xEdit + ' >编辑 <input  name="tpfd_btn" value=Del type="checkbox" ' + xDel + ' >删除 <input  name="tpfd_btn" value=View type="checkbox" checked  onclick="return false;">查看 <input  name="tpfd_btn" value=Status ' + xStatus + ' type="checkbox">核准 <input  name="tpfd_btn" value=WorkFlow type="checkbox" ' + xWorkFlow + '>工作流 <input  name="tpfd_btn" value=Import type="checkbox" ' + XImport + '>导入 <input  name="tpfd_btn" value=DelAll type="checkbox" ' + XDelAll + '>批删 <input  name="tpfd_btn" value=Copy type="checkbox" ' + XCopy + '>复制 </div></div>' +
            '<div class="sfdp-form-item"><label class="sfdp-label">数据软删</label><div class="sfdp-input-block">' +
            '<input  name="tpfd_del" value=0 type="radio" ' + (s_del || '') + '>是' +
            '<input  name="tpfd_del" value=1 type="radio" ' + (s_del2 || '') + '>否</div><label class="sfdp-label">租户模式</label><div class="sfdp-input-block">' +
            '<input  name="tpfd_saas" value=0 type="radio" ' + (s_saas || '') + '>是' +
            '<input  name="tpfd_saas" value=1 type="radio" ' + (s_saas2 || '') + '>否</div></div>' +
            '<div class="sfdp-form-item"><label class="sfdp-label">打印组件</label><div class="sfdp-input-block">' +
            '<input  name="s_form_print" value=0 type="radio" ' + (s_form_print || '') + '>UE' +
            '<input  name="s_form_print" value=1 type="radio" ' + (s_form_print2 || '') + '>Hiprint</div><label class="sfdp-label">表单提交</label><div class="sfdp-input-block">' +
            '<input  name="s_form_submit" value=0 type="radio" ' + (s_form_submit || '') + '>回车提交数据' +
            '<input  name="s_form_submit" value=1 type="radio" ' + (s_form_submit2 || '') + '>禁用回车提交</div></div>' +
            '<div class="sfdp-form-item"><label class="sfdp-label">鉴别信息</label><div class="sfdp-input-block">' +
            '<input  name="s_sys_check" value=0 type="radio" ' + (s_sys_check || '') + '>关闭' +
            '<input  name="s_sys_check" value=1 type="radio" ' + (s_sys_check2 || '') + '>开启</div><label class="sfdp-label">编辑条件</label><div class="sfdp-input-block">' +
            '<input  name="s_sys_edit" value=0 type="radio" ' + (s_sys_edit || '') + '>不限制' +
            '<input  name="s_sys_edit" value=1 type="radio" ' + (s_sys_edit2 || '') + '>禁止编辑<input  name="s_sys_edit" value=2 type="radio" ' + (s_sys_edit3 || '') + '>本人编辑</div></div>' +
            '<div class="sfdp-form-item"><label class="sfdp-label">表单样式</label><div class="sfdp-input-block"><input  name="tpfd_style" value=0 type="radio" ' + (tpfd_style || '') + '>边框表单 <input  name="tpfd_style" value=1 type="radio" ' + (tpfd_style2 || '') + '>无边框表单</div> <label class="sfdp-label">弹窗设置</label><div class="sfdp-input-block"><input name="tpfd_open" type="text" value="' + json_data.tpfd_open + '" class="sfdp-input"></div></div>' +
            '<div class="sfdp-form-item"><label class="sfdp-label">业务样式</label><div class="sfdp-input-block"><textarea style="height: 65px" id="tpfd_class" name="tpfd_class">' + json_data.tpfd_class + '</textarea><a onclick=sfdp.insFgf("tpfd_class")>分割</a></div><label class="sfdp-label">脚本控件</label><div class="sfdp-input-block"><div><textarea style="height: 65px" id="tpfd_script" name="tpfd_script">' + json_data.tpfd_script + '</textarea><a onclick=sfdp.insFgf("tpfd_script")>分割</a></div></div></div>' +
            '<div class="sfdp-form-item" style="padding-top: 20px;text-align: center;"><div class="button" onclick="sfdp.save_data()">保存</div></div></form> ';
        layer.open({
            type: 1,
            title: '基础配置 Base Set',
            area: ['620px', '465px'], //宽高
            content: html
        });
    },
    /*6.0.0 自动转换字段长度*/
    select_type: function (obj) {
        var formclass = $(obj).parent().parent().parent().attr('class');
        var mycars = {'int':11,'varchar':255,'decimal':'5,2','time':0,'datetime':0,'longtext':0,'date':0}
        var tpfd_dblx = $("."+formclass+" select[name='tpfd_dblx']").val();
        $("."+formclass+" input[name='tpfd_dbcd']").val(mycars[tpfd_dblx]);
    },
    /*5.0.1 初始化系统配置*/
    int_data: function (int_data,type=0) {
        if (int_data === 1) {
            var local_data = localStorage.getItem("json_data_"+design_main_id);
            if (local_data != null) {
                var desc_data = local_data;
            }
        } else {
            var desc_data = int_data;
        }
        if (int_data == null || int_data === 1) {
            localStorage.setItem("json_data_"+design_main_id, JSON.stringify(sfdp.sfdp_int_data(type)));
        } else {
            localStorage.setItem("json_data_"+design_main_id, JSON.stringify(int_data));
            if(type==0){
                $('#fb_name').html(desc_data.name + '(DbTable:' + desc_data.name_db + ')');
                for (x in desc_data.list) {
                    sfdp.build_view(desc_data.list[x]['type'], desc_data.list[x]);//恢复表单布局设计
                    sfdp.recovery_input(desc_data.list[x]);//用于恢复表单字段内容
                }
                for (y in desc_data.sublist) {
                    sfdp.recovery_ui(desc_data.sublist[y]['type'], desc_data.sublist[y]);//恢复表单布局设计
                    sfdp.recovery_sub(desc_data.sublist[y]);
                }
            }else{
                editor.cmd.do('insertHTML', sfdpEditor.check_data_con(desc_data.tpfd_content))
            }
        }
        $('.move_field').draggable({
            connectToSortable: ".fb-fz",
            helper: "original",
            revert: "invalid",
            cursor: "move"
        });
    },
    /*5.0.1 恢复UI布局*/
    recovery_ui: function (id, data) {
        var lan = parseInt(12 / id);
        var html = '';
        for (var i = 1; i <= id; i++) {
            html += '<td class=" sfdp-field-con fb-fz" id=' + i + '></td>';
        }
        var td_data = data.data;
        var heads = {};
        for (x in td_data) {
            var type = td_data[x]['td_type'];
            heads[td_data[x]['td']] = td_data[x]['tpfd_name'];
        }
        var head_html = '';
        $.each(heads, function () {
            head_html += '<th style="text-align: center">' + this + '</th>';
        });
        html = '<fieldset  id="' + data.id + '_field"  mode="field" > <legend id="' + data.id + '_title">　' + (data.title || '请设置子表单标题') + '　<b title="点击可删除组件" class="ico red" id="del" data="' + data.id + '" >㊀</b>　</legend><div class="sfdp-rows " id="' + data.id + '" mode="zibiao"><div class="view-action"></div><table style="font-size: 12px;"><tr style="background-color: #f2f2f2;">'+head_html+'</tr><tr id="' + data.id + '_tr">'+html+'</tr></table></div></fieldset>';
        $('#sfdp-main').append(html);
        $(".fb-fz").sortable({
            opacity: 0.5,
            revert: true,
            animation: 150,
            stop: function (event, ui) {

                var field_code = $(ui.item).children('label').attr("data-code");
                //拖动放置后的代码转换，或者代码处理
                if(field_code !==undefined){
                    var field_id = $(ui.item).children('label').attr("data-id");
                    var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
                    var old_tr = json_data.list[field_code].data[field_id];
                    /*旧数据的样式处理下*/
                    $('#'+field_code+' #'+old_tr.td).addClass("fb-fz ui-sortable");
                    $('#'+field_code+' #'+old_tr.td).removeClass("fb-disabled ui-sortable-disabled");
                    $('#'+field_code+' #'+old_tr.td).sortable("enable");
                    /*获取放置点的位置*/
                    var parent_td = $(this).attr("id");
                    var parent_code = $(this).parent().attr("id");
                    old_tr.td = parent_td;
                    old_tr.tr_id = parent_code;
                    sfdp.dataSave({tpfd_id: old_tr.tpfd_id}, field_code, 'td_del');//删除旧的
                    sfdp.dataSave(old_tr, parent_code, 'tr_data');
                    $(this).html($(ui.item));
                    $("#up_save").trigger("click");
                }else{
                    var type = $(this).children('div').children('a').attr("data");
                    var parent_code = $(this).parent().attr("id");
                    $(this).removeClass("fb-fz");
                    $(this).removeClass("ui-sortable");
                    $(this).addClass("fb-disabled");
                    $(".fb-disabled").sortable("disable");
                    $(this).html(sfdp.bulid_tpl(type, parent_code, $(this).attr("id")));
                }
            }
        });
    },
    /*6.0.6 恢复子表数据*/
    recovery_sub: function (old_data) {
        var td_data = old_data.data;
        if (!$.isEmptyObject(old_data.data)) {
            for (x in td_data) {
                var type = td_data[x]['td_type'];
                var parent_code = old_data['id'];
                var html = sfdp.bulid_tpl(type, parent_code, td_data[x]['td'], td_data[x]['tpfd_id']);
                $('#' + parent_code+'_tr').children('td').eq(td_data[x]['td'] - 1).html(html);
                if (td_data[x]['tpfd_name'] !== undefined) {
                    sfdp.fb_set_return(td_data[x]);
                }
            }
        }
    },
    /*5.0.1 字段信息设置*/
    field_config: function (type, id, parent_code) {
        var all_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        var mode = $('#' + parent_code).attr('mode') || 'zhubiao';
        if (mode === 'zhubiao') {
            var default_data = all_data['list'][parent_code]['data'][id];
        } else {
            var default_data = all_data['sublist'][parent_code]['data'][id];
        }
        if (default_data.tpfd_db === undefined) {
            var field_name = {'text':'varchar','number':'int','money':'decimal','radio':'int','dropdown':'int','date':'date','textarea':'longtext','edit':'longtext','system_user':'int','system_role':'int','cascade':'int','sign':'longtext'}
            var field_cd = {'text':'255','number':'11','money':'5,2','radio':'11','dropdown':'11','date':'0','textarea':'0','edit':'0','system_user':'11','system_role':'11','cascade':'11','sign':'0'}
            var tpfd_db = {
                tpfd_id: id,
                tr_id: parent_code,
                tpfd_db: '',
                tpfd_name: "",tpfd_dblx : field_name[type] || 'varchar',tpfd_dbcd: field_cd[type] || '255',
                tpfd_zanwei: "",
                tpfd_moren: "",
                tpfd_chaxun: "no",
                tpfd_list: "no"
            };
        } else {
            var tpfd_db = default_data;
        }
        return sfdp.tpfd_return(type, tpfd_db);
    },
    /*5.0.1 字段信息保存*/
    save_field: function (obj) {
        var params = sfdp.fromdata($("."+obj)); //将表单序列化为JSON对象
        if (params.tpfd_db === undefined) {
            sfdp.ShowTip('请选择要设置的字段信息~');
            return;
        }
        if (!DbFieldExp.test(params.tpfd_db)) {
            sfdp.ShowTip(`　检查不通过：${params.tpfd_db}字段应为英文且为小写　`);
            return;
        }
        if (params.tpfd_name === '') {
            sfdp.ShowTip('　检查不通过：标题不能为空　');
            return;
        }
        if (params.tpfd_dbcd === '') {
            sfdp.ShowTip('　检查不通过：数据字段长度不能为空　');
            return;
        }
        if(s_type != 1){
            sfdp.fb_set_return(params);
        }
        var mode = $('#' + params.tr_id).attr('mode') || 'zhubiao';

        if (mode === 'zhubiao') {
            sfdp.dataSave(params, params.tr_id, 'td_data');
        } else {
            sfdp.dataSave(params, params.tr_id, 'sublist_datasave');
            $('.trdata').remove();
            sfdp.rec_field2(params.tr_id);
        }
        $("#up_save").trigger("click");
    },
    /*5.0.1 配置信息保存功能*/
    save_data: function () {
        var params = sfdp.fromdata($("#configform")); //将表单序列化为JSON对象
        if (params.name !== undefined) {
            if (!NameExp.test(params.name)) {
                sfdp.ShowTip(' Name格式不正确 ');
                return;
            }
            if (!DbNameExp.test(params.name_db)) {
                sfdp.ShowTip(' Dbname格式不正确 ！');
                logout('仅允许使用小写字母和数字和下划线~');
                return;
            }
            var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
            json_data['name'] = params.name;
            json_data['name_db'] = params.name_db;
            json_data['tpfd_btn'] = params.tpfd_btn;
            json_data['tpfd_class'] = params.tpfd_class;
            json_data['tpfd_script'] = params.tpfd_script;
            json_data['tpfd_del'] = params.tpfd_del;
            json_data['tpfd_saas'] = params.tpfd_saas;
            json_data['tpfd_style'] = params.tpfd_style;
            json_data['tpfd_open'] = params.tpfd_open;
            json_data['s_sys_check'] = params.s_sys_check;
            json_data['s_sys_edit'] = params.s_sys_edit;
            json_data['s_form_print'] = params.s_form_print;
            json_data['s_form_submit'] = params.s_form_submit;
            localStorage.setItem("json_data_"+design_main_id, JSON.stringify(json_data));
            sfdp.ShowTip(' 设置成功 ！');
            setTimeout(function () {
                $("#up_save").trigger("click");
                layer.closeAll();
            }, 2000);
            logout('初始化配置成功！');
        }

    },
    /*5.0.1 排序组件*/
    setSortable: function () {
        $(".fb-fz").sortable({
            opacity: 0.5,
            revert: true,
            animation: 150,
            stop: function (event, ui) {
                var type = $(this).children('div').children('a').attr("data");
                var parent_code = $(this).parent().attr("id"); //code id
                var mode = $('#' + parent_code).attr('mode') || 'zhubiao';
                if(mode==='zibiao' && type==='checkboxes'){
                    sfdp.ShowTip('不支持该组件！');
                    $(this).html('');return;
                }
                if(mode==='zibiao' && type==='cascade'){
                    sfdp.ShowTip('不支持该组件！');
                    $(this).html('');return;
                }
                var html = sfdp.bulid_tpl(type, parent_code, $(this).attr("id"));
                $(this).removeClass("fb-fz");
                $(this).removeClass("ui-sortable");
                $(this).addClass("fb-disabled");
                $(".fb-disabled").sortable("disable");
                $(this).html(html);
            }
        });
    },
    fb_set_return: function (data) {
        $('#label' + data.tpfd_id).html(data.tpfd_name + '：');
    },
    /*5.0.1 附表设计*/
    build_fb: function (old_data = '') {
        var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        if (json_data.name === '') {
            sfdp.ShowTip(' 请先执行基础配置！');
            return;
        }
        var code = 'Id' + sfdp.dateFormat(new Date(), "hhmmssS");
        $('#sfdp-main').append('<fieldset id="' + code + '_field" mode="field"> <legend id="' + code + '_title">子表设计</legend><div class="sfdp-rows " mode="zibiao" id="' + code + '"><div class="view-action"><b title="点击可删除组件" class="ico" id="del" data="' + code + '" >㊀</b></div><div class="col-6 sfdp-field-con fb-fz"  id="1"></div><div class="col-6 sfdp-field-con fb-fz" id="2"></div></div></fieldset>');
        if (old_data === '') {
            sfdp.dataSave({id: code, title: '', data: {}, type: 2}, code, 'sublist');
        }
        sfdp.setSortable();
    },
    /*5.0.1 布局模式设计*/
    build_bj: function (old_data = '',tdnum=2) {
        var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        if (json_data.name === '') {
            sfdp.ShowTip(' 请先执行基础配置！');
            return;
        }
        var code = 'Id' + sfdp.dateFormat(new Date(), "hhmmssS");
        var tdhtml = '';
        var col = 12/tdnum;
        for (var i = 1; i <= tdnum; i++) {
            tdhtml += `<div class="col-${col} sfdp-field-con fb-fz"  id="${i}"></div>`;
        }
        $('#sfdp-main').append(`<div class="sfdp-rows" id="${code}"><div class="view-action"><b title="点击可删除组件" class="ico" id="del" data="${code}" >㊀</b></div>${tdhtml}</div>`);
        if (old_data === '') {
            sfdp.dataSave({tr: code, data: {}, type: tdnum, show: 1}, code, 'tr');
        }
        sfdp.setSortable();
        $("#up_save").trigger("click");
        setTimeout(function () {
            window.location.reload();
        }, 1000);
    },
    /*7.0.0 批量布局模式设计*/
    build_bjs: function (num) {
        for (var i = 1; i <= 9; i++) {
            sfdp.build_bj('',num);
        }
    },
    /*5.0.1 拖拽进去转换信息*/
    bulid_tpl: function (type, parent_code, td_xh, old_data = 0) {
        if (old_data === 0) {
            var td_id = type + '_' + sfdp.dateFormat(new Date(), "yyyyMMddhhmmssS");
        } else {
            var td_id = old_data;
        }
        var labid = ' data-type="' + type + '" data-id="' + td_id + '" data-code="' + parent_code + '" id="label' + td_id + '"';
        var mode = $('#' + parent_code).attr('mode') || 'zhubiao';
        var html = sfdp.btn_to_input(labid, type ,mode);

        if (mode === 'zhubiao') {
            if (old_data === 0) {
                sfdp.dataSave({td: td_xh, td_type: type, tpfd_id: td_id}, parent_code, 'tr_data');
            }
        } else {
            if (old_data === 0) {
                sfdp.dataSave({td: td_xh, td_type: type, tpfd_id: td_id}, parent_code, 'sublist_data');
            }
        }
        return html;
    },
    /*5.0.1 构建样式布局*/
    build_view: function (id, old_data = '', showtype = '') {
        if (old_data === '') {
            var code = 'Tr' + sfdp.dateFormat(new Date(), "hhmmssS");
        } else {
            var code = old_data['tr'];
        }
        var lan = parseInt(12 / id);
        var html = '';
        for (var i = 1; i <= id; i++) {
            html += '<div class="col-' + lan + ' sfdp-field-con fb-fz" id=' + i + '></div>';
        }
        html = '<div class="sfdp-rows " id="' + code + '"><div class="view-action"><b title="点击可删除组件" class="ico" id="del" data="' + code + '" >㊀</b></div>' + html + '</div>';
        var logs = '新增1*' + id + '单元行';
        if (old_data === '') {
            sfdp.dataSave({tr: code, data: {}, type: id}, code, 'tr');
            logout(logs);
        } else {
            logout('[恢复]' + logs);
        }
        if (showtype === '') {

            $('#sfdp-main').append(html);
            $(".fb-fz").sortable({
                opacity: 0.5,
                revert: true,
                animation: 150,
                stop: function (event, ui) {
                    var field_code = $(ui.item).children('label').attr("data-code");
                    //拖动放置后的代码转换，或者代码处理
                    if(field_code !==undefined){
                        var field_id = $(ui.item).children('label').attr("data-id");
                        var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
                        var old_tr = json_data.list[field_code].data[field_id];

                        /*旧数据的样式处理下*/
                        $('#'+field_code+' #'+old_tr.td).addClass("fb-fz ui-sortable");
                        $('#'+field_code+' #'+old_tr.td).removeClass("fb-disabled ui-sortable-disabled");
                        $('#'+field_code+' #'+old_tr.td).sortable("enable");
                        /*获取放置点的位置*/
                        var parent_td = $(this).attr("id");
                        var parent_code = $(this).parent().attr("id");
                        old_tr.td = parent_td;
                        old_tr.tr_id = parent_code;
                        $('#'+parent_code+' #'+parent_td).removeClass("fb-fz");
                        $('#'+parent_code+' #'+parent_td).addClass("fb-disabled ui-sortable-disabled");
                        sfdp.dataSave({tpfd_id: old_tr.tpfd_id}, field_code, 'td_del');//删除旧的
                        sfdp.dataSave(old_tr, parent_code, 'tr_data');
                        $(this).html($(ui.item));
                        $('#'+parent_code+' #'+parent_td +' .move_field').addClass("ui-draggable ui-draggable-handle");
                        $('#label'+field_id).attr("data-code",parent_code);
                        $('.move_field').draggable({
                            connectToSortable: ".fb-fz",
                            helper: "original",
                            revert: "invalid",
                            cursor: "move"
                        });
                        $("#up_save").trigger("click");
                    }else{
                        var type = $(this).children('div').children('a').attr("data");
                        var parent_code = $(this).parent().attr("id");
                        $(this).removeClass("fb-fz");
                        $(this).removeClass("ui-sortable");
                        $(this).addClass("fb-disabled");
                        $(".fb-disabled").sortable("disable");

                        if(type=='user_field'){
                            var json = $(this).children('div').children('a').attr("data-json");
                            var json = JSON.parse(json);
                            var td_id = json.td_type + '_' + sfdp.dateFormat(new Date(), "yyyyMMddhhmmssS");
                            $(this).html(sfdp.bulid_tpl(json.td_type, parent_code, $(this).attr("id"),td_id));
                            json.td = $(this).attr("id");
                            json.tpfd_id = td_id;
                            json.tr_id = parent_code
                            sfdp.fb_set_return(json);
                            sfdp.dataSave(json, parent_code, 'tr_data');
                            $("#up_save").trigger("click");
                            return;
                        }
                        $(this).html(sfdp.bulid_tpl(type, parent_code, $(this).attr("id")));
                    }
                }
            });

        } else {
            return html;
        }

    },
    /*5.0.1 恢复表单布局中的样式并初始化*/
    recovery_input: function (old_data) {
        var td_data = old_data.data;
        if (!$.isEmptyObject(old_data.data)) {
            for (x in td_data) {
                var type = td_data[x]['td_type'];
                var parent_code = old_data['tr'];

                var html = sfdp.bulid_tpl(type, parent_code, td_data[x]['td'], td_data[x]['tpfd_id']);
                var elem = $('#' + parent_code).children('div').eq(td_data[x]['td']);
                elem.removeClass("fb-fz");
                elem.removeClass("ui-sortable");
                elem.addClass("fb-disabled");
                elem.html(html);
                $(".fb-disabled").sortable("disable");//阻止排序
                if (td_data[x]['tpfd_name'] !== undefined) {
                    sfdp.fb_set_return(td_data[x]);
                }
            }
        }
    },
    /*5.0.1 判断存储的数据是否存在*/
    has_item: function (Item) {
        var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        if (json_data[Item] === '') {
            alert('参数' + Item + '未配置！');
            return;
        }
        return true;
    },
    /*6.0.0*/
    sub_to_json: function () {
        /*构建子表json*/
        var searchdata = [];
        var stable = [];
        var allSdata = {};
        /*遍历找出所有子表*/
        $(".sub_list").each(function(index, el) {
            var id = $(this).attr("data");
            if(typeof(id)!=='undefined'){
                stable.push(id)
            }
        });
        //遍历子表，找出子表的表单数据
        $.each(stable, function(index,table) {
            var search = $('#'+table).serializeArray();
            //表单数据拼接成数组
            var i = 0;
            $.each(search, function(index,vars) {
                var dbname = this.name;
                if(searchdata[dbname] == undefined){
                    searchdata[dbname] = [];
                    searchdata[dbname].push(vars.value || '');
                }else{
                    searchdata[dbname].push(vars.value);
                }
            });
            allSdata[table] = {};
            allSdata[table] = Object.assign({},searchdata);
            searchdata = [];
        });
        return JSON.stringify(allSdata)
    },
    /*6.0.0*/
    list_to_json: function (search) {
        var searchdata = {};
        $.each(search, function() {
            var dbname = this.name;
            if(dbname.indexOf("[]") >= 0 && dbname.indexOf("@") < 0 ) {
                var rdbname = dbname.replace("[", "").replace("]", "");
                if(searchdata[rdbname] == undefined){
                    searchdata[rdbname] = this.value;
                }else{
                    searchdata[rdbname] = searchdata[rdbname] +',' + this.value;
                }
                //@函数为 日期时间的判断符号，可以进行区间查询
            }else if(dbname.indexOf("@") >= 0 ){
                var rdbname = dbname.replace("[0]", "").replace("[1]", "");
                if(searchdata[rdbname] == undefined){
                    if(this.value != ''){
                        searchdata[rdbname] = this.value;
                    }
                }else{
                    searchdata[rdbname] = searchdata[rdbname] +',' + this.value;
                }
            }else{
                searchdata[dbname] = this.value;
            }
        });
        return searchdata;
    },
    Ico: function (id = 0) {
        var data = ['<svg style="padding: 10px;" t="1616854567257" class="icon" viewBox="0 0 1352 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2095" width="120" height="80"><path d="M1086.603604 415.596184c-13.65934-211.566186-160.799312-415.596184-430.627602-415.596184-246.810151 0-437.119373 183.448833-447.215407 428.272544-135.037018 42.022439-208.760594 186.520649-208.760594 291.699618 0 161.946123 134.586485 303.986881 287.99296 303.986881l207.982401 0c8.82635 0 15.99392-7.16757 15.99392-15.99392s-7.16757-15.99392-15.99392-15.99392l-207.982401 0c-136.368138 0-255.984641-127.091255-255.984641-271.978561 0-97.622303 85.560306-271.978561 255.984641-271.978561l48.00224 0c8.82635 0 15.99392-7.16757 15.99392-15.99392s-7.16757-15.99392-15.99392-15.99392l-48.00224 0c-16.280623 0-31.680659 1.822611-46.691599 4.443893 12.553487-191.66082 158.239466-388.420855 414.654161-388.420855 274.804632 0 399.970882 215.641462 399.970882 415.985281l0 48.00224c0 8.82635 7.16757 15.99392 15.99392 15.99392s15.99392-7.16757 15.99392-15.99392l0-48.00224c0-0.102394 0-0.184309 0-0.286703 96.598364 10.587525 223.9968 114.271544 223.9968 272.265264 0 122.422095-118.101074 271.978561-255.984641 271.978561l-239.990721 0c-103.602104 0-143.986241-40.384137-143.986241-143.986241l0-438.921505 130.490731 130.224507c3.133252 3.133252 7.229006 4.710117 11.324761 4.710117s8.191509-1.576865 11.324761-4.66916c6.246025-6.246025 6.246025-16.423975 0-22.629042l-142.737036-142.450333c-19.0043-18.942863-33.749015-18.942863-52.732836 0l-142.737036 142.450333c-6.246025 6.205068-6.246025 16.383017 0 22.629042s16.383017 6.246025 22.629042 0l130.449773-130.265464 0 438.921505c0 121.725816 54.268744 175.99456 175.99456 175.99456l239.990721 0c155.085735 0 287.99296-167.16821 287.99296-303.986881 0-165.32512-135.118933-294.628082-257.33624-304.375977z" p-id="2096" fill="#2d6dcc"></path></svg>', '<svg t="1616854233639" class="icon" viewBox="0 0 1352 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="1148" width="20" height="20"><path d="M1086.667547 415.408012c-13.762285-211.51321-160.867183-415.408012-430.644827-415.408012-32.931181 0-65.411812 3.194816-96.581748 9.50253-8.662867 1.761245-14.233315 10.178356-12.51303 18.882182 1.761245 8.662867 10.055479 14.233315 18.882182 12.51303 29.060539-5.918602 59.431771-8.888142 90.212596-8.888142 274.815624 0 399.98688 215.650087 399.98688 416.00192l0 48.00416c0 8.826703 7.167857 15.99456 15.99456 15.99456s15.99456-7.167857 15.99456-15.99456l0-48.00416c0-0.163837-0.040959-0.348153-0.040959-0.51199 98.01532 8.437591 224.026239 93.817004 224.026239 272.50143 0 152.531989-112.453431 271.98944-255.99488 271.98944l-767.98464 0c-127.239695 0-255.99488-93.448371-255.99488-271.98944 0-131.00794 80.116158-271.98944 255.99488-271.98944l48.00416 0c8.826703 0 15.99456-7.167857 15.99456-15.99456s-7.167857-15.99456-15.99456-15.99456l-48.00416 0 0-63.99872c0-119.252655 73.562689-193.347813 191.99616-193.347813 107.640727 0 191.99616 84.928861 191.99616 193.347813l0 415.776644-133.117338-123.51241c-6.410112-6.021-16.547509-5.672847-22.588988 0.839663-6.021 6.471551-5.672847 16.608948 0.839663 22.588988l144.50399 134.038919c9.563969 9.543489 18.02204 14.233315 26.459631 14.233315 8.355673 0 16.649907-4.648867 25.845243-13.864683l144.934061-134.468991c6.49203-6.021 6.881142-16.137917 0.839663-22.588988-6.021-6.532989-16.199356-6.881142-22.588988-0.839663l-133.117338 123.553369 0-415.776644c0-126.359073-98.404432-225.336933-224.00576-225.336933-136.066399 0-224.00576 88.451351-224.00576 225.336933l0 65.637087c-168.157917 16.260795-255.99488 160.088958-255.99488 302.360673 0 147.350653 100.923422 303.99904 288.00448 303.99904l767.98464 0c161.50205 0 288.00448-133.526929 288.00448-303.99904 0-201.723966-145.814684-296.892622-257.346533-304.572469z" p-id="1149" fill="#2d6dcc"></path></svg>', '<svg t="1616854567257" class="icon" viewBox="0 0 1352 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2095" width="20" height="20"><path d="M1086.603604 415.596184c-13.65934-211.566186-160.799312-415.596184-430.627602-415.596184-246.810151 0-437.119373 183.448833-447.215407 428.272544-135.037018 42.022439-208.760594 186.520649-208.760594 291.699618 0 161.946123 134.586485 303.986881 287.99296 303.986881l207.982401 0c8.82635 0 15.99392-7.16757 15.99392-15.99392s-7.16757-15.99392-15.99392-15.99392l-207.982401 0c-136.368138 0-255.984641-127.091255-255.984641-271.978561 0-97.622303 85.560306-271.978561 255.984641-271.978561l48.00224 0c8.82635 0 15.99392-7.16757 15.99392-15.99392s-7.16757-15.99392-15.99392-15.99392l-48.00224 0c-16.280623 0-31.680659 1.822611-46.691599 4.443893 12.553487-191.66082 158.239466-388.420855 414.654161-388.420855 274.804632 0 399.970882 215.641462 399.970882 415.985281l0 48.00224c0 8.82635 7.16757 15.99392 15.99392 15.99392s15.99392-7.16757 15.99392-15.99392l0-48.00224c0-0.102394 0-0.184309 0-0.286703 96.598364 10.587525 223.9968 114.271544 223.9968 272.265264 0 122.422095-118.101074 271.978561-255.984641 271.978561l-239.990721 0c-103.602104 0-143.986241-40.384137-143.986241-143.986241l0-438.921505 130.490731 130.224507c3.133252 3.133252 7.229006 4.710117 11.324761 4.710117s8.191509-1.576865 11.324761-4.66916c6.246025-6.246025 6.246025-16.423975 0-22.629042l-142.737036-142.450333c-19.0043-18.942863-33.749015-18.942863-52.732836 0l-142.737036 142.450333c-6.246025 6.205068-6.246025 16.383017 0 22.629042s16.383017 6.246025 22.629042 0l130.449773-130.265464 0 438.921505c0 121.725816 54.268744 175.99456 175.99456 175.99456l239.990721 0c155.085735 0 287.99296-167.16821 287.99296-303.986881 0-165.32512-135.118933-294.628082-257.33624-304.375977z" p-id="2096" fill="#2d6dcc"></path></svg>'];
        return data[id];
    },
    add_field:function(dataType,dataCode){
        var dataType = $('#type').val();
        if(dataType==''){
            layer_msg('未选择添加组件！');return;
        }
        var all_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        var default_data = all_data['sublist'][dataCode]['data'];
        var td_xh = Object.keys(default_data).length + 1
        sfdp.dataSave(td_xh, dataCode, 'sublist_num');
        $('#zibiaonum').val(td_xh);
        var dataId = dataType + '_' + sfdp.dateFormat(new Date(), "yyyyMMddhhmmssS");
        sfdp.dataSave({td: td_xh, td_type: dataType, tpfd_id: dataId}, dataCode, 'sublist_data');
        var html = sfdp.field_config(dataType, dataId, dataCode);
        $('#zbcon').html(html);
    },
    rec_field2:function(dataCode){
        var all_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        var default_data = all_data['sublist'][dataCode]['data'];
        var news_data  = [];
        $.each(default_data,function(index,obj){
            news_data.push(obj);
        });
        var new_datas = sfdp.sortByKey(news_data,'td','asc');
        $.each(new_datas,function(index,obj){
            $('#trdata').after(`<tr class="trdata"><td data-id="${obj.td}" style="text-align: center"><a style="font-size: 6px;padding: 1px 3px;" class="button" onclick=sfdp.del_sub(this,"${obj.tpfd_id}","${obj.tr_id}","${obj.td}")>删</a> <a style="font-size: 6px;padding: 1px 3px;" class="button" onclick=sfdp.moveUp(this)> 上移 </a>  <a style="font-size: 6px;padding: 1px 3px;" class="button" onclick=sfdp.moveDown(this)> 下移 </a></td><td style="text-align: center" onclick=sfdp.set_sub_sx('${obj.td_type}','${obj.tpfd_id}','${dataCode}')>${obj.tpfd_name}(${obj.tpfd_db})<input name="tpfd_id" type="hidden" value="${obj.tpfd_id}"><input name="tr_id" type="hidden" value="${obj.tr_id}"></td> <td style="text-align: center">${obj.td_type}</td><td style="text-align: center">${obj.tpfd_dbcd}</td></tr>`);
        });
    },
    set_sub_sx:function(td_type,tpfd_id,dataCode){
        var html = sfdp.field_config(td_type, tpfd_id, dataCode);
        $('#zbcon').html(html);
    },
    rec_field(dataCode){
        var all_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        var default_data = all_data['sublist'][dataCode]['data'];
        var obj = [{'pv':100,'uv':105},{'pv':123,'uv':132},{'pv':128,'uv':138},{'pv':88,'uv':68}];
        var news_data  = [];
        $.each(default_data,function(index,obj){
            news_data.push(obj);
        });
        var new_datas = sfdp.sortByKey(news_data,'td','asc');
        $.each(new_datas,function(index,obj){
            var html = sfdp.field_config(obj.td_type, obj.tpfd_id, dataCode);
            $('#trdata').after(`<tr class="trdata"><td data-id="${obj.td}" style="text-align: center"><a style="font-size: 6px;padding: 1px 3px;" class="button" onclick=sfdp.del_sub(this,"${obj.tpfd_id}","${obj.tr_id}","${obj.td}")>删</a> <a style="font-size: 6px;padding: 1px 3px;" class="button" onclick=sfdp.moveUp(this)> ↑ </a>  <a style="font-size: 6px;padding: 1px 3px;" class="button" onclick=sfdp.moveDown(this)> ↓ </a></td><td>${html}</td> </tr>`);
        });
    },
    sortByKey(arr,key,order){
        for(i=0;i<arr.length;i++){
            for(j=i+1;j<arr.length;j++){
                if(order=='desc'){
                    if(parseFloat(arr[i][key])<=parseFloat(arr[j][key])){
                        var min=arr[i];
                        arr[i]=arr[j];
                        arr[j]=min;
                    }
                }else{
                    if(parseFloat(arr[i][key])>=parseFloat(arr[j][key])){
                        var max=arr[i];
                        arr[i]=arr[j];
                        arr[j]=max;
                    }
                }
            }
        }
        return arr;
    },
    moveUp(obj) {
        var current = $(obj).parent().parent(); //获取当前<tr>
        var prev = current.prev();  //获取当前<tr>前一个元素
        if(prev[0].id=='trdata'){
            layer.msg('Tip:已经是最顶级了！');
            return;
        }
        if (current.index() > 0) {
            current.insertBefore(prev); //插入到当前<tr>前一个元素前
        }
        var all_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        var tr_id = current.children('td').eq(1).find('input[name=tr_id]').val();//
        var tpfd_id = current.children('td').eq(1).find('input[name=tpfd_id]').val();//本
        var ptpfd_id = prev.children('td').eq(1).find('input[name=tpfd_id]').val();//上一个
        var default_data = all_data['sublist'][tr_id]['data'];
        var tr_data = default_data[tpfd_id];
        var ptr_data = default_data[ptpfd_id];
        var td1 = tr_data.td;
        var td2 = ptr_data.td;
        tr_data.td = td2;
        ptr_data.td = td1;
        sfdp.dataSave({tpfd_id: tpfd_id}, tr_id, 'sublist_td_del');
        sfdp.dataSave(tr_data, tr_id, 'sublist_data');
        sfdp.dataSave({tpfd_id: ptpfd_id}, tr_id, 'sublist_td_del');
        sfdp.dataSave(ptr_data, tr_id, 'sublist_data');
        $("#up_save").trigger("click");
    },
    moveDown(obj) {
        var current = $(obj).parent().parent(); //获取当前<tr>
        var next = current.next(); //获取当前<tr>后面一个元素
        if (next.length>0) {
            current.insertAfter(next);  //插入到当前<tr>后面一个元素后面
            var all_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
            var tr_id = current.children('td').eq(1).find('input[name=tr_id]').val();//
            var tpfd_id = current.children('td').eq(1).find('input[name=tpfd_id]').val();//本
            var ptpfd_id = next.children('td').eq(1).find('input[name=tpfd_id]').val();//上一个
            console.log(tr_id);
            var default_data = all_data['sublist'][tr_id]['data'];
            var tr_data = default_data[tpfd_id];
            var ptr_data = default_data[ptpfd_id];
            var td1 = tr_data.td;
            var td2 = ptr_data.td;
            tr_data.td = td2;
            ptr_data.td = td1;
            sfdp.dataSave({tpfd_id: ptpfd_id}, tr_id, 'sublist_td_del');
            sfdp.dataSave(ptr_data, tr_id, 'sublist_data');
            sfdp.dataSave({tpfd_id: tpfd_id}, tr_id, 'sublist_td_del');
            sfdp.dataSave(tr_data, tr_id, 'sublist_data');
            $("#up_save").trigger("click");
        }else{
            layer.msg('Tip:已经是最后一个了！');
            return;
        }
    },
    del_sub(obj,tpfd_id,tr_id,xh){
        $(obj).parent().parent().remove();
        sfdp.dataSave({tpfd_id: tpfd_id}, tr_id, 'sublist_td_del');
        var all_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
        var default_data = all_data['sublist'][tr_id]['data'];

        $.each(default_data,function(index,val) {
            if(val.td > xh){
                val.td = val.td-1;
                sfdp.dataSave(val, tr_id, 'sublist_data');
            }
        });
        sfdp.dataSave(Object.keys(default_data).length, tr_id, 'sublist_num');
        $('.trdata').remove();
        sfdp.rec_field2(tr_id);

        $("#up_save").trigger("click");
    },
    /*7.0.1*/
    default_data(val){
        if(val.indexOf("{uid}") >= 0 ) {
            return  g_uid;
        }
        if(val.indexOf("{role}") >= 0 ) {
            return  g_role;
        }
        if(val.indexOf("{username}") >= 0 ) {
            return  g_username;
        }
        if(val.indexOf("{realname}") >= 0 ) {
            return  parent.g_soft_realname;
        }
        if(val.indexOf("{rolename}") >= 0 ) {
            return  parent.g_soft_rolename;
        }
        if(val.indexOf("{nowdate}") >= 0 ) {
            return  sfdp.dateFormat(new Date(), "yyyy-MM-dd");
        }
        if(val.indexOf("{nowdatetime}") >= 0 ) {
            return  sfdp.dateFormat(new Date(), "yyyy-MM-dd hh:mm:ss");
        }
        return val;
    },
    showInform(obj, type,name) {
        if(type==0){
            var tmp = `<span onclick="sfdp.set_tip_data('${name}','{uid}')">用户id:{uid}</span><br/><span onclick="sfdp.set_tip_data('${name}','{role}')">用户角色id:{role}</span><br/><span onclick="sfdp.set_tip_data('${name}','{username}')">操作员名:{username}</span><br/><span onclick="sfdp.set_tip_data('${name}','{nowdate}')">当前日期:{nowdate}</span><br/><span onclick="sfdp.set_tip_data('${name}','{nowdatetime}')">日期时间:{nowdatetime}</span><br/><span onclick="sfdp.set_tip_data('${name}','{realname}')">真实姓名:{realname}</span><br/><span onclick="sfdp.set_tip_data('${name}','{rolename}')">角色名字:{rolename}</span>`
        }
        if(type==1){
            var zzys = {"*":"不能为空！", "*6-16":"请填写6到16位任意字符！", "n":"请填写数字！", "n6-16":"请填写6到16位数字！", "s":"不能输入特殊字符！", "s6-18":"请填写6到18位字符！", "p":"请填写邮政编码！", "m":"请填写手机号码！", "e":"邮箱地址格式不对！", "url":"请填写网址！", "idcard":"身份证格式不正确！"}
            var tmp ='';
            $.each(zzys, function (k,n) {
                tmp += `<span onclick="sfdp.set_tip_data('${name}','${k}')">${n}</span><br/>`;
            });
        }
        if ($('.inform').length <1) {
            var htmlDiv = `<div class='inform' id='${name}_divTip' >${tmp} <a style='color:red;top: 0px;float: right;position: absolute;right: 1%;' onclick=$("#${name}_divTip").remove()>✖</a></div>`
            $(obj).parent().append(htmlDiv);
        }
    },
    set_tip_data(obj,val){
        $('#tpfd_yztype_divTip').remove()
        $('#tpfd_zanwei_divTip').remove()
        $('input[name="'+obj+'"]').val(val);
    },
    open_design(url,sid=''){
        return layer.open({
            type:2,
            area: ['99.5%', '99.5%'],
            fix: false, // 不固定
            title: "业务设计",
            shade: 0,
            anim:'slideUp',
            content: url,
            success: function (layero, index) {
                var title = layero.find('.layui-layer-title'); // Get the title element
                title.css({
                    'height': '0px', // Set the font size
                    'border': '0px', // Set the color
                    // Add your other CSS properties here
                });
            },
            cancel: function(index, layero, that){
                localStorage.removeItem('json_data_'+sid);//清空设计数据，减少干扰
                layer.close(index);
            }
        });
    }
}
/*5.0.1 日志输出信息*/
function logout(info) {
    if (Debug) {
        console.log(sfdp.dateFormat(new Date(), "mm:ss") + '] ' + info);
    }
}
/*5.0.1 是否在数组里面判断*/
function isInArray(arr, value) {
    for (var i = 0; i < arr.length; i++) {
        if (value === arr[i]) {
            return true;
        }
    }
    return false;
}
/*5.0.1 监听单元格点击状态*/
$("#sfdp-main").on("click", ".sfdp-rows", function (e) {
    var activeDom = $(this).attr('id');
    var show= 1;
    var mode = $(this).attr('mode') || 'zhubiao';
    var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
    var default_data = [{cid: 0, clab: '隐藏'}, {cid: 1, clab: '显示'}];
    if (mode === 'zibiao') {
        var rownum = json_data.sublist[activeDom].type;
        var html = '<style>.trdata .sfdp-label{min-width:60px;}.trdata .sfdp-form-item{margin-bottom: 2px;clear: none;}.trdata .sfdp-title,.wybs,.zjkd{display: none}.trdata .sfdp-input{width: 90px}</style><div class="sfdp-form-item" style="padding-top: 10px;">' +
            '<label class="sfdp-label">子表标识</label><div class="sfdp-input-block"><input type="text" readonly id="row-id" value="' + activeDom +  '" class="sfdp-input"></div> <label class="sfdp-label">子表标题</label><div class="sfdp-input-block"><input id="row-title" autocomplete="off"  type="text" value="' + (json_data.sublist[activeDom].title || '请设置子表单标题') + '" class="sfdp-input"></div><label class="sfdp-label">子表列数</label><div class="sfdp-input-block"><input readonly id="zibiaonum" type="text" value="' + rownum + '" class="sfdp-input"></div><label class="sfdp-label" >添加列</label><div class="sfdp-input-block"><select id="type"><option value="text">文本</option><option value="number">数字</option><option value="money">金额</option><option value="upload">上传</option><option value="dropdown">下拉</option><option value="date">时间日期</option><option value="textarea">多行文本</option><option  value="time_range">时间范围</option><option value="dropdowns">下拉多选</option><option value="cascade">级联组件</option><option value="tag">tag输入</option></select><div class="button" onclick=sfdp.add_field("text","'+activeDom+'")>添加</div></div></div>' +
            '' +
            '<div class="sfdp-form-item">' +
            '<div class="layui-row layui-col-space15"><div class="col-7"><div class="sfdp-card"><div class="sfdp-card-head">子表字段</div><div class="sfdp-card-body" style="height: 750px;overflow-y: auto;"><table class="table"><tr class="text-c title" id="trdata" style="background-color: #f2f2f2;"><th style="width: 30%;text-align: center">序号</th><th style="width: 40%;text-align: center">字段信息</th><th style="width: 15%;text-align: center">字段类型</th><th style="width: 15%;text-align: center">字段长度</th></tr></table></div></div></div><div class="col-5"><div class="sfdp-card"><div class="sfdp-card-body" style="height: 750px;overflow-y: auto;"><div id="zbcon"><div class="sfdp-card-head">属性配置</div></div></div></div></div></div></div></form> ';
        layer.open({
            type: 1,
            title: '子表设计 Schedule Set',
            area: ['55%', '85%'], //宽高
            content: html,
            cancel: function(index, layero){
                window.location.reload();
            }
        });
        sfdp.rec_field2(activeDom);
        return;
    } else {
        var zbtitle = '';
        var rownum = json_data.list[activeDom].type;
        var show = json_data.list[activeDom].show;
    }
    $('.sfdp-rows').removeClass('sfdp-ctrl-active');
    $('#' + activeDom).addClass('sfdp-ctrl-active');
    var html = '<div  style="margin: 5px;"><div class="sfdp-title">布局配置</div><div class="sfdp-form-item"><label class="sfdp-label">唯一标识</label><div class="sfdp-input-block"><input id="row-id" type="text" name="id" autocomplete="off" value=' + activeDom + ' class="sfdp-input"></div></div><div class="sfdp-form-item"><label class="sfdp-label">显示隐藏</label><div class="sfdp-input-block">' + sfdp.tpfd_select(default_data, 'show', show) + '</div></div><div class="sfdp-form-item"><label class="sfdp-label">布局栅格</label><div class="sfdp-input-block"><input id="row-num" type="number" name="id" autocomplete="off" value=' + rownum + ' class="sfdp-input"></div></div>' + zbtitle + '</div>';
    $('.sfdp-att').html(html);
});

/*5.0.1 监听删除点击事件*/
$("#sfdp-main").on("click", "#del", function (event) {
    const is_del = confirm("是否继续执行删除操作？");
    if (!is_del) {
       return;
    }



    var mode = $('#' + $(this).attr("data")).attr('mode') || 'zhubiao';
    if (mode === 'zhubiao') {
        $('#' + $(this).attr("data")).remove();
        sfdp.dataSave('', $(this).attr("data"), 'tr_del');
    } else {
        $('#' + $(this).attr("data") + '_field').remove();
        sfdp.dataSave('', $(this).attr("data"), 'sublist_del');
    }
    event.stopPropagation();
});
/*6.0.0*/
$("#sfdp-main").on("click", "#del_con", function (event) {
    var parent_id = $(this).parent().parent().parent().attr("id");//得到父元素的ID
    $(this).parent().parent().removeClass("fb-disabled ui-sortable-disabled");
    $(this).parent().parent().addClass("fb-fz ui-sortable");
    $(this).parent().parent().sortable("enable");
    var id = $(this).parent().children("label").attr("data-id");
    sfdp.dataSave({tpfd_id: id}, parent_id, 'td_del');
    $(this).parent().empty();
    event.stopPropagation();
});
/*5.0.1 监听设置属性*/
$("#sfdp-main").on("click", ".sfdp-label", function (event) {
    var dataId = $(this).attr("data-id");
    var dataCode = $(this).attr("data-code");
    var dataType = $(this).attr("data-type");
    var html = sfdp.field_config(dataType, dataId, dataCode);
    $('.sfdp-att').html(html);
    event.stopPropagation();
});
/*6.0.0 监听栅格变化设计 优化处理*/
$(document).on("input propertychange", "#row-num", function (e) {
    var p_id = $('#row-id').val();
    var num = $(this).val();
    if(num==0){
        layer.msg('Tip:栅格不能为空。');
        return;
    }
    if (num < 0 || num > 12) {
        $(this).val(12);
        num = 12;
        layer.msg('Tip:栅格布局只能在1~12区间。');
        return;
    }
    var elem = $('#' + p_id + ' .sfdp-field-con');
    var nowcd = elem.length;
    var lan = parseInt(12 / num);
    var old_lan = parseInt(12 / nowcd);
    if(num>nowcd){
        elem.removeClass('col-'+old_lan);
        elem.addClass('col-'+lan);
        $('#' + p_id).append(`<div class="col-${lan} sfdp-field-con fb-fz" id=${num}></div>`);
    }else{
        elem.removeClass('col-'+old_lan);
        elem.addClass('col-'+lan);
        var removeid = $('#' + p_id + ' .sfdp-field-con:last label').attr("data-id");
        elem.eq(-1).remove();
    }
    sfdp.setSortable();
    var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
    var show = $("select[name='show']").find("option:selected").val();
    var trdata = json_data.list[p_id].data;//Json数据
    if(num>nowcd){
        sfdp.dataSave({tr: p_id, data: trdata, type: num, show: show}, p_id, 'tr');
    }else{
        var datacd = Object.keys(trdata).length;//数据长度
        if(num<datacd){
            delete trdata[removeid];
        }
        sfdp.dataSave({tr: p_id, data: trdata, type: num, show: show}, p_id, 'tr');
    }
});
/*5.0.1 监听子表标题设置*/
$(document).on('input propertychange', "select[name='show']", function(event) {
    var p_id = $('#row-id').val();
    var show = $(this).val();
    var json_data = JSON.parse(localStorage.getItem("json_data_"+design_main_id));
    var trdata = json_data.list[p_id];
    trdata['show'] = show;
    sfdp.dataSave(trdata, p_id, 'tr');
});

/*5.0.1 监听子表标题设置*/
$(document).on("input propertychange", "#row-title", function () {
    var title = $('#row-title').val();
    var p_id = $('#row-id').val();
    $('#' + p_id + '_title').html(title);
    sfdp.dataSave(title, p_id, 'sublist_title');
    $("#up_save").trigger("click");
});
