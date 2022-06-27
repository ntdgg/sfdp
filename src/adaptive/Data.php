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

class Data{

    protected $mode ;
    public function  __construct(){
        if(unit::gconfig('db_mode')==1){
            $className = '\\sfdp\\custom\\AdapteeData';
        }else{
            $className = unit::gconfig('db_namespace').'AdapteeData';
        }
        $this->mode = new $className();
    }
    /**
     * 添加
     */
    static function add($sid,$data){
        foreach($data as $k=>$v){
            if(is_array($v)){
                $data[$k] = implode(",", $v);
            }
        }
        $data['uid']=unit::getuserinfo('uid');
        $data['create_time']=time();
        $table = $data['name_db'];
        unset($data['name_db']);
        unset($data['tpfd_check']);
        $subdata  = json_decode($data['@subdata'],true);
        unset($data['@subdata']);
        $did = (new Data())->mode->add($table,$data);
        if(!$did){
            return $did;
        }
        if(is_array($subdata) && count($subdata)>0 ){
            $subDatas = [];
            foreach($subdata as $k=>$v){
                $keys = array_keys($v);//取出所有
                $num = count($v[$keys[0]]);//计算下有几列数据
                //数组转换，将值合并
                for ($x=0; $x<$num; $x++) {
                    $new[$x] = [];
                    foreach($keys as $key){
                        //相同顺序的值Push到同个数组
                        array_push($new[$x],[$key=>$v[$key][$x]]);
                    }
                }
                //将值对应转成二维数组
                foreach($new as $kk=>$vv){
                    //二维数组转一维数组，并赋值给对应的数据表
                    $subDatas[$k][] = array_reduce($vv, 'array_merge', array());
                }
            }
            foreach($subDatas as $k=>$v){
                $subTable = $k;
                foreach($v as $kk=>$vv){
                    $vv['uid']=unit::getuserinfo('uid');
                    $vv['create_time']=time();
                    $vv['d_id']=$did;
                    $ret = (new Data())->mode->add($subTable,$vv);
                    if(!$ret){
                        return $ret;
                    }
                }
            }
        }
        return 	true;
    }
    /**
     * 删除
     */
    static function del($sid,$id){
        $sfdp_ver_info = Design::findVer($sid);
        $field = json_decode($sfdp_ver_info['s_field'],true);
        $sublist =[];
        if($field['sublist']!='' && is_array($field['sublist']) && count($field['sublist'])>0){
            $Stable = self::getSubData($field['name_db'],$field['sublist']);
            foreach($Stable as $k=>$v){
                $ret = self::delSub($v[0],$id);
                if(!$ret){
                    return $ret;
                }
            }
        }

        $table = $sfdp_ver_info['s_db'];
        return (new Data())->mode->del($table,$id);
    }
    /**
     * 删除
     */
    static function delSub($table,$id){
        return (new Data())->mode->delSub($table,$id);
    }
    /**
     * 编辑修改
     */
    static function edit($sid,$data,$id){
        foreach($data as $k=>$v){
            if(is_array($v)){
                $data[$k] = implode(",", $v);
            }
        }
        $table = $data['name_db'];
        $data['update_time'] = time();
        unset($data['name_db']);
        unset($data['tpfd_check']);
        $subdata  = json_decode($data['@subdata'],true);
        unset($data['@subdata']);
        //修改主表数据
        $ret = (new Data())->mode->edit($table,$data,$id);
        if(!$ret){
            return $ret;
        }
        if(is_array($subdata) && count($subdata)>0 ){
            $subDatas = [];
            foreach($subdata as $k=>$v){
                $keys = array_keys($v);//取出所有
                $num = count($v[$keys[0]]);//计算下有几列数据
                //数组转换，将值合并
                for ($x=0; $x<$num; $x++) {
                    $new[$x] = [];
                    foreach($keys as $key){
                        //相同顺序的值Push到同个数组
                        array_push($new[$x],[$key=>$v[$key][$x]]);
                    }
                }
                //将值对应转成二维数组
                foreach($new as $kk=>$vv){
                    //二维数组转一维数组，并赋值给对应的数据表
                    $subDatas[$k][] = array_reduce($vv, 'array_merge', array());
                }
            }
            foreach($subDatas as $k=>$v){
                $subTable = $k;
                self::delSub($subTable,$id);
                foreach($v as $kk=>$vv){
                    $vv['uid']=unit::getuserinfo('uid');
                    $vv['create_time']=time();
                    $vv['d_id']=$id;
                    $ret = (new Data())->mode->add($subTable,$vv);
                    if(!$ret){
                        return $ret;
                    }
                }
            }
        }
        return 	true;
    }
    /**
     * 获取列表数据
     */
    static function getListData($sid,$map,$page=1,$limit=15){
        $jsondata = Design::descVerTodata($sid);
        $Modue = Modue::find($jsondata['sid']);
        /*权限系统组合过滤*/
        $access = json_decode($Modue['access'],true);
        $whereRaw ='';
        $count = count((array)$access);
        foreach((array)$access as $kk=>$vv){
            $field = Field::find($vv[0]);
            $value =unit::getuserinfo($vv[2]);
            if($vv[1]=='in'){
                $eq ='find in set';
                $fi = "FIND_IN_SET('".$value."', `".$field['field']."`)";
            }else if($vv[1]=='no in'){
                $eq ='NOT REGEXP';
                $fi =  $field['field'] ." NOT REGEXP '(^|,)(".$value.")(,|$)'";
            }else{
                $eq =$vv[1];
                $fi =  $field['field'] .' '. $eq .' '. $value .' ';
            }
            $link ='';
            if($kk >= 0 && $kk < $count-1){
                $link =$vv[4];
            }
            $whereRaw .= $fi.' '. $link.' ';
        }
        if(unit::getuserinfo('uid')==1){
            $whereRaw ='';
        }
        /*权限系统组合过滤*/
        $list = (new Data())->mode->select($jsondata['db_name'],$map,$Modue['field'],$page,$limit,$whereRaw,$Modue['order']);
        $json = $list['data'];
        foreach ($json as $k => $v) {
            foreach($jsondata['fieldArrAll'] as $k2=>$v2){
                if(in_array($k2,explode(',',$Modue['field']))){
                    if(strpos($v[$k2],',') !== false){
                        $vk2 = explode(',',$v[$k2]);
                        $vk2value ='';
                        foreach($vk2 as $vvv){
                            $vk2value .=$jsondata['fieldArrAll'][$k2][$vvv].',';
                        }
                        $json[$k][$k2] = rtrim($vk2value, ",");
                    }else{
                        $json[$k][$k2] = $jsondata['fieldArrAll'][$k2][$v[$k2]] ?? '<font color="red">未选择</font>';
                    }
                }
            }
            $sys_user = unit::gconfig('sys_user');
            foreach($jsondata['fieldSysUser'] as $k3=>$v3) {
                if(isset($v[$k3])){
                    $json[$k][$k3] = (new $sys_user())->value($v3,$v[$k3]);
                }
            }

        }
        return ['count'=>$list['count'],'list'=>$json,'field'=>$jsondata,'title'=>$jsondata['title']];
    }
    static function selectAll($table,$where=[]){
        return  (new Data())->mode->selectAll($table,$where);
    }
    /**
     * 修改编辑
     */
    static function getEditData($sid,$bid){
        $data = Design::getAddData($sid);
        $sfdp_ver_info = Design::findVer($sid);
        $field = json_decode($sfdp_ver_info['s_field'],true);
        $find = (new Data())->mode->find($field['name_db'],$bid);
        foreach($field['list'] as $k=>$v){
            foreach($v['data'] as $k2=>$v2){
                if(isset($v2['xx_type']) && $v2['xx_type']==1 && $v2['td_type']!='time_range' && $v2['td_type']!='date'){
                    if($v2['td_type']=='cascade'){
                        $v2['tpfd_data'] =  Data::getFun2($v2['checkboxes_func']);
                    }else{
                        $v2['tpfd_data'] =  Data::getFun($v2['checkboxes_func']);
                    }

                }

                if($v2['td_type']=='dropdowns' || $v2['td_type']=='dropdown'||$v2['td_type']=='radio'||$v2['td_type']=='checkboxes'){
                    $value_arr = explode(",",$find[$v2['tpfd_db']]);
                    $fiedsver = '';
                    foreach($value_arr as $v3){
                        $fiedsver .=($v2['tpfd_data'][$v3] ?? $v3).',';
                    }
                    $field['list'][$k]['data'][$k2]['tpfd_data'] = $v2['tpfd_data'];
                    $field['list'][$k]['data'][$k2]['value'] = rtrim($fiedsver, ',');
                    $field['list'][$k]['data'][$k2]['rvalue'] =$find[$v2['tpfd_db']];
                }elseif($v2['td_type']=='system_user' || $v2['td_type']=="system_role"){
                    $field['list'][$k]['data'][$k2]['value'] = $find[$v2['tpfd_db']];
                    $sys_user = unit::gconfig('sys_user');
                    $field['list'][$k]['data'][$k2]['text'] = (new $sys_user())->value($v2['td_type'],$find[$v2['tpfd_db']]);
                }elseif($v2['td_type']=='cascade'){
                    $field['list'][$k]['data'][$k2]['tpfd_data'] = $v2['tpfd_data'];
                    $field['list'][$k]['data'][$k2]['value'] = $find[$v2['tpfd_db']];
                }else{
                    $field['list'][$k]['data'][$k2]['value'] = $find[$v2['tpfd_db']];
                }
            }
        }
        //字表数据
        $sublist =[];
        if(isset($field['sublist']) && $field['sublist']!='' && is_array($field['sublist']) && count($field['sublist'])>0){
            foreach($field['sublist'] as $k=>$v){
                foreach($v['data'] as $k2=>$v2) {
                    if (isset($v2['xx_type']) && $v2['xx_type'] == 1 && $v2['td_type']!='time_range' && $v2['td_type']!='date'){
                        //函数名转为数据信息
                        $field['sublist'][$k]['data'][$k2]['tpfd_data'] = self::getFun($v2['checkboxes_func']);
                    }else{
                        if(isset($v2['tpfd_data'])){
                            $field['sublist'][$k]['data'][$k2]['tpfd_data']  = $v2['tpfd_data'];
                        }
                    }
                }
            }
            $Stable = self::getSubData($field['name_db'],$field['sublist']);
            foreach($Stable as $k=>$v){
                $sublist[$k]= self::selectAll($v[0],['d_id'=>$bid]);
                $datas = self::selectAll($v[0],['d_id'=>$bid]);
                foreach($datas as $kk=>$vv){
                    $mkey = array_keys($v[1]);
                    $mkey2 = array_keys($vv);
                    foreach($mkey as $kkk=>$vvv){
                        if($v2['td_type']=='dropdowns' || $v[1][$vvv]['td_type']=='dropdown'||$v[1][$vvv]['td_type']=='radio'||$v[1][$vvv]['td_type']=='checkboxes'){
                            $v[1][$vvv]['value'] =$v[1][$vvv]['tpfd_data'][$vv[$mkey2[$kkk]]] ?? '未匹配';
                        }else{
                            $v[1][$vvv]['value'] =$vv[$mkey2[$kkk]];
                        }
                    }
                    $sublist[$k][$kk] = $v[1];
                }
            }
        }
        $field['sublists'] = $sublist;
        return ['info'=>json_encode($field),'sublist'=>json_encode($sublist),'data'=>$data];
    }
    /**
     * 子表数据
     */
    static function getSubData($table,$data){
        $buile_table =[];
        $i = 1;
        foreach($data as $k=>$v){
            if($v['data']!='' && is_array($v['data']) && count($v['data'])>0){
                $buile_table[$i-1][] = $table.'_d'.$i;
                $buile_table[$i-1][] = $v['data'];
                $i++;
            }
        }
        return $buile_table;
    }
    /**
     * 获取数据方法
     */
    static function getViewData($sid,$bid){
        $sfdp_ver_info = Design::findVer($sid);
        $field = json_decode($sfdp_ver_info['s_field'],true);
        $find = (new Data())->mode->find($field['name_db'],$bid);
        foreach($field['list'] as $k=>$v){
            foreach($v['data'] as $k2=>$v2){
                if(isset($v2['xx_type']) && $v2['xx_type']==1 && $v2['td_type']!='time_range' && $v2['td_type']!='date'){
                    $v2['tpfd_data'] = self::getFun($v2['checkboxes_func'],'all');
                }
                if($v2['td_type']=='dropdowns' || $v2['td_type']=='dropdown'||$v2['td_type']=='radio'||$v2['td_type']=='checkboxes'){
                    $value_arr = explode(",",$find[$v2['tpfd_db']]);
                    $fiedsver = '';
                    foreach($value_arr as $v3){
                        $fiedsver .=($v2['tpfd_data'][$v3] ?? '未选择').',';
                    }
                    $field['list'][$k]['data'][$k2]['value'] = rtrim($fiedsver, ',');
                }elseif($v2['td_type']=='system_user' || $v2['td_type']=="system_role"){
                    $field['list'][$k]['data'][$k2]['value'] = $find[$v2['tpfd_db']];
                    $sys_user = unit::gconfig('sys_user');
                    $field['list'][$k]['data'][$k2]['text'] = (new $sys_user())->value($v2['td_type'],$find[$v2['tpfd_db']]);
                }elseif($v2['td_type']=='cascade'){
                    $temp = array_column(self::getFun2($v2['checkboxes_func'],'all'), 'name', 'id');
                    $field['list'][$k]['data'][$k2]['value'] = $temp[$find[$v2['tpfd_db']]] ? : '';;
                }else{
                    $field['list'][$k]['data'][$k2]['value'] = $find[$v2['tpfd_db']];
                }
                $field['list'][$k]['data'][$k2]['rvalue'] = $find[$v2['tpfd_db']];//增加真实值
            }
        }
        //字表数据
        $sublist =[];
        if(isset($field['sublist']) && $field['sublist']!='' && is_array($field['sublist']) && count($field['sublist'])>0){
            foreach($field['sublist'] as $k=>$v){
                foreach($v['data'] as $k2=>$v2) {
                    if (isset($v2['xx_type']) && $v2['xx_type'] == 1 && $v2['td_type']!='time_range' && $v2['td_type']!='date'){
                        $field['sublist'][$k]['data'][$k2]['tpfd_data'] = self::getFun($v2['checkboxes_func'],'all');
                    }else{
                        if(isset($v2['tpfd_data'])){
                            $field['sublist'][$k]['data'][$k2]['tpfd_data']  = $v2['tpfd_data'];
                        }
                    }
                }
            }
            $Stable = self::getSubData($field['name_db'],$field['sublist']);
            foreach($Stable as $k=>$v){
                $sublist[$k]= self::selectAll($v[0],['d_id'=>$bid]);
                $datas = self::selectAll($v[0],['d_id'=>$bid]);
                $find['sublist_data'][$v[0]] = $datas;
                foreach($datas as $kk=>$vv){
                    $mkey = array_keys($v[1]);
                    $mkey2 = array_keys($vv);
                    foreach($mkey as $kkk=>$vvv){
                        if($v[1][$vvv]['td_type']=='dropdown'||$v[1][$vvv]['td_type']=='radio'||$v[1][$vvv]['td_type']=='checkboxes'){
                            $v[1][$vvv]['value'] =$v[1][$vvv]['tpfd_data'][$vv[$mkey2[$kkk]]] ?? '未匹配';
                        }else{
                            $v[1][$vvv]['value'] =$vv[$mkey2[$kkk]];
                        }
                    }
                    $sublist[$k][$kk] = $v[1];
                }
            }
        }
        $field['sublists'] = $sublist;
        return ['info'=>json_encode($field),'row'=>$find];
    }
    /*函数处理数据*/
    static function getFun($checkboxes_func,$all =''){
        //函数名转为数据信息
        $fun_mode = unit::gconfig('fun_mode') ?? 1;
        if ($fun_mode == 1 || $fun_mode == '') {
            $getFun = Functions::findWhere([['fun_name', '=', $checkboxes_func]]);
            if (!$getFun) {
                echo '<h2>系统级别错误(' . $checkboxes_func . ')：函数名无法找到~</h2>';
                exit;
            }
            $getData = Common::query($getFun['function']);
        } else {
            $className = unit::gconfig('fun_namespace');
            if (!class_exists($className)) {
                return ['code'=>1,'msg'=>unit::errMsg(3003)];
            }
            $getData = (new $className())->func($checkboxes_func,$all);
        }
        if ($getData['code'] == -1) {
            return $getData;
        }
        $tpfd_data = [];
        foreach ($getData['msg'] as $k3 => $v3) {
            if(!array_key_exists('name',$v3) || !array_key_exists('id',$v3)){
                return ['code'=>1,'msg'=>unit::errMsg(3004)];
            }
            $tpfd_data[$v3['id']] = $v3['name'];
        }
        return $tpfd_data;
    }
    /*函数处理数据*/
    static function getFun2($checkboxes_func,$all =''){
        //函数名转为数据信息
        $fun_mode = unit::gconfig('fun_mode') ?? 1;
        if ($fun_mode == 1 || $fun_mode == '') {
            $getFun = Functions::findWhere([['fun_name', '=', $checkboxes_func]]);
            if (!$getFun) {
                echo '<h2>系统级别错误(' . $checkboxes_func . ')：函数名无法找到~</h2>';
                exit;
            }
            $getData = Common::query($getFun['function']);
        } else {
            $className = unit::gconfig('fun_namespace');
            if (!class_exists($className)) {
                return ['code'=>1,'msg'=>unit::errMsg(3003)];
            }
            $getData = (new $className())->func($checkboxes_func,$all);
        }
        if ($getData['code'] == -1) {
            return $getData;
        }

        foreach ($getData['msg'] as $k3 => $v3) {
            if(!array_key_exists('name',$v3) || !array_key_exists('id',$v3)){
                return ['code'=>1,'msg'=>unit::errMsg(3004)];
            }
        }
        return $getData['msg'];
    }
}