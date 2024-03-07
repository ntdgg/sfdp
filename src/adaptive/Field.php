<?php
/**
  *+------------------
  * SFDP-超级表单开发平台V7.0
  *+------------------
  * Sfdp 数据类
  *+------------------
  * Copyright (c) 2018~2023 www.liuzhiyun.com All rights reserved.
  *+------------------
  */
namespace sfdp\adaptive;


use sfdp\fun\BuildTable;
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

	static function select($map=[],$order='id asc')
    {
		return (new Field())->mode->select($map,$order);
    }
    /*获取Sup的查询条件来过滤*/
    static function getSupRaw($suphelpID,$sid){
        $sid_ver = (new Field())->mode->findFidVal($suphelpID);
        $data = Design::descVerTodata($sid_ver);
        return $data['fieldSup'][$suphelpID] ?? '';
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
				['sid'=>$varId,'field'=>'id','name'=>'主键','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'text','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>'','table_type'=>0,'table_id'=>''],
				['sid'=>$varId,'field'=>'uid','name'=>'用户id','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'text','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>'','table_type'=>0,'table_id'=>''],
				['sid'=>$varId,'field'=>'status','name'=>'审核状态','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'text','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>'','table_type'=>0,'table_id'=>''],
				['sid'=>$varId,'field'=>'create_time','name'=>'新增时间','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>180,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'datetime','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>'','table_type'=>0,'table_id'=>''],
				['sid'=>$varId,'field'=>'update_time','name'=>'更新时间','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>180,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'datetime','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>'','table_type'=>0,'table_id'=>'']
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
                    'width'=>intval($v['tpfd_width'] ?? 120),//组件长度
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
					'search_type'=>'','table_type'=>0,'table_id'=>''
				];
			}
		return (new Field())->mode->add_all($field);
	}
    /**
     * 添加
     */
    static function sadd($varId,$varInfo){
        foreach($varInfo as $k=>$v){
            $field = [
                ['sid'=>$varId,'field'=>'id','name'=>'主键','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'text','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>'','table_type'=>1,'table_id'=>$k],
                ['sid'=>$varId,'field'=>'uid','name'=>'用户id','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'text','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>'','table_type'=>1,'table_id'=>$k],
                ['sid'=>$varId,'field'=>'status','name'=>'审核状态','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'text','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>'','table_type'=>1,'table_id'=>$k],
                ['sid'=>$varId,'field'=>'create_time','name'=>'新增时间','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'datetime','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>'','table_type'=>1,'table_id'=>$k],
                ['sid'=>$varId,'field'=>'update_time','name'=>'更新时间','name_type'=>'int','zanwei'=>'','moren'=>'','width'=>120,'is_request'=>'','is_read'=>'','length'=>11,'type_data'=>'','type'=>'datetime','data'=>'','function'=>'','type_lx'=>'','is_list'=>'','is_search'=>'','fid'=>'','search_type'=>'','table_type'=>1,'table_id'=>$k]
            ];
            foreach($v as $k2=>$v2){
                $type_lx = intval($v2['xx_type'] ?? 0);
                $field[] = [
                    'sid'=>$varId,
                    'field'=>$v2['tpfd_db'],
                    'name'=>$v2['tpfd_name'],//字段名
                    'name_type'=>$v2['tpfd_dblx'],
                    'zanwei'=>$v2['tpfd_zanwei'] ?? '',//占位标识
                    'moren'=>$v2['tpfd_moren'] ?? '',//默认标识
                    'width'=>intval($v2['tpfd_width'] ?? 120),//组件长度
                    'is_request'=>$v['tpfd_must'] ?? '',
                    'is_read'=>$v2['tpfd_read'] ?? '',
                    'length'=>$v2['tpfd_dbcd'],
                    'type_data'=>json_encode($v2['tpfd_data'] ?? '',true),
                    'type'=>$v2['td_type'],//输入类型
                    'data'=>$v2['xx_type'] ?? '',
                    'function'=>$v2['checkboxes_func'] ??'',
                    'type_lx'=>1,
                    'is_list'=>$v2['tpfd_list']=='yes'?1:0,
                    'is_search'=>$v2['tpfd_chaxun']=='yes'?1:0,
                    'fid'=>$v2['tpfd_id'],
                    'search_type'=>'','table_type'=>1,'table_id'=>$k
                ];
            }
             (new Field())->mode->add_all($field);
            unset($field);
        }
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


    static function fieldSelectIndex($sid){
        return (new Field())->mode->selectFieldIndex($sid);
    }
    /**
     * 创建字段索引信息
     * @param $post
     * @return array
     */
    static function fieldBuildIndex($post){
        $res = (new Field())->mode->addFieldIndex(['sid'=>$post['sid'],'field'=>$post['key'],'type'=>$post['val'],'add_time'=>time()]);
        if($res){
            $info = Design::find($post['sid']);
            return BuildTable::buildIndex($info['s_db'],$post['key'],$post['val']);
        }else{
            return ['code'=>1,'msg'=>'写入数据库出错。'];
        }
    }

    static function fieldDelIndex($post){
        $find = (new Field())->mode->viewFieldIndex($post['id']);
        if(!$find){
            return ['code'=>1,'msg'=>'索引已被删除！'];
        }
        /*执行删除*/
        $del = (new Field())->mode->delFieldIndex($post['id']);
        if($del){
            $info = Design::find($find['sid']);
            return BuildTable::delIndex($info['s_db'],$find['field']);
        }else{
            return ['code'=>1,'msg'=>'写入数据库出错。'];
        }

    }
	
	
}