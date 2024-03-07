<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V7.0
  *+------------------
  * Sfdp 核心驱动类
  *+------------------
  * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
  *+------------------
  */
namespace sfdp\service;

use sfdp\adaptive\Design;
use sfdp\adaptive\Userconfig;
use sfdp\adaptive\View;
use sfdp\adaptive\Script;
use sfdp\adaptive\Functions;
use sfdp\adaptive\Data;
use sfdp\adaptive\Common;
use sfdp\adaptive\Modue;
use sfdp\adaptive\Field;
use sfdp\adaptive\M;

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
	static function api($act,$sid='',$map=null){
		$urls= unit::gconfig('url');
		if($act =='list'){
			$list = Design::select();
			return lib::index($list);
		}
        if($act =='listData'){
            return Design::select($map);
        }
		if($act =='fun'){
			$list = Functions::select();
			return lib::fun($list);
		}
        if($act =='field'){
            $sid_ver = Design::findVerWhere([['status','=',1],['sid','=',$sid]]);
            if(!$sid_ver){
                echo '<style type="text/css"> a{color:#2E5CD5;cursor: pointer;text-decoration: none} 
	body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;padding: 50px;} 
	h1{ font-size: 50px; font-weight: normal; margin-bottom: 12px; } 
	p{ line-height: 1.2em; font-size: 36px }
	li {font-size: x-large;margin: 10px;}
	</style><h2><h1>d(╯﹏╰)b</h1>您好，部署后方可进行使用！</h2>';exit;
            }
            return Field::select(['sid'=>$sid_ver['id']]);
        }
		if($act =='desc'){
			 $info = Design::find($sid);
             $list = Design::select([['s_design','=','2']]);
			 if($info['s_type']==0 || $info['s_type']==2){
				 return lib::desc($info['s_field'],$info['id'],$info['s_look'],$list);
			 }
            if($info['s_type']==1){
				 return lib::desc2($info['s_field'],$info['id'],$info['s_look'],$list);
			 }
            if($info['s_type']==3){
                return lib::desc3($info['s_field'],$info['id'],$info['s_look'],$list);
            }
		}
		if($act =='center'){
            return lib::center($sid);
        }
        if($act =='mysql'){
            if($sid !='' && is_array($sid)){
                $bill = Script::scriptSave($sid);
                BuildFun::Bfun($sid['function'],$bill);
                unit::errJSMsg('Success,脚本生成成功！',$urls['api'].'?act=script&sid='.$sid['sid']);
            }
            return lib::mysql();
        }
		if($act =='script'){
			if($sid !='' && is_array($sid)){
				$bill = Script::scriptSave($sid);
				BuildFun::Bfun($sid['function'],$bill);
                unit::errJSMsg('Success,脚本生成成功！',$urls['api'].'?act=script&sid='.$sid['sid']);
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
            $db_field_array=[];
            foreach($json['list'] as $k=>$v){
                foreach($v['data'] as $v2){
                    if(!isset($v2['tpfd_db'])){
                        return json(['code'=>1,'msg'=>unit::errMsg(3001)]);
                    }
                    $db_field_array[] = $v2['tpfd_db'];
                    if(isset($v2['xx_type']) && $v2['xx_type']==1 && $v2['td_type']!='time_range' && $v2['td_type']!='date'){
                        $ret = Data::getFun($v2['checkboxes_func']);;
                        if(isset($ret['code'])){
                            return json($ret);
                        }
                    }
                }
            }
            if (count($db_field_array) != count(array_unique($db_field_array))) {
                return json(['code'=>1,'msg'=>unit::errMsg(3002)]);
            }
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
			$BuildTable = BuildTable::Btable($json['name_db'],$varInfo['db'],$all['tpfd_btn'],$all['name'],$all);
			if($BuildTable['code']==-1){
				return json($BuildTable);
			}
			//判断是否有附表
			if(isset($json['sublist']) && $json['sublist']!='' && is_array($json['sublist']) && count($json['sublist'])>0){
                $fieldId2 = Field::sadd($varInfo['ver']['id'],$varInfo['db2']);//子表加入版本库
				$Stable = BuildStable::Btable($json['name_db'],$json['sublist'],$all);
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
			if(!$ver){
				return json(['code'=>1,'msg'=>'请检查是否已经部署项目！']);
			}
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
		if($act =='custom'){
			$info = Design::find($sid);
			if($info['s_design']<>2){
                unit::errJSMsg('Err,校验错误,请先设计并部署！');
			}
			$json = View::ver($sid);
			$field = Field::select([['sid','=',$json['ver']['id']],['table_type','=',0]],'');;//找出模型字段
			$modue = Modue::findWhere([['sid','=',$json['ver']['id']]]);//找出模型字段
			return lib::custom($sid,$field,$modue);
		}
        if($act =='fieldkey'){
            $info = Design::find($sid);
            if($info['s_design']<>2){
                unit::errJSMsg('Err,校验错误,请先设计并部署！');
            }
            $json = View::ver($sid);
            $field = Field::select([['sid','=',$json['ver']['id']],['table_type','=',0]]);;//找出模型字段
            return lib::fieldkey($sid,$field);
        }
        if($act =='fieldkeyBuild'){
            return json(Field::fieldBuildIndex($sid));
        }
        if($act=='fieldkeyDel'){
            return json(Field::fieldDelIndex($sid));
        }
		if($act =='customOrder'){
			$json = View::ver($sid['sid']);
			$ret =Modue::saveWhere([['sid','=',$json['ver']['id']]],['order'=>$sid['order'],'height'=>$sid['height'],'update_time'=>time()]);
			if($ret){
				return json(['code'=>0,'msg'=>'保存成功']);
			}else{
				return json(['code'=>1,'msg'=>'保存失败']);
			}
		}
        if($act =='customSave'){
            $json = View::ver($sid['sid']);
            unset($sid['sid']);
            $count = [];
            $list = [];
            $list_name = [];
            Field::saveWhere([['sid','=',$json['ver']['id']]],['is_list'=>0,'is_search'=>0,'search_type'=>'','update_time'=>time()]);//设置所有为未保存状态
            foreach($sid as $k=>$v){
                if(isset($v['count']) && $v['count']=='1'){
                    $count[] = $k;
                }
                if(isset($v['list']) && $v['list']<>''){
                    $list[$v['list']] = $k;
                    $list_name[$v['list']] = $v['title'];
                }
                if(isset($v['search']) && $v['search'] <> ''){
                    Field::saveWhere([['id','=',$v['id']]],['is_search'=>1,'search_type'=>$v['search'],'update_time'=>time()]);//更新查询条件
                }
                Field::saveWhere([['id','=',$v['id']]],['width'=>$v['width'],'field_length'=>$v['field_length'],'field_wz'=>$v['field_wz'],'update_time'=>time()]);//更新宽度
            }
            ksort($list);ksort($list_name);
            $list_field = implode(',',$list);
            $field_name = implode(',',$list_name);
            if($list_field !=''){
                $modue = Modue::saveWhere([['sid','=',$json['ver']['id']]],['field'=>$list_field,'field_name'=>$field_name,'update_time'=>time()]);
                $field = Field::saveWhere([['field','in',$list],['sid','=',$json['ver']['id']]],['is_list'=>1,'update_time'=>time()]);
            }
            $count_field = implode(',',$count);
            if($count_field !=''){
                $ret =Modue::saveWhere([['sid','=',$json['ver']['id']]],['count_field'=>$count_field,'update_time'=>time()]);
            }else{
                $ret =Modue::saveWhere([['sid','=',$json['ver']['id']]],['count_field'=>'','update_time'=>time()]);
            }
            return json(['code'=>0,'msg'=>'保存成功']);
        }
        if($act =='customShow'){
            $json = View::ver($sid['sid']);
            if($sid['show_type']==1||$sid['show_type']==2){
                if($sid['show_fun']!='sys_role'){
                    $ret = Data::getFun($sid['show_fun']);;
                    if((isset($ret['errCode']) || (isset($ret['code']) && $ret['errCode']!=3004))){
                        return $ret;
                    }
                }
            }
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
        if($act =='customData'){
            $ids = explode(",",$sid['ids_val']);//字段
            $value = explode(",",$sid['value_val']);//表达式
            $user = explode(",",$sid['user_val']);//用户值
            $json = View::ver($sid['sid']);
            $access = [];
            foreach($ids as $k=>$v){
                if($v <> '' && $value[$k] <> '' && $user[$k] <> ''){
                    $access[] = [$v,$value[$k],$user[$k]];
                }
            }
            $ret = Modue::saveWhere([['sid','=',$json['ver']['id']]],['linkdata'=>json_encode($access),'update_time'=>time()]);
            if($ret){
                return json(['code'=>0,'msg'=>'保存成功']);
            }else{
                return json(['code'=>1,'msg'=>'保存失败']);
            }
        }

        if($act =='customUserConfig'){
            $ids = explode(",",$sid['ids']);//字段
            $json = View::ver($sid['sid']);
            $access = [];
            foreach($ids as $k=>$v){
                $name =Field::findWhere([['sid','=',$json['ver']['id']],['field','=',$v]]);
                $access[] = $name['name'];
            }
            $ret = Userconfig::add(['sid'=>$sid['sid'],'field'=>$sid['ids'],'field_name'=>implode(',',$access)]);
            if($ret){
                return json(['code'=>0,'msg'=>'保存成功']);
            }else{
                return json(['code'=>1,'msg'=>'保存失败']);
            }
        }
        if($act =='scan'){
            return lib::scan($sid);
        }
        if($act =='sign'){
            return lib::sign($sid);
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
			$btns .= '<a class="btn btn-primary radius" onclick=layer_show("新增","'.$url.'","85","50") ><i class="Hui-iconfont-add Hui-iconfont"></i> 新增</a> ';
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
	static function curd($act,$sid,$data='',$bid=''){
        $old_sid = $sid;
	    //使用sid找出当前的版本sid_ver; 2021年5月3日22:58:33
        $sid_ver = Design::findVerWhere([['status','=',1],['sid','=',$sid]]);
        if(!$sid_ver){
            echo '<style type="text/css"> a{color:#2E5CD5;cursor: pointer;text-decoration: none} 
	body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;padding: 50px;} 
	h1{ font-size: 50px; font-weight: normal; margin-bottom: 12px; } 
	p{ line-height: 1.2em; font-size: 36px }
	li {font-size: x-large;margin: 10px;}
	</style><h2><h1>d(╯﹏╰)b</h1>您好，请前往业务平台进行业务开发，部署后方可进行使用！</h2>';exit;
        }
        $sid = $sid_ver['id'];
		if($act =='index'){
			$modueId = Modue::findWhere([['sid','=',$sid]]);//找出模型
			$fieldId = Field::select([['sid','=',$sid],['table_type','=',0]]);//找出模型字段
            $filed_desc_info = View::ver($old_sid);
            $sfdp_design = Design::find($sid_ver['sid']);
			$search = [];
            $search_field = [];
            $field_wz = [];
			foreach($fieldId as $k=>$v){
				if($v['is_search']==1){
					if($v['type_lx']==1 && $v['function'] !=''){
                        $fun_mode = unit::gconfig('fun_mode') ?? 1;
                        if($fun_mode==1 || $fun_mode==''){
                            $getFun = Functions::findWhere([['fun_name','=',$v['function']]]);
                            if(!$getFun){
                                echo '<h2>系统级别错误('.$v['function'].')：函数名无法找到~</h2>';exit;
                            }
                            $getData = Common::query($getFun['function']);
                        }else{
                            $className = unit::gconfig('fun_namespace');
                            if(!class_exists($className)){
                                return 'Sorry,未找到自定函数，请先配置~';
                            }
                            $getData = (new $className())->func($v['function']);
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
                    if($v['type']=='suphelp'){
                        $getData = Data::getFun3($filed_desc_info['db_id'][$v['fid']]);
                        $v['type_data'] = json_encode($getData);
                    }
                    $search_field[] = $v['field'];
					$search[] = $v;
				}
                if(in_array($v['field'],explode(',',$modueId['field'] ?? ''))){
                    $field_lenth[$v['field']] = $v['width'] ?? '120';
                }
                if($v['field_wz']!=''){
                    $field_wz[$v['field']] = $v['field_wz'];
                }
			}
			$map = SfdpUnit::Bsearch($data,$sid);
			$list = Data::getListData($sid,$map);
			$field_name = explode(',',$modueId['field_name'] ?? '');
            $field_mysql_name = explode(',',$modueId['field'] ?? '');
			$config = [
				'g_js'=>unit::sJs($sid),
				'sid' =>$sid,
                'table' => $sid_ver['s_db'],
				'field'=>$field_name,
                'field_lenth'=>$field_lenth ?? [],
                'field_wz'=>$field_wz,
                'sql_field'=>$field_mysql_name,
                'count_field'=>$modueId['count_field'],
				'search' =>json_encode($search),
                'search_field' =>$search_field,
                'fun' =>$list['field']['fun'],
				'title' =>$modueId['title'],
                'height' =>$modueId['height'],
				'load_file' =>$list['field']['load_file'],
				'show_field' =>$modueId['show_field'],
                'show_type' =>$modueId['show_type'],
                'show_fun' =>$modueId['show_fun'],
                'sfdp_design' =>$sfdp_design
			];
			$btns = explode(',',$modueId['btn']);
			$btns_tohtml = self::btnArray($btns,$old_sid);
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/index.html',['btn'=>$btns_tohtml,'config'=>$config,'list'=>$list['list']]);
				}else{
				return ['config'=>$config,'list'=>$list['list'],'btn'=>$btns];
			}
		}
		if($act =='GetData'){
			$map = SfdpUnit::Bsearch($data,$sid);
			$list = Data::getListData($sid,$map,$data['page'],$data['limit'],$data['whereRaw'] ?? '',$data['sys_order']);
			$jsondata = [];
			foreach($list['list'] as $k=>$v){
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
            $linkdata = json_decode($info['m']['linkdata'] ?? '',true);
            if($linkdata!= null && count($linkdata)>0){
                foreach($linkdata as $k=>$v){
                    $sid_ver_moduce = Design::findVerWhere([['status','=',1],['sid','=',$v[0]]]);
                    $linkdata[$k][3] = (Modue::find($sid_ver_moduce['id']))['title'] ?? '未定义';
                }
            }
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/view.html',['info'=>$info['info'],'row'=>$info['row']]);
				}else{
				return ['info'=>$info['info'],'row'=>$info['row'],'config'=>$info['config'],'i'=>$info,'l'=>$linkdata];
			}
		}
		if($act =='edit'){
			if($data !=''){
				Data::edit($sid,$data,$bid);
				return json(['code'=>0,'msg'=>'保存成功']);
			}
			$data = Data::getEditData($sid,$bid);
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/edit.html',['showtype'=>'edit','config'=>$data['data']['config'],'data'=>$data['info']]);
				}else{
				return ['config'=>$data['data']['config'],'data'=>$data['info'],'bill_info'=>$data['bill_info']];
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
			if(unit::gconfig('return_mode')==1){
				return view(ROOT_PATH.'/edit.html',['showtype'=>'add','config'=>$data['config'],'data'=>$data['info']['s_field']]);
				}else{
				return ['config'=>$data['config'],'data'=>$data['info']['s_field']];
			}
		}
        if($act =='info'){
            $data = Design::getAddData($sid);
            return ['config'=>$data['config'],'nofield'=>$data['nofield'],'data'=>$data['info']['s_field']];
        }
        if($act=='Data'){
            return M::Save($data);
        }
        if($act=='UserConfig'){
            return Userconfig::getInfo($old_sid);
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
