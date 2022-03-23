<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V5.0
  *+------------------
  * Sfdp 数据类
  *+------------------
  * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
  *+------------------
  * Author: guoguo(1838188896@qq.com)
  *+------------------ 
  */
namespace sfdp\adaptive;


use sfdp\fun\SfdpUnit;
use sfdp\lib\unit;

class Field{
	
	protected $mode ; 
    public function  __construct(){
		if(unit::gconfig('db_mode')==1){
			$className = '\\sfdp\\custom\\AdapteeField';
		}else{
			$className = unit::gconfig('db_namespace').'AdapteeField';
		}
		$this->mode = new $className();
    }

    static function addField($postData){
        return (new Field())->mode->add($postData);
    }
    static function upField($postData,$id){
        return (new Field())->mode->saveWhere([['id','=',$id]],$postData);
    }

	static function select($map=[])
    {
		return (new Field())->mode->select($map);
    }
	static function saveWhere($where,$data)
    {
		return (new Field())->mode->saveWhere($where,$data);
    }
	/**
	 * 添加
	 */
	static function add($varId,$varInfo){
		$field = [
				['sid'=>$varId,'field'=>'id','name'=>'主键','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'text','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>''],
				['sid'=>$varId,'field'=>'uid','name'=>'用户id','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'text','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>''],
				['sid'=>$varId,'field'=>'status','name'=>'审核状态','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'text','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>''],
				['sid'=>$varId,'field'=>'create_time','name'=>'新增时间','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'datetime','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>''],
				['sid'=>$varId,'field'=>'update_time','name'=>'更新时间','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'datetime','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>'']
			];
			foreach($varInfo as $k=>$v){
				$type_lx = intval($v['xx_type'] ?? 0);
				$field[] = [
					'sid'=>$varId,
					'field'=>$v['tpfd_db'],
					'name'=>$v['tpfd_name'],//字段名
					'name_type'=>$v['tpfd_dblx'],
					'zanwei'=>$v['tpfd_zanwei'] ?? '',//占位标识
					'moren'=>$v['tpfd_moren'] ?? '',//默认标识
                    'width'=>$v['tpfd_width'] ?? 120,//组件长度
					'is_request'=>$v['tpfd_must'] ?? '',
					'is_read'=>$v['tpfd_read'] ?? '',
					'length'=>$v['tpfd_dbcd'],
					'type_data'=>json_encode($v['tpfd_data'] ?? '',true),
					'type'=>$v['td_type'],//输入类型
					'data'=>$v['xx_type'] ?? '',
					'function'=>$v['checkboxes_func'] ??'',
					'type_lx'=>1,
					'is_list'=>$v['tpfd_list']=='yes'?1:0,
					'is_search'=>$v['tpfd_chaxun']=='yes'?1:0,
                    'fid'=>$v['tpfd_id'],
					'search_type'=>''

				];
			}
			
		return (new Field())->mode->add_all($field);
	}
	/**
	 * 添加
	 */
	static function find($id){
		return (new Field())->mode->find($id);		
	}
	static function findWhere($map){
		return (new Field())->mode->findWhere($map);		
	}
	static function value($id){
		return (new Field())->mode->value($id);		
	}
	
	
}