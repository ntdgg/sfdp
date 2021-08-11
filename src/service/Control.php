<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 核心驱动类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\service;

use sfdp\adaptive\Design;
use sfdp\adaptive\View;
use sfdp\adaptive\Script;
use sfdp\adaptive\Functions;
use sfdp\adaptive\Data;
use sfdp\adaptive\Common;
use sfdp\adaptive\Modue;
use sfdp\adaptive\Field;

use sfdp\fun\BuildFix;
use sfdp\fun\BuildFun;
use sfdp\fun\SfdpUnit;
use sfdp\fun\BuildTable;
use sfdp\fun\BuildStable;
use sfdp\lib\lib;

use sfdp\lib\unit;

use think\facade\Db;

class Control{
	/**
	 * API设计统一调用接口
	 *
	 * @param  str $act 调用方法
	 * @param  Array $sid post数据，或者sid值
	 */
	static function api($act,$sid=''){
		$urls= unit::gconfig('url');
		if($act =='list'){
			$list = Design::select();
			return lib::index($list);
		}
        if($act =='listData'){
            $list = Design::select();
            return $list;
        }
		if($act =='fun'){
			$list = Functions::select();
			return lib::fun($list);
		}
		if($act =='desc'){
			 $info = Design::find($sid);
			// if($info['s_type']==0){
				 return lib::desc($info['s_field'],$info['id'],$info['s_look']);
			// }else{
				 //return lib::desc2($info['s_field'],$info['id'],$info['s_look']);
			// }
		}
		if($act =='field'){
		    
        }
		if($act =='script'){
			if($sid !='' && is_array($sid)){
				$bill = Script::scriptSave($sid);
				BuildFun::Bfun($sid['function'],$bill);
				$action = $urls['api'].'?act=script&sid='.$sid['sid'];
				echo "<script language='javascript'>alert('Success,脚本生成成功！'); location.assign('".$action."');</script>";exit;
			}
			return lib::script(Script::script($sid),$sid);
		}
		if($act =='save'){
			return json(Design::saveDesc($sid,'save'));
		}
		if($act =='deldb'){
			 $json = Design::getDesignJson($sid);
			 //判断是否有附表
			if(isset($json['sublist']) && $json['sublist']!='' && is_array($json['sublist']) && count($json['sublist'])>0){
				$Stable = BuildStable::delDbbak($json['name_db'],$json['sublist']);
				if($Stable['code']==1){
					return json($Stable);
				}
			}
			 $ret = BuildTable::delDbbak($json['name_db']);
			 if($ret['code']==0){
				 Design::saveDesc(['s_db_bak'=>0,'id'=>$sid],'update');
			 }
			 return json($ret);
		}
		/*免部署生成*/
        if($act =='fix2'){
        	$ret = BuildFix::Bfix($sid);
        	if($ret['code']==-1){

            }
            return json($ret);
        }
		if($act =='fix'){
			$info = Design::find($sid);
			$json = Design::getDesignJson($sid);
			$ret =  BuildTable::hasDbbak($json['name_db']);
			if($ret['code']==1){
				 Design::saveDesc(['s_db_bak'=>1,'s_look'=>1,'id'=>$sid],'update');
				 return json($ret);
			 }
			//添加到版本库，并返回版本信息
			$varInfo = View::verAdd($sid,$info,$json);
			$all = json_decode($varInfo['all'],true);
			//版本信息写入模块数据库
			$modueId = Modue::add($varInfo,$json['tpfd_btn']);
			//版本字段写入字段数据库
			$fieldId = Field::add($varInfo['ver']['id'],$varInfo['db']);
			//创建数据表
			$BuildTable = BuildTable::Btable($json['name_db'],$varInfo['db'],$all['tpfd_btn'],$all['name']);
			if($BuildTable['code']==-1){
				return json($BuildTable);
			}
			//判断是否有附表
			if(isset($json['sublist']) && $json['sublist']!='' && is_array($json['sublist']) && count($json['sublist'])>0){
				$Stable = BuildStable::Btable($json['name_db'],$json['sublist']);
				if($Stable['code']==-1){
					return json($Stable);
				}
			}
			//更新设计表
			Design::saveDesc(['s_db_bak'=>1,'s_look'=>1,'s_design'=>2,'id'=>$sid],'update');
			
			return json(['code'=>0]);
		}
		if($act=='fun_save'){
			return Functions::save($sid);
		}
		if($act=='fun_update'){
			$ret =  Functions::update($sid);
			if($ret){
				return json(['code'=>0,'msg'=>'操作成功！']);
			}else{
				return json(['code'=>-1,'msg'=>'更新出错']);
			}
		}
		if($act=='create'){
			$id = Design::saveDesc($sid,'create');
			return json(['code'=>0]);
		}
		if($act=='node'){
			$className = unit::gconfig('node_action');
			if(!class_exists($className)){
				return 'Sorry,未找到node_action类，请先配置~';
			}
			$ver = Design::findVerWhere([['status','=',1],['sid','=',$sid['sid']]]);//取得版本ID
			$Node = (new $className())->SaveNode($sid['sid'],Design::descVerTodata($ver['id']),$sid['node']);//获取目录节点信息
			if($Node['code']==0){
				return json(['code'=>0]);
			}else{
				return json(['code'=>1,'msg'=>$Node['msg']]);
			}
		}
		if($act =='btable'){
			$ret = Design::insertBtable($sid);
			if($ret){
				return json(['code'=>0,'msg'=>'保存成功']);
			}else{
				return json(['code'=>1,'msg'=>'保存失败']);
			}
		}
		if($act =='customSave'){
			$field = implode(',',$sid['field']);
			$field_name = implode(',',$sid['name']);
			$json = View::ver($sid['sid']);
			$modue = Modue::saveWhere([['sid','=',$json['ver']['id']]],['field'=>$field,'field_name'=>$field_name,'update_time'=>time()]);
			$field = Field::saveWhere([['sid','=',$json['ver']['id']]],['is_list'=>0,'update_time'=>time()]);
			$field = Field::saveWhere([['id','in',$sid['data']]],['is_list'=>1,'update_time'=>time()]);
			if($field){
				return json(['code'=>0,'msg'=>'保存成功']);
			}else{
				return json(['code'=>1,'msg'=>'保存失败']);
			}
		}
		if($act =='custom'){
			$info = Design::find($sid);
			if($info['s_design']<>2){
				echo "<script language='javascript'>alert('Err,请先设计并部署！！'); </script>";
				exit;
			}
			$json = View::ver($sid);
			$field = Field::select([['sid','=',$json['ver']['id']]]);;//找出模型字段
			$modue = Modue::findWhere([['sid','=',$json['ver']['id']]]);//找出模型字段
			$fielselect = explode(",",$modue['field']);
			$list = '';
			foreach($fielselect as $k=>$v){
				
				$find = Field::findWhere([['sid','=',$json['ver']['id']],['field','=',$v]]);//找出模型字段
				if($find){
					$list  .= '<li class="ui-state-highlight " data-name="'.$find['name'].'" data-id="'.$find['id'].'" data-field="'.$find['field'].'">'.$find['name'].'('.$find['field'].')</li>'; 
				}
			}
			$listtrue ='';
			foreach($field as $k=>$v){
				if(!in_array($v['field'], ['id','status','update_time','create_time','uid'])){
					if($v['is_list']<>1){
						  $listtrue  .= '<li class="ui-state-highlight " data-name="'.$v['name'].'" data-id="'.$v['id'].'" data-field="'.$v['field'].'">'.$v['name'].'('.$v['field'].')</li>'; 
					  }
				}
			}
			return lib::custom($sid,$list,$listtrue,$field,$modue);
		}
		if($act =='customOrder'){
			$json = View::ver($sid['sid']);
			$ret =Modue::saveWhere([['sid','=',$json['ver']['id']]],['order'=>$sid['order'],'update_time'=>time()]);
			if($ret){
				return json(['code'=>0,'msg'=>'保存成功']);
			}else{
				return json(['code'=>1,'msg'=>'保存失败']);
			}
		}
        if($act =='customShow'){
            $json = View::ver($sid['sid']);
            $ret =Modue::saveWhere([['sid','=',$json['ver']['id']]],['show_field'=>$sid['show_field'],'show_fun'=>$sid['show_fun'],'show_type'=>$sid['show_type'],'update_time'=>time()]);
            if($ret){
                return json(['code'=>0,'msg'=>'保存成功']);
            }else{
                return json(['code'=>1,'msg'=>'保存失败']);
            }
        }
		if($act =='customSearch'){
			$ids = explode(",",$sid['ids_val']);
			$value = explode(",",$sid['value_val']);
			$json = View::ver($sid['sid']);
			Field::saveWhere([['sid','=',$json['ver']['id']]],['is_search'=>0,'search_type'=>'','update_time'=>time()]);
			foreach($ids as $k=>$v){
				if($v <> ''){
					$ret =Field::saveWhere([['id','=',$v]],['is_search'=>1,'search_type'=>$value[$k],'update_time'=>time()]);
				}
			}
			if($ret){
				return json(['code'=>0,'msg'=>'保存成功']);
			}else{
				return json(['code'=>1,'msg'=>'保存失败']);
			}
		}
		if($act =='customAccess'){
			$ids = explode(",",$sid['ids_val']);//字段
			$value = explode(",",$sid['value_val']);//表达式
			$user = explode(",",$sid['user_val']);//用户值
            $fun = explode(",",$sid['fun_val']);//用户值
			$json = View::ver($sid['sid']);
			$access = [];
			foreach($ids as $k=>$v){		
				if($v <> '' && $value[$k] <> '' && $user[$k] <> ''){
					$name =Field::value($v);
					$access[] = [$v,$value[$k],$user[$k],$name,$fun[$k]];
				}
			}
			$ret = Modue::saveWhere([['sid','=',$json['ver']['id']]],['access'=>json_encode($access),'update_time'=>time()]);
			if($ret){
				return json(['code'=>0,'msg'=>'保存成功']);
			}else{
				return json(['code'=>1,'msg'=>'保存失败']);
			}
		}
        return $act.'参数出错';
	}
	/**
	 * 按钮数据转换
	 *
	 * @param  Array $btnArray 按钮数组
	 * @param  init $sid sid值
	 */
	static function btnArray($btnArray,$sid){
       $btns ='';
	    if((in_array('add',$btnArray))){
			$url = url('index/sfdp/sfdpCurd',['act'=>'add','sid'=>$sid]);
			$btns .= '<a class="btn btn-primary radius" onclick=layer_show("新增","'.$url.'","850","500") ><i class="Hui-iconfont-add Hui-iconfont"></i> 新增</a> ';
		}
	   if((in_array('Edit',$btnArray))){
			$btns .= '<a onClick="edit('.$sid.')" class="btn btn-success radius"><i class="Hui-iconfont Hui-iconfont-edit"></i>修改</a> ';
		}
	   if(in_array('Del',$btnArray)){
			$btns .= ' <a onClick="del('.$sid.')"	class="btn btn-danger radius"><i class="Hui-iconfont Hui-iconfont-del3"></i>删除</a> ';
		}
	   return $btns;
    }
	/**
	 * API数据的CURD
	 *
	 * @param  str $act 调用方法
	 * @param  Array $sid sid值
	 */
	static function curd($act,$sid,$data='',$g_js='',$bid=''){
	    //使用sid找出当前的版本sid_ver; 2021年5月3日22:58:33
        $sid_ver = Design::findVerWhere([['status','=',1],['sid','=',$sid]]);
        if(!$sid_ver){
            echo '未发现发行版本';exit;
        }
        $sid = $sid_ver['id'];
		if($act =='index'){
			$modueId = Modue::findWhere([['sid','=',$sid]]);//找出模型
			$fieldId = Field::select([['sid','=',$sid]]);//找出模型字段
			$search = [];
			foreach($fieldId as $k=>$v){
				if($v['is_search']==1){
					if($v['type_lx']==1 && $v['function'] !=''){
                        $fun_mode = unit::gconfig('fun_mode') ?? 1;
                        if($fun_mode==1 || $fun_mode==''){
                            $getFun = Functions::findWhere([['fun_name','=',$v['function']]]);
                            if(!$getFun){
                                echo '<h2>系统级别错误('.$v2['function'].')：函数名无法找到~</h2>';exit;
                            }
                            $getData = Common::query($getFun['function']);
                        }else{
                            $className = unit::gconfig('fun_namespace');
                            if(!class_exists($className)){
                                return 'Sorry,未找到自定函数，请先配置~';
                            }
                            $getData = (new $className())->func($v['function'],'all');
                        }
						if($getData['code']==-1){
							echo '<h2>系统级错误：'.$getData['msg'].'</h2>';exit;
						}else{
							$sd = [];
							foreach($getData['msg'] as $k3=>$v3){
								$sd[$v3['id']] = $v3['name'];
							}
							$v['type_data'] = json_encode($sd);
						}
					}
					$search[] = $v;
				}
			}
			$map = SfdpUnit::Bsearch($data,$sid);
			$list = Data::getListData($sid,$map);
			$field_name = explode(',',$modueId['field_name']);
			$config = [
				'g_js'=>$g_js,
				'sid' =>$sid,
				'field'=>$field_name,
				'search' =>json_encode($search),
                'fun' =>$list['field']['fun'],
				'title' =>$modueId['title'],
				'load_file' =>$list['field']['load_file'],
				'show_field' =>$modueId['show_field'],
                'show_type' =>$modueId['show_type'],
                'show_fun' =>$modueId['show_fun']
			];
			$btns = explode(',',$modueId['btn']);
			$btns_tohtml = self::btnArray($btns,$sid);
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/index.html',['btn'=>$btns_tohtml,'config'=>$config,'list'=>$list['list']]);
				}else{
				return ['config'=>$config,'list'=>$list['list'],'btn'=>$btns];
			}
		}
		if($act =='GetData'){
			$map = SfdpUnit::Bsearch($data,$sid);
			$list = Data::getListData($sid,$map,$data['page'],$data['limit']);
			$jsondata = [];
			$btnArray = $list['field']['btn'];
			$tablename = $list['field']['db_name'];
			$stv = [
				-1=>'<span class="label label-danger radius" >退回修改</span>',0=>'<span class="label radius">保存中</span>',1=>'<span class="label radius" >流程中</span>',2=>'<span class="label label-success radius" >审核通过</span>'
			];
			foreach($list['list'] as $k=>$v){		
				//$list['list'][$k]['id'] = '<input type="checkbox" value="'.$v['id'].'/'.$v['status'].'/'.$tablename.'" name="ids">';
				//$list['list'][$k]['status'] =   $stv[$v['status']] ?? 'ERR';
				$list['list'][$k]['url'] = '<a onClick=sfdp.openfullpage("查看","'.url('/index/sfdp/sfdpCurd',['act'=>'view','sid'=>$sid,'bid'=>$v['id']]).'")	class="btn  radius size-S">查看</a>';
				$jsondata[$k] = array_values($list['list'][$k]);
			}
			if(unit::gconfig('return_mode')==1){
				return json(['data'=>$jsondata,'recordsFiltered'=>$list['count'],'recordsTotal'=>$list['count']]);
				}else{
				return ['data'=>$jsondata,'list'=>$list,'count'=>$list['count']];
			}
		}
		if($act=='view'){
			$info = Data::getViewData($sid,$data);
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/view.html',['info'=>$info['info'],'row'=>$info['row']]);
				}else{
				return ['info'=>$info['info'],'row'=>$info['row']];
			}
		}
		if($act =='edit'){
			if($data !=''){
				Data::edit($sid,$data,$bid);
				return json(['code'=>0,'msg'=>'保存成功']);
			}
			$data = Data::getEditData($sid,$bid);
			$viewdata = $data['data'];
			$config = [
				'g_js'=>$g_js,
				'fun' =>$viewdata['fun'],
				'load_file' =>$viewdata['load_file'],
				'upload_file'=>unit::gconfig('upload_file')
			];
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/edit.html',['showtype'=>'edit','config'=>$config,'data'=>$data['info']]);
				}else{
				return ['config'=>$config,'data'=>$data['info']];
			}
		}
		if($act =='del'){	
			Data::del($sid,$bid);
			return json(['code'=>0]);	
		}
		if($act =='add'){
			if($data !=''){
				$ret = Data::add($sid,$data);
				if($ret){
					return json(['code'=>0,'msg'=>'保存成功']);
				}else{
					return json(['code'=>1,'msg'=>'系统出错']);
				}
			}
			$data = Design::getAddData($sid);
			$config = [
				'g_js'=>$g_js,
				'fun' =>$data['fun'],
				'load_file' =>$data['load_file'],
				'upload_file'=>unit::gconfig('upload_file')
			];
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/edit.html',['showtype'=>'add','config'=>$config,'data'=>$data['info']['s_field']]);
				}else{
				return ['config'=>$config,'data'=>$data['info']['s_field']];
			}
		}
	}
	static function fapi($post){
		$key_name = [];
		$key_val = [];
		foreach($post as $k=>$v){
			if($k<>'fun'){
				$key_name[] = '@'.$k;
				$key_val[] = $v;
			}
		}
		$sql = Functions::findWhere([['fun_name','=',$post['fun']]]);;
		$new_sql=str_replace($key_name,$key_val,$sql['function']);
		return Common::query($new_sql);
	}
}