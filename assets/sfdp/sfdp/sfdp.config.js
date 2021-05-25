var sfdp = (function($){
    $.ui = {
        css : '/static/layui/css/layui.css',
        ui_css :'<style>select{width: 100%} .layui-form-label{box-sizing: initial;}</style>',
        ui_js :'',
        form :'layui-form',
        btn_ok:'layui-btn',//一行的样式
        table :'layui-table',
        field :'layui-elem-field layui-field-title',
        btn_close:'layui-btn layui-btn-primary',//一行的样式
        rows:'layui-form-item',//一行的样式
        rows_xs:' layui-col-sm',//栅格布局样式
        rows_label:'layui-form-label',//表单的lab
        rows_block:'layui-input-block',//输入区域CSS
        input_input:'layui-input',//输入框
        input_textarea:'layui-textarea', //多行文本
        input_select:''
    }
    return $;
})(window.sfdp||{});

