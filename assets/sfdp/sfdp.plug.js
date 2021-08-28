/**
 *
 * @type {string[]}
 */
var dataType = ['yyyy', 'MM-dd', 'yyyy-MM-dd', 'yyyyMMdd', 'yyyy-MM','yyyy-MM-dd HH:mm:ss'];
var dateType_format= ['date', 'datetime', 'time', 'year', 'month'];

var sfdpPlug = {
    PlugInit:function (type,data,is_sub= false,Curd='add') {
        var maste ='';
        var read ='';
        if (data.tpfd_must == 0) {
            var maste = 'datatype="*" nullmsg="请填写'+data.tpfd_name+'"';
        }
        if (data.tpfd_read == 0) {
            var read = 'readonly';
        }
        var field_att = maste + read;
        if (typeof (data['tpfd_name']) == 'undefined') {
            return sfdpPlug.view_default(type, data);
        } else {

            var lab = '<label class="' + sfdp.ui.rows_label + '"><b>' + data.tpfd_name + '</b></label><div class="' + sfdp.ui.rows_block + '">';
            if (is_sub) {
                lab = '';
            }
            if(type=='group'){
                lab ='<fieldset class="layui-elem-field layui-field-title" style="margin: 0px;"><legend>' + data.tpfd_name + '</legend></fieldset>';
            }
            var html = lab + sfdpPlug.common(type,data,field_att,Curd);
        }
        return html + '</div>';
    },
    PlugList:function (type,item) {
        var data ='';
        if(type==='radio'||type==='checkboxes'){
            item.tpfd_db = item.field;
            item.tpfd_data = eval('(' + item.type_data + ')');
        }
        if(type==='dropdown') {
            item.tpfd_db = item.field;
            item.tpfd_data = item.type_data;
            data = JSON.parse(item.tpfd_data)
        }
        var fieldArray = {
            text : '<input  type="text"  name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input">',
            number :'<input  type="number" name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input">',
            radio : (sfdpPlug.checkboxes_clss(item.id,'radio','',item.field,'',item.tpfd_data,data.tpfd_check)).replace(/<a>(.+?)<\/a>/g,''),
            checkboxes : sfdpPlug.checkboxes_clss(item.id,'checkboxes','',item.field,'',item.tpfd_data,data.tpfd_check).replace(/<a>(.+?)<\/a>/g,''),
            textarea  : '<textarea  name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input"></textarea>',
            dropdown  : sfdpPlug.view_select(data, item.tpfd_db, 0 , '', item.id),
            upload: sfdpPlug.view_upload(data, item.field, 0),
            date : '<input  type="text"  placeholder="'+item.name+'起" data-format="yyyy-MM-dd" data-type="date" class="datetime sfdp-input layui-input"  name="@' + item.field + '[]"></div><div class="layui-input-inline" style="width:90px"><input  type="text"  placeholder="'+item.name+'止" data-format="yyyy-MM-dd" data-type="date" class="datetime sfdp-input layui-input"  name="@' + item.field + '[]">',
            group : ''
        }
        return fieldArray[type] || '';
    },
    common:function (type,data,att,Curd) {
        var name = data.tpfd_db,radios='',checkboxes='',view_selects='';
        var placeholder ='placeholder="'+(data.tpfd_moren || '')+'"';
        if(Curd==='add'){
            var value =data.tpfd_zanwei || '';
        }else{
            var value =(data.value || '');
        }
        if(type=='radio'){
            radios = sfdpPlug.checkboxes_clss(data.tpfd_id,'radio',att,name,value,data.tpfd_data,data.tpfd_check);
        }
        if(type=='checkboxes'){
            checkboxes = sfdpPlug.checkboxes_clss(data.tpfd_id,'checkboxes',att,name,value,data.tpfd_data,data.tpfd_check);
        }
        if(type=='dropdown'){
            view_selects = sfdpPlug.view_select(data.tpfd_data, name, value , att, data.tpfd_id);
        }
        var fieldArray = {
            text : '<input type="text" ' + att + ' value="' + value + '" name="' + name + '"  '+placeholder+' id="' + data.tpfd_id + '" class="' + sfdp.ui.input_input + '">',
            number :'<input type="number" ' + att + ' value="' + value + '" name="' + name + '"  '+placeholder+' id="' + data.tpfd_id + '" class="' + sfdp.ui.input_input + '">',
            radio : radios,
            checkboxes : checkboxes,
            textarea  : '<textarea id="' + data.tpfd_id + '" name="' + name + '" ' + att + ' class="' + sfdp.ui.input_textarea + '">' + value +'</textarea>',
            dropdown  : view_selects,
            upload: sfdpPlug.view_upload(data, name, 0),
            date : '<input type="text" ' + att + ' data-type="' + data.tpfd_dblx + '" data-format="' + dataType[data.xx_type] + '" class="datetime  layui-input"  name="' + name + '"  id="' + data.tpfd_id + '" value="' + value + '">',
            time_range : '<input type="text" ' + att + ' data-type="' + data.tpfd_dblx + '" data-format="' + dateType_format[data.xx_type] + '" class="time_range layui-input"  name="' + name + '"  id="' + data.tpfd_id + '" value="' + value + '">',
            html : '<div style="min-height: 38px;line-height: 38px;">'+ (data.value || data.tpfd_moren) + '</div>',
            wenzi : '<div style="min-height: 38px;line-height: 38px;">'+ (data.value || data.tpfd_moren) + '</div>',
            links : `<div style="min-height: 38px;line-height: 38px;"><a href="//${data.tpfd_moren}" target="${data.tpfd_zanwei}" class="layui-btn layui-btn-primary layui-border-blue">${data.tpfd_name}</a></div>`,
            system_user:'<input name="' + name + '"  type="hidden" readonly id="' + data.tpfd_id + '" value="' + value + '"><input  type="text" readonly id="' + data.tpfd_id + '_text" value="' + (data.text || '请选择') +'" style="width:85%;" class="layui-input"><a  href="javascript:" class="layui-btn  layui-btn-primary layui-border-blue" style="width:15%;" onclick=sfdp.openpage("用户组件","'+sfdp.url.user+'?id=' + data.tpfd_id + '&value=' + (data.value || '') + '",{w:"50%",h:"60%"})><i class="layui-icon layui-icon-friends"></i> 选择</a>',
            system_role:'<input name="' + name + '"  type="hidden" readonly id="' + data.tpfd_id + '" value="' + value + '"><input  type="text" readonly id="' + data.tpfd_id + '_text" value="' + (data.text || '请选择') +'" style="width:85%;" class="layui-input"><a  href="javascript:" class="layui-btn  layui-btn-primary layui-border-blue" style="" onclick=sfdp.openpage("用户组件","'+sfdp.url.role+'?id=' + data.tpfd_id + '&value=' + (data.value || '') + '",{w:"50%",h:"60%"})><i class="layui-icon layui-icon-group"></i> 选择</a>',
            upload_img:sfdpPlug.view_upload_img(data),
            edit:'<div id="div' + data.tpfd_id + '">' + value +'</div><textarea style="display:none" id="' + data.tpfd_id + '" name="' + name + '" ' + att + ' class="' + sfdp.ui.input_textarea + '">' + value +'</textarea><script></script><script type="text/javascript">' +
                '$.getScript("/static/lib/wangEditor.min.js",function(){var html = $(\'#' + data.tpfd_id + '\').val();const E = window.wangEditor; const editor = new E(\'#div' + data.tpfd_id + '\');editor.config.zIndex = 500;const $text1 = $(\'#' + data.tpfd_id + '\');  editor.config.onchange = function (html) {$text1.val(html)};editor.config.uploadImgShowBase64 = true;editor.create();$text1.val(editor.txt.html());}); </script>',
            group:'',
        }

        return fieldArray[type];
    },
    view_select: function (data, field, value, attr, selectId) {

        let tags = `<select name="${field}" ${attr} id="${selectId}" class="this-select" data-widget="select2"  data-value="${value}" lay-search>`
        tags += ` <option value="">请选择</option>`;
        for (const key in data) {
            if(data.hasOwnProperty(key)){
                tags += `<option value="${key}" ${value == data[key] ? 'selected' : ''}>${data[key]}</option>`;
            }
        }
        tags += `</select>`
        return tags
    },
    view_upload_img: function (data) {
        let html= '<input  type="hidden"  name="' + data.tpfd_db + '" style="width:85%;"  id="' + data.tpfd_id + '"><span id="' + data.tpfd_id + '_img"></span>  <a id="' + data.tpfd_id + '_btn" href="javascript:" class="layui-btn layui-btn-radius layui-btn-primary layui-border-blue" style="" onclick=sfdp.H5uploadhtml("' + data.tpfd_id + '",1)><i class="layui-icon layui-icon-upload-drag"></i> <span id="' + data.tpfd_id + '_text">选择图片</span></a>';
        return html;
    },
    view_upload: function (data) {
        var html = '<input type="text" style="width:85%;"class="layui-input" name="' + data.tpfd_db + '" readonly id="' + data.tpfd_id + '" value=' + (data.value || '') + '><span>' + '<a id="' + data.tpfd_id + '_btn" href="javascript:" class="layui-btn layui-btn-primary layui-border-blue" style="" onclick=sfdp.H5uploadhtml("' + data.tpfd_id + '")><i class="layui-icon layui-icon-upload-drag"></i> <span id="' + data.tpfd_id + '_text">上传</span></a></span>';
        if (data.tpfd_upload_type == 1) {
            var html = '<input type="text" style="width:85%;"class="layui-input" name="' + data.tpfd_db + '" readonly id="' + data.tpfd_id + '" value=' + (data.value || '') + '><span>' + '<a class="layui-btn layui-btn-primary layui-border-blue" style="" id="label_' + data.tpfd_id + '" onclick=sfdp.openpage("多文件上传","' + data.tpfd_upload_action + '?id=' + data.tpfd_id + '&value=' + (data.value || '') + '",{w:"50%",h:"60%"})><i class="layui-icon layui-icon-upload-drag"></i>上传</a></span>';
        }
        return html;
    },
    checkboxes_clss:function (id, type = 'checkbox', att,name,value,data,check) {
        if(type==='checkboxes'){
            type = 'checkbox';
        }
        var htmls = '',check='';
        if (value !== undefined ) {
            var strs = (value).split(",");
        }
        for (y in data) {
            check ='';
            if (value !== undefined  && sfdpPlug.isInArray(strs, data[y])) {
                check = 'checked';
            }
            htmls += '<input ' + att + ' ' + check + ' name="' + name + '[]" value=' + y + ' type="' + type + '" title="'+data[y]+'"><a>&nbsp &nbsp' + data[y] + '&nbsp &nbsp</a>';
        }
        return '<div style="height: 38px;line-height: 38px;" id="' + id + '">'+htmls+'</div>';
    },
    isInArray:function (arr, value) {
        for (var i = 0; i < arr.length; i++) {
            if (value === arr[i]) {
                return true;
            }
        }
        return false;
    },
    view_default: function (type) {
        var fieldArray = {
            text :'<label>文本控件：</label><input type="text"  placeholder="请输入信息~" >',
            upload :'<label>上传控件：</label>上传',
            checkboxes :'<label>多选控件：</label>选项1<input type="checkbox"  placeholder="" > 选项2<input type="checkbox"  placeholder="" >',
            radio :'<label>单选控件：</label>选项1<input type="radio"  placeholder="" > 选项2<input type="radio"  placeholder="" >',
            date :'<label>时间日期：</label><input type="text"  placeholder=""  >',
            dropdown :'<label>下拉选择：</label><select ><option value ="请选择">请选择</option></select>',
            textarea :'<label>多行控件：</label><textarea   ></textarea>',
            html :'<label>HTML控件：</label><b style="color: blue;">Look this is a HTML</b>',
            wenzi :'<label>文字控件：</label>默认现实的文本',
        }
        return fieldArray[type];
    },
    view_show: function (type,data,is_sub = false) {
        var html = '';
        if (typeof (data['tpfd_name']) == 'undefined') {
            return sfdpPlug.view_default(type, data);
        } else {
            var lab = '<div><label class="' + sfdp.ui.rows_label + '"><b>' + data.tpfd_name + '：</b></label><div class="' + sfdp.ui.rows_block + '">';
            if (is_sub) {
                lab = '';
            }
            if(type==='upload'){
                if (data.tpfd_upload_type == 1) {
                    html = lab + '<label id="label_' + data.tpfd_id + '" onclick=sfdp.openpage("多文件上传","' + data.tpfd_upload_action + '?act=view&id=' + data.tpfd_id + '&value=' + data.value + '",{w:"50%",h:"60%"})><i class="layui-icon layui-icon-download-circle" style="font-size: 30px; color: #1E9FFF;"></i>  </label>';
                }else{
                    html = lab + '<a target="_blank" href="/' + data.value + '" >' + sfdp.Ico(1) + '</a>';
                }
            }else if(type==='links'){
                html = lab + `<div style="min-height: 38px;line-height: 38px;"><a href="//${data.tpfd_moren}" target="${data.tpfd_zanwei}" class="layui-btn layui-btn-primary layui-border-blue">${data.tpfd_name}</a></div>`;
            }else if(type==='upload_img'){
                html = lab + `<div style="min-height: 38px;line-height: 38px;"><img src="/${data.value}" width="100px" onclick="window.open($(this).attr('src'))">'</div>`;
            }else if(type==='system_user' || type==='system_role'){
                html = lab + `<div style="min-height: 38px;line-height: 38px;">${data.text}</div>`;
            }else if(type==='group'){
                html = `<fieldset class="layui-elem-field layui-field-title" style="margin: 0px;"><legend>${data.tpfd_name}</legend></fieldset>`;
            }else{
                var data_value = sfdpPlug.htmlEncode(data.value);
                html = lab + `<div style="min-height: 38px;line-height: 38px;" id="${data.tpfd_id}" data-value="${data_value}">${data.value}</div>`;
            }
        }
        return html + '</div></div>';
    },
    /*1.用浏览器内部转换器实现html转码*/
    htmlEncode: function (html) {
        var temp = document.createElement("div");
        (temp.textContent != undefined ) ? (temp.textContent = html) : (temp.innerText = html);
        var output = temp.innerHTML;
        temp = null;
        return output;
    },
    /*2.用浏览器内部转换器实现html解码*/
    htmlDecode: function (text) {
        var temp = document.createElement("div");
        temp.innerHTML = text;
        var output = temp.innerText || temp.textContent;
        temp = null;
        return output;
    },
}
