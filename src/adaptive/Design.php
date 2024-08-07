<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V7.0
 *+------------------
 * Sfdp 设计类
 *+------------------
 * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
 *+------------------
 */
namespace sfdp\adaptive;

use sfdp\fun\BuildFun;
use sfdp\fun\SfdpUnit;
use sfdp\fun\BuildTable;

use sfdp\lib\unit;


class Design{

    protected $mode ;
    public function  __construct(){
        if(unit::gconfig('db_mode')==1){
            $className = '\\sfdp\\custom\\AdapteeDesign';
        }else{
            $className = unit::gconfig('db_namespace').'AdapteeDesign';
        }
        $this->mode = new $className();
    }
    /**
     * find 获取设计信息  getDesign
     * @param array $whereRaw raw查询条件
     * @param array $map 查询条件
     */
    static function find($sid)
    {
        return (new Design())->mode->find($sid);
    }
    static function select($map=[],$order='id desc')
    {
        return (new Design())->mode->select($map,$order);
    }
    /**
     * getDesignJson 获取设计信息  getDesign
     * @param array $whereRaw raw查询条件
     * @param array $map 查询条件
     */

    static function getDesignJson($sid){
        $info = (new Design())->mode->find($sid);
        $json = json_decode($info['s_field'],true);
        if($info){
            return $json;
        }else{
            return  false;
        }
    }
    /**
     * 插入版本
     */
    static function insertVer($data)
    {
        return (new Design())->mode->insertVer($data);
    }
    /**
     * 更新版本
     */
    static function updateVer($data)
    {
        return (new Design())->mode->updateVer($data);
    }
    /**
     * 更新版本
     */
    static function updateVerWhere($map,$data)
    {
        return (new Design())->mode->updateVerWhere($map,$data);
    }
    /**
     * 查询方法
     */
    static function findVerWhere($data)
    {
        return (new Design())->mode->findVerWhere($data);
    }

    /**
     * 获取设计版本
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
    static function getDesignVer($status=1)
    {
        return (new Design())->mode->getDesignVer($status);
    }
    /**
     * 获取设计版本 getDescVerVal
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
    static function findVer($id)
    {
        return (new Design())->mode->findVer($id);
    }
    /**
     * 获取设计版本  descVerTodata
     *
     * @param $status 版本状态 0为禁用 1为启用
     */
    static function descVerTodata($sid){
        $sfdp_ver_info =(new Design())->mode->findVer($sid);
        $field = json_decode($sfdp_ver_info['s_field'] ?? '',true);
        $list_field = json_decode($sfdp_ver_info['s_list'] ?? '',true);
        $searct_array = json_decode($sfdp_ver_info['s_search'] ?? '',true);
        $searct_field =json_encode($list_field);
        $listid = ''; //变量赋值为空
        $listfield = []; //变量赋值为空
        $fieldArr = [];
        $fieldArrAll = [];
        $fieldSysUser = [];
        $fieldSysProcess = [];
        $fieldSysImg = [];
        $fieldSysSign = [];
        $fieldSup = [];
        foreach($field['list'] as $k=>$v){
            foreach($v['data'] as $k2=>$v2){
                if($v2['td_type']=='process'){
                    $fieldSysProcess[]=$v2['tpfd_db'];
                }
                if($v2['td_type']=='upload_img'){
                    $fieldSysImg[]=$v2['tpfd_db'];
                }
                if($v2['td_type']=='sign'){
                    $fieldSysSign[]=$v2['tpfd_db'];
                }
                if($v2['td_type']=='system_user'||$v2['td_type']=='system_role'){
                    $fieldSysUser[$v2['tpfd_db']]=$v2['td_type'];
                }
                if(isset($v2['xx_type']) && $v2['xx_type']==1 && $v2['td_type']!='time_range' && $v2['td_type']!='date'){
                    //函数名转为数据信息
                    if($v2['td_type']=='cascade'){
                        $v2['tpfd_data'] = self::set_c(Data::getFun2($v2['checkboxes_func'],'all'));
                    }else{
                        $v2['tpfd_data'] = Data::getFun($v2['checkboxes_func'],'all');
                    }
                }
                if($v2['td_type']=='suphelp'||$v2['td_type']=='suphelps'){
                    $fieldSup[$v2['tpfd_id']] = $v2['tpfd_suphelp_where'];
                    $v2['tpfd_data'] =Data::getFun3($v2);
                }
                if(isset($v2['tpfd_db']) and(isset($v2['tpfd_list']))){
                    if(($v2['td_type']=='dropdowns'||$v2['td_type']=='cascade'||$v2['td_type']=='dropdown'||$v2['td_type']=='radio'||$v2['td_type']=='checkboxes')and($v2['tpfd_list']=='yes')){
                        $fieldArr[$v2['tpfd_db']]=$v2['tpfd_data'];
                    }
                    if($v2['td_type']=='dropdowns'||$v2['td_type']=='cascade'||$v2['td_type']=='dropdown'||$v2['td_type']=='radio'||$v2['td_type']=='checkboxes'||$v2['td_type']=='suphelp'||$v2['td_type']=='suphelps'){
                        $fieldArrAll[$v2['tpfd_db']]=$v2['tpfd_data'];
                    }
                }
            }
        }
        $load_file = SfdpUnit::Loadfile($field['name_db'],$field['tpfd_class'],$field['tpfd_script']);
        return ['sid'=>$sfdp_ver_info['id'],'db_name'=>$field['name_db'],'load_file'=>$load_file,'btn'=>$field['tpfd_btn'],'field'=>rtrim($listid, ','),'search'=>$searct_field,'fun'=>unit::uJs($sfdp_ver_info),'title'=>$sfdp_ver_info['s_name'],'fieldArr'=>$fieldArr,'fieldArrAll'=>$fieldArrAll,'fieldSysUser'=>$fieldSysUser,'fieldSysProcess'=>$fieldSysProcess,'fieldSysImg'=>$fieldSysImg,'fieldSup'=>$fieldSup,'fieldSysSign'=>$fieldSysSign];
    }
    static function set_c($array){
        $result = [];
        foreach ($array as $item) {
            $name = $item['name'];
            $pid = $item['pid'];
            $parentName = '';
            while ($pid != 0) {
                foreach ($array as $parentItem) {
                    if ($parentItem['id'] == $pid) {
                        $parentName = $parentItem['name'] . '->' . $parentName;
                        $pid = $parentItem['pid'];
                        break;
                    }
                }
            }

            $result[$item['id']] = $parentName . $name;
        }
        return $result;
    }

    /**
     * 获取数据
     */
    static function getAddData($sid,$app=0){
        $sfdp_ver_info = (new Design())->mode->findVer($sid);
        $field = self::fieldFun($sfdp_ver_info['s_field']);
        $sfdp_ver_info['s_field'] = json_encode($field);
        $load_file = SfdpUnit::Loadfile($field['name_db'],$field['tpfd_class'],$field['tpfd_script']);
        if($app==1){
            $config = ['',''];
         }else{
            $config = unit::sConfig($sfdp_ver_info,$load_file,$sid);
        }
        $keyfield = [];
        $nofield = [];
        foreach ($field['list'] as $key=>$item) {
            foreach ($item['data'] as $v) {
                if(isset($v['td_type']) && $v['td_type']=='billno'){
                    $info = Bill::findWhere(['type'=>$sfdp_ver_info['sid'],'state'=>1]);
                    $nofield = [$v['tpfd_db'],$info['name'] ?? '',$info['lenth'] ?? ''];
                }
                if(isset($v['tpfd_key']) && $v['tpfd_key']=='0'){
                    $keyfield[] = [$v['tpfd_db'],$v['tpfd_name']];
                }
            }
        }
        return ['info'=>$sfdp_ver_info,'fun'=>$config[0],'nofield'=>$nofield,'keyfield'=>$keyfield,'load_file'=>$load_file,'config'=>$config[1]];
    }
    static function fieldFun($sfdp_ver_info){
        $field = json_decode($sfdp_ver_info,true);
        foreach($field['list'] as $k=>$v){
            foreach($v['data'] as $k2=>$v2){
                if(isset($v2['xx_type']) && $v2['xx_type']==1 && $v2['td_type']!='time_range' && $v2['td_type']!='date'){
                    if($v2['td_type']=='cascade'){
                        $field['list'][$k]['data'][$k2]['tpfd_data'] = Data::getFun2($v2['checkboxes_func']);
                    }else{
                        $field['list'][$k]['data'][$k2]['tpfd_data'] = Data::getFun($v2['checkboxes_func']);
                    }
                }
                if($v2['td_type']=='suphelp'||$v2['td_type']=='suphelps'){
                    $field['list'][$k]['data'][$k2]['tpfd_data'] =Data::getFun3($v2,'view');
                }
            }
        }
        //字表数据
        if(isset($field['sublist']) && $field['sublist']!='' && is_array($field['sublist']) && count($field['sublist'])>0){
            foreach($field['sublist'] as $k=>$v){
                foreach($v['data'] as $k2=>$v2) {
                    if (isset($v2['xx_type']) && $v2['xx_type'] == 1 && $v2['td_type']!='time_range' && $v2['td_type']!='date'){
                        $field['sublist'][$k]['data'][$k2]['tpfd_data'] = Data::getFun($v2['checkboxes_func']);;
                    }
                }
            }
        }
        return $field;
    }
    /**
     * 设计保存
     */
    static function saveDesc($data,$type='save'){
        if($type=='save'){
            $data['s_field'] = htmlspecialchars_decode($data['ziduan']);
            $field = json_decode($data['s_field'],true);
            $db_field_array = [];
            foreach($field['list'] as $k=>$v){
                foreach($v['data'] as $v2){
                    $db_field_array[] = $v2['tpfd_db'];
                    if(isset($v2['xx_type']) && $v2['xx_type']==1 && $v2['td_type']!='time_range' && $v2['td_type']!='date'){
                        $ret = Data::getFun($v2['checkboxes_func']);;
                        if(isset($ret['code'])){
                            return $ret;
                        }
                    }
                }
            }
            if (count($db_field_array) != count(array_unique($db_field_array))) {
                return ['code'=>1,'msg'=>unit::errMsg(3002)];
            }
            $ver = [
                'id'=>$data['id'],
                's_title'=>$field['name'],
                's_db'=>$field['name_db'],
                's_field'=>htmlspecialchars_decode($data['ziduan']),
                's_design'=>1,
                'add_time'=>time()
            ];
            if((new Design())->mode->update($ver)){
                return ['code'=>0,'msg'=>'Success'];
            }else{
                return ['code'=>1,'msg'=>'Sorry,更新失败~'];
            }

        }elseif($type=='update'){
            return (new Design())->mode->update($data);
        }else{
            $ver = [
                's_bill'=>unit::OrderNumber(),
                'add_user'=>'Sys',
                's_field'=>1,
                's_type'=>$data ?? 0,
                'add_time'=>time()
            ];
            return (new Design())->mode->insert($ver);
        }
    }
    /**
     * 插入版本
     */
    static function insertBtable($data)
    {
        return (new Design())->mode->insertBtable($data);
    }
}