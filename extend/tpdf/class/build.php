<?php
/**
*+------------------
* 工作流任务服务
*+------------------ 
*/

namespace tpdf;

class Build{
	/**
	  * input 转换
	  */
	public function convertInput($form)
	{
		$colspanArr = ['small'=>'','medium'=>'colspan=3','large'=>'colspan=5'];
		$colspan = $colspanArr[$form['option']["size"]];
		$editField='';
		$script_edit = '';
		switch ($form['type']) {
			case "radio":
			case "checkbox":
				$editField .= '<td>'.$form['title'].'</td>'."\n" . tab(3).'<td '.$colspan.'><div class="skin-minimal">';
			if ($form['type'] == "radio") {
					foreach($form['option']["options"] as $v){
						$editField .= '<div class="radio-box" ><input type="radio"   name="'.$form['name'].'"   value='.$v['label'].'><label for="radio-0">'.$v['label'].'</label></div>';
					}
					
				}else{
					foreach($form['option']["options"] as $v){
						$editField .= '<div class="radio-box" ><input type="checkbox"   name="'.$form['name'].'"   value='.$v['label'].'><label for="radio-0">'.$v['label'].'</label></div>';
					}
				}
				$editField .= '</div></td>';
				break;
			case "select":
				$editField .= '<td>'.$form['title'].'</td>'."\n" . tab(3).'<td '.$colspan.'><span class="select-box"><select name="'.$form['name'].'"  class="select"  datatype="*">';
					foreach($form['option']["options"] as $v){
						$editField .= '<option value="'.$v['label'].'" >'.$v['label'].'</option>	';
					}
				$editField .= '</select></td>';
				break;
				
			case "upload":
				$editField .= '<td width="10%">'.$form['title'].'</td>'."\n" . tab(3).'<td width="23%" '.$colspan.'><input type="text" class="dfinput" id="'.$form['name'].'" value="{$vo.' . $form['name'] . '}" name="'.$form['name'].'" datatype="*"><a class="btn btn-primary radius" id=\'bupload\' onclick=layer_show("上传","/index.php/admin/Fromdesc/upload?id='.$form['name'].'","140","160")>附件</a> </td>';
				break;
			case "Ue":
				$editField .= '<td width="10%">'.$form['title'].'</td>'."\n" . tab(3).'<td width="23%" '.$colspan.'><script id="container" name="content" type="text/plain">{$vo.' . $form['name'] . '}</script></td>';
				$script_edit .= "\n var finish = UE.getEditor('container', {
                toolbars: [[
                    'undo', //撤销
                    'redo', //重做
                    '|',
                    'bold', //加粗
                    'italic', //斜体
                    'underline', //下划线
                    'strikethrough', //删除线 
                ]],
                allHtmlEnabled:true,
                initialFrameWidth: 600,
                initialFrameHeight: 200,
                elementPathEnabled:false,   // 是否启用元素路径，默认是显示
            });\n";
			break;
				
			case "date":
				$editField .= '<td>'.$form['title'].'</td>'."\n" . tab(3).'<td '.$colspan.'><input type="text" class="input-text date" value="{$info.' . $form['name'] . ' ?? \'\'}" name="'.$form['name'].'" datatype="*"></td>';
				$script_edit .= "laydate.render({elem: '.date'});";
				break;
			case "text":
			case "password":
			case "number":
			default:
				$editField .= '<td>'.$form['title'].'</td>'."\n" . tab(3).'<td '.$colspan.'><input type="' .$form['type'] . '" class="input-text" '
				. 'placeholder="' . $form['title'] . '" name="' . $form['name']. '" '
				. 'value="' . '{$vo.' . $form['name'] . ' ?? \'' . $form['default'] . '\'}' . '">' . "\n </td>";
			break;
		}
		
		$js = ['small'=>1,'medium'=>2,'large'=>3];
	
		return ['Field'=>$editField,'script_edit'=>$script_edit,'num'=>$js[$form['option']["size"]]];
		
	}
	public function bulidTd($ii)
	{
		$num = 3-$ii;
		$editField='';
		for ($x=0; $x<$num; $x++) {
		  $editField .= '<td></td>'."\n" . tab(3).'<td></td>';
		} 
		return $editField.'</tr>';
		
	}
}