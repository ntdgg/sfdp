{include file='pub/base' /}
<div class="page-container">
		{present name="vo.id"}
			<form action="{:url('edit')}" method="post" name="form" id="form">
			<input type="hidden" name="id" value="{$vo.id}">
			<input type="hidden" name="status" value="0">
		{else /}
			<form action="{:url('add')}" method="post" name="form" id="form">
		{/present}
		[ROWS]
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                <button type="submit" class="btn btn-primary radius">&nbsp;&nbsp;提交&nbsp;&nbsp;</button>
                <button type="button" class="btn btn-default radius ml-20" onClick="layer_close();">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="__LIB__/Validform/5.3.2/Validform.min.js"></script>
<script>
    $(function () {
[SET_VALUE]

        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '20%'
        });

        $("#form").Validform({
            tiptype: 2,
            ajaxPost: true,
            showAllError: true,
            callback: function (ret){
                ajax_progress(ret);
            }
        });

    })
[SCRIPT]
</script>