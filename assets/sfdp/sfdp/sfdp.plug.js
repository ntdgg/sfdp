/**
 *
 * @type {string[]}
 */

var dataType = ['yyyy', 'MM-dd', 'yyyy-MM-dd', 'yyyyMMdd', 'yyyy-MM','yyyy-MM-dd HH:mm:ss'];

var sfdpPlug = {
    PlugInit:function (type,data,is_sub= false,Curd='add') {
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
            text : '<input type="text"  name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input">',
            number :'<input type="number" name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input">',
            radio : (sfdpPlug.checkboxes_clss(item.id,'radio','',item.field,'',item.tpfd_data,data.tpfd_check)).replace(/<a>(.+?)<\/a>/g,''),
            checkboxes : sfdpPlug.checkboxes_clss(item.id,'checkboxes','',item.field,'',item.tpfd_data,data.tpfd_check).replace(/<a>(.+?)<\/a>/g,''),
            textarea  : '<textarea  name="' + item.field + '"  placeholder="'+item.name+'" class="layui-input"></textarea>',
            dropdown  : sfdpPlug.view_select(data, item.tpfd_db, 0 , '', item.id),
            upload: sfdpPlug.view_upload(data, item.field, 0),
            date : '<input type="text"  data-type="' + dataType[data.xx_type] + '" class="datetime sfdp-input layui-input"  name="' + item.field + '">'
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
            textarea  : '<textarea id="' + data.tpfd_id + '" name="' + name + '" ' + att + ' placeholder="" class="' + sfdp.ui.input_textarea + '">' + value +'</textarea>',
            dropdown  : view_selects,
            upload: sfdpPlug.view_upload(data, name, 0),
            date : '<input type="text" ' + att + ' data-type="' + dataType[data.xx_type] + '" class="datetime sfdp-input"  name="' + name + '"  id="' + data.tpfd_id + '" value="' + value + '">',
            html : '<div style="height: 38px;line-height: 38px;">'+ (data.value || data.tpfd_moren) + '</div>',
            wenzi : '<div style="height: 38px;line-height: 38px;">'+ (data.value || data.tpfd_moren) + '</div>'
        }
        return fieldArray[type];
    },
    view_select: function (data, field, value, attr, selectId) {

        let tags = `<select name="${field}" ${attr} id="${selectId}" data-value="${value}">`
        tags += ` <option value="">请选择</option>`;
        for (const key in data) {
            if(data.hasOwnProperty(key)){
                tags += `<option value="${key}" ${value == data[key] ? 'selected' : ''}>${data[key]}</option>`;
            }
        }
        tags += `</select>`

        return tags
    },
    view_upload: function (data) {
        if (data.tpfd_upload_api === 0) {
            var html = '<input type="text" name="' + data.tpfd_db + '" readonly id="' + data.tpfd_id + '" value=' + (data.value || '') + '><span id="drag" style="width:80px;margin-left:5px">' + '<label onclick=sfdp.H5uploadhtml("' + data.tpfd_id + '")>' + sfdp.Ico(2) + '</label></span>';
        } else {
            if (data.value === undefined) {
                data.value = '';
            }
            if (data.tpfd_upload_type === 1) {
                var html = '<input type="text" name="' + data.tpfd_db + '" readonly id="' + data.tpfd_id + '" value=' + data.value + '><span id="drag" style="width:80px;margin-left:5px">' + '<label id="label_' + data.tpfd_id + '" onclick=sfdp.openpage("多文件上传","' + data.tpfd_upload_action + '?id=' + data.tpfd_id + '&value=' + data.value + '",{w:"50%",h:"60%"})>附件</label></span>';
            } else {
                var html = '<input type="text" name="' + data.tpfd_db + '" readonly id="' + data.tpfd_id + '" value=' + data.value + '><span id="drag" style="width:80px;margin-left:5px">' + '<label id="label_' + data.tpfd_id + '" onclick=sfdp.openpage("文件上传","' + data.tpfd_upload_action + '?id=' + data.tpfd_id + '&value=' + data.value + '",{w:"48%",h:"50%"})>附件</label></span>';
            }
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
                if (data.tpfd_upload_api == 1) {
                     html = lab + '<span id="drag" style="width:80px;margin-left:5px">' + '<label id="label_' + data.tpfd_id + '" onclick=sfdp.openpage("多文件上传","' + data.tpfd_upload_action + '?act=view&id=' + data.tpfd_id + '&value=' + data.value + '",{w:"50%",h:"60%"})><i class="layui-icon layui-icon-download-circle" style="font-size: 30px; color: #1E9FFF;"></i>  </label></span>';
                } else {
                     html = lab + '<a target="_blank" href="/' + data.value + '" >' + sfdp.Ico(1) + '</a>';
                }
            }else{
                    var data_value = sfdpPlug.htmlEncode(data.value);
                    html = lab + `<div style="height: 38px;line-height: 38px;" id="${data.tpfd_id}" data-value="${data_value}">${data.value}</div>`;
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
