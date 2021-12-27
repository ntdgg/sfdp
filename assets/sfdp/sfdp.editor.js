/**
 *
 * @type {string[]}
 */
var dataType = ['yyyy', 'MM-dd', 'yyyy-MM-dd', 'yyyyMMdd', 'yyyy-MM','yyyy-MM-dd HH:mm:ss'];
var dateType_format= ['date', 'datetime', 'time', 'year', 'month'];


var sfdpEditor = {
    PlugInit:function (type,data,is_sub= false,Curd='add') {
    },
    ToEditor:function (type) {
        var td_id = type + '_' + sfdp.dateFormat(new Date(), "yyyyMMddhhmmssS");
        var labid = ' data-type="' + type + '" data-id="' + td_id + '" id="' + td_id + '" class="set_att sfdp-input"';
        sfdp.dataSave({td_type: type, tpfd_id: td_id}, 'Editor', 'tr_data');
        return sfdpEditor.btn_to_input(labid, type);
    },
    btn_to_input: function (labid,type) {
            var html;
        switch (type) {
            case 'text':
                 html = `<input ${labid}  type="text"  placeholder="请输入信息~">`;
                break;
            case 'edit':
                 html = `<textarea ${labid} ></textarea>`;
                break;
            case 'textarea':
                 html = `<textarea  ${labid} ></textarea>`;
                break;
            case 'number':
                 html = `<input ${labid} type="number"  placeholder="数字框" >`;
                break;
            case 'date':
                 html = `<input  ${labid} type="text"  placeholder="请选择日期"  >`;
                break;
            case 'time_range':
                 html = `<input  ${labid} type="text"  placeholder="时间范围"  >`;
                break;
            case 'upload':
                html = `<input  ${labid} type="file"  placeholder="文件上传" >`;
                break;
            case 'upload_img':
                html = `<input  ${labid} type="file"  placeholder="单图上传" >`;
                break;
            case 'checkboxes':
                 html = `<sfdp>选项1<input type="checkbox" ${labid} style="height: 15px;width: 15px;"> 选项2<input type="checkbox" disabled></sfdp>`;
                break;
            case 'radio':
                 html = `<sfdp>选项1<input type="radio" ${labid} style="height: 15px;width: 15px;" > 选项2<input type="radio" disabled></sfdp>`;
                break;
            case 'dropdown':
                 html = `<select ${labid}><option value ="请选择">下拉组件</option></select>`;
                break;
            case 'dropdowns':
                 html = `<select ${labid}><option value ="请选择">多选下拉组件</option></select>`;
                break;
            case 'system_user':
                 html = `<select ${labid}  class="sfdp-input"><option value ="请选择">系统用户</option></select>`;
                break;
            case 'system_role':
                 html = `<select ${labid}  class="sfdp-input"><option value ="请选择">系统角色</option></select>`;
                break;
            default:
                 html = '';
        }
        return `${html}`;
    },
    convert_data_view: function (int_data) {
        var field = int_data.list.Editor.data;
        $.each(field,function(index,value){
            if(value['td_type']=='radio' || value['td_type']=='checkboxes'){
                $('#'+index).parents('sfdp').after(sfdpPlug.view_show(value['td_type'],value,true));
                $('#'+index).parents('sfdp').remove();
            }else{
                console.log(value);
               $('#'+index).after(sfdpPlug.view_show(value['td_type'],value,true));
               $('#'+index).remove();
            }
        });
    },
    convert_data_html: function (int_data,show_type = false) {
        var field = int_data.list.Editor.data;
        $.each(field,function(index,value){
            if(value['td_type']=='radio' || value['td_type']=='checkboxes'){
                if(show_type){
                    $('#'+index).parents('sfdp').after(sfdpPlug.PlugInit(value['td_type'],value,false,'edit'));
                     }else{
                    $('#'+index).parents('sfdp').after(sfdpPlug.PlugInit(value['td_type'],value));
                }
                $('#'+index).parents('sfdp').remove();
            }else{
                if(show_type){
                    $('#'+index).after(sfdpPlug.PlugInit(value['td_type'],value,false,'edit'));
                }else{
                    $('#'+index).after(sfdpPlug.PlugInit(value['td_type'],value));
                }
                $('#'+index).remove();
            }
        });
    },
    check_data_ids: function (ids){
        var json_data = JSON.parse(localStorage.getItem("json_data"));
        $.each(json_data.list.Editor.data,function(index,value){
            if($.inArray( index,ids)==-1){
                delete json_data.list.Editor.data[index];
            }
        });
        localStorage.setItem("json_data", JSON.stringify(json_data));
    },
    /*清空未使用的字段信息*/
    check_data_con: function (con){
        $('#sfdp_html2').append(con);
      $("#sfdp_html2 .set_att").each(function() {
          if($(this).attr("data-id")==''){
              $(this).remove();
          }
       });
        var new_html = $('#sfdp_html2').html();
        $('#sfdp_html2').html('');
        return new_html;
    }
}
