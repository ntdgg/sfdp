<?php
/**
 *+------------------
 * SFDP-超级表单开发平台V7.0
 *+------------------
 * Sfdp 数据类
 *+------------------
 * Copyright (c) 2018~2020 https://cojz8.com All rights reserved.
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
        $subdata  = json_decode($data['@subdata'],true);
        unset($data['name_db'],$data['tpfd_check'],$data['@subdata'],$data['@saas_id']);
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
        $is_delete = $field['tpfd_del'] ?? 1;
        $sublist =[];
        if($field['sublist']!='' && is_array($field['sublist']) && count($field['sublist'])>0){
            $Stable = self::getSubData($field['name_db'],$field['sublist']);
            foreach($Stable as $k=>$v){
                $ret = self::delSub($v[0],$id,$is_delete);
                if(!$ret){
                    return $ret;
                }
            }
        }
        $table = $sfdp_ver_info['s_db'];
        return (new Data())->mode->del($table,$id,$is_delete);
    }
    /**
     * 删除
     */
    static function delSub($table,$id,$is_delete){
        return (new Data())->mode->delSub($table,$id,$is_delete);
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
    static function getListData($sid,$map,$page=1,$limit=15,$ewhereRaw='',$sys_order=''){
        $jsondata = Design::descVerTodata($sid);
        $Modue = Modue::find($jsondata['sid']);
        /*权限系统组合过滤*/
        $access = json_decode($Modue['access'] ?? '',true);
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
        //增加强制软删除过滤
        if($Modue['is_delete']==0){
            $map[]=['is_delete','=',0];
        }
        $is_saas ='';
        /*超级管理员不受权限控制*/
        if($Modue['is_saas']==0 && unit::getuserinfo('uid')!=1){
            //"concat(',',xtuid,',') regexp '(^|,)(".str_ireplace(',', '|', $ids).")(,|$)'";
            $is_saas = "concat(',',saas_id,',') regexp concat('".str_ireplace(',', ',|,', unit::getuserinfo('saas_id'))."')";
        }
        if($sys_order==''||$sys_order==null){
            $sys_order = $Modue['order'];//模块设置排序
        }
        $field_length = Field::select([['sid','=',$sid],['field_length','>',0]]);
        //saas_id
        /*权限系统组合过滤*/
        $list = (new Data())->mode->select($jsondata['db_name'],$map,$Modue['field'],$page,$limit,$whereRaw,$sys_order,$is_saas,$ewhereRaw);
        $json = $list['data'];

        //dump($jsondata['fieldSysProcess']);
        foreach ($json as $k => $v) {

            foreach($jsondata['fieldArrAll'] as $k2=>$v2){
                if(in_array($k2,explode(',',$Modue['field'] ?? ''))){
                    if(strpos($v[$k2] ?? '',',') !== false){
                        $vk2 = explode(',',$v[$k2]);
                        $vk2value ='';
                        foreach($vk2 as $vvv){
                            $vk2value .=$jsondata['fieldArrAll'][$k2][$vvv].',';
                        }
                        $json[$k][$k2] = rtrim($vk2value, ",");
                    }else{
                        $json[$k][$k2] = $jsondata['fieldArrAll'][$k2][$v[$k2]] ?? unit::gconfig('data_default');
                    }
                    $json[$k]['g_sys_'.$k2] = $v[$k2] ?? '';
                }
            }
            $sys_user = unit::gconfig('sys_user');
            foreach($jsondata['fieldSysUser'] as $k3=>$v3) {
                if(isset($v[$k3])){
                    $json[$k][$k3] = (new $sys_user())->value($v3,$v[$k3]);
                }
            }
            foreach($jsondata['fieldSysProcess'] as $v4) {
                if(isset($v[$v4])){
                    $json[$k][$v4] = '<div id="progressBar" title="'.$v[$v4].'"><div id="progressBar_Track" style="width: '.$v[$v4].'%;"></div></div>';
                }
            }
            foreach($jsondata['fieldSysImg'] as $v4) {
                if(isset($v[$v4])){
                    $json[$k][$v4] = '<img src="/'.$v[$v4].'"  width="100px" onclick=sfdp.openpage("看图",$(this).attr("src"))>';
                }
            }
            foreach($jsondata['fieldSysSign'] as $v4) {
                if(isset($v[$v4])){
                    $json[$k][$v4] = '<img src="'.$v[$v4].'"  width="100px" onclick=sfdp.openpage("签名信息",$(this).attr("src"),{w:"710px",h:"510px"})>';
                }
            }
            /*增加字段超出长度控制*/
            foreach ($field_length as $k5=>$v5){
                if(isset($v[$v5['field']])){
                    if (mb_strlen($v[$v5['field']]) > $v5['field_length']) {
                        $json[$k][$v5['field']] = '<span title="'.$v[$v5['field']].'">'.mb_substr($v[$v5['field']], 0, $v5['field_length']) . '...</span>';
                    }
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
        $sfdp_ver_info = Design::findVer($sid);
        $field = self::setField($sfdp_ver_info,$bid);
        return ['info'=>json_encode($field['field']),'sublist'=>json_encode($field['sublists']),'data'=>Design::getAddData($sid),'bill_info'=>$field['find']];
    }

    /**
     * 获取数据方法
     */
    static function getViewData($sid,$bid){
        $sfdp_ver_info = Design::findVer($sid);
        $field = self::setField($sfdp_ver_info,$bid,'all',1);
        $load_file = SfdpUnit::Loadfile($field['field']['name_db'],$field['field']['tpfd_class'],$field['field']['tpfd_script']);
        $config = unit::sConfig($sfdp_ver_info,$load_file,$sid);
        return ['info'=>json_encode($field['field']),'files'=>json_encode($field['files']),'row'=>$field['find'],'config'=>$config[1],'f'=>$field,'m'=>Modue::find($sid)];
    }
    /*字段统一处理*/
    static function setField($sfdp_ver_info,$bid,$all = '',$is_view=0){
        $field = json_decode($sfdp_ver_info['s_field'],true);
        $find = (new Data())->mode->find($field['name_db'],$bid);
        $zfield = [];
        $files = [];
        foreach($field['list'] as $k=>$v){
            foreach($v['data'] as $k2=>$v2){
                if(isset($v2['xx_type']) && $v2['xx_type']==1 && $v2['td_type']!='time_range' && $v2['td_type']!='date'){
                    if($v2['td_type']=='cascade'){
                        $v2['tpfd_data'] =  Data::getFun2($v2['checkboxes_func'],$all);
                    }else{
                        if($is_view==1){
                            $v2['tpfd_data'] =  Data::getFun($v2['checkboxes_func'],$all,$find[$v2['tpfd_db']]);
                        }else{
                            $v2['tpfd_data'] =  Data::getFun($v2['checkboxes_func'],$all);
                        }
                    }
                }
                if($v2['td_type']=='suphelp'){
                    /*如果$all = all 则输出全部数据，否则则按whereRaw过滤数据*/
                    if($all=='all'){
                        $v2['tpfd_data'] =Data::getFun3($v2);
                    }else{
                        $v2['tpfd_data'] =Data::getFun3($v2,'view');
                    }
                }
                if(in_array($v2['td_type'],['dropdowns','dropdown','radio','checkboxes','suphelp'])){
                    $value_arr = explode(",",$find[$v2['tpfd_db']] ?? '');
                    $fiedsver = '';
                    foreach($value_arr as $v3){
                        if($v3==''){
                            $fiedsver .='';
                        }else{
                            $fiedsver .=($v2['tpfd_data'][$v3] ?? unit::gconfig('data_default')).',';
                        }
                    }
                    $field['list'][$k]['data'][$k2]['value'] = rtrim($fiedsver, ',');
                    $field['list'][$k]['data'][$k2]['tpfd_data'] = $v2['tpfd_data'];
                }elseif($v2['td_type']=='system_user' || $v2['td_type']=="system_role"){
                    $field['list'][$k]['data'][$k2]['value'] = $find[$v2['tpfd_db']];
                    $sys_user = unit::gconfig('sys_user');
                    $field['list'][$k]['data'][$k2]['text'] = (new $sys_user())->value($v2['td_type'],$find[$v2['tpfd_db']]);
                }elseif($v2['td_type']=='cascade'){
                    if($all=='all'){
                        $temp = array_column(self::getFun2($v2['checkboxes_func'],'all'), 'name', 'id');
                        $field['list'][$k]['data'][$k2]['value'] = $temp[$find[$v2['tpfd_db']]] ? : '';;
                        }else{
                        $field['list'][$k]['data'][$k2]['tpfd_data'] = $v2['tpfd_data'];
                        $field['list'][$k]['data'][$k2]['value'] = $find[$v2['tpfd_db']];
                    }
                }else{
                    $field['list'][$k]['data'][$k2]['value'] = $find[$v2['tpfd_db']];
                }
                if($v2['td_type']=='upload' || $v2['td_type']=='upload_img'){
                    if($v2['tpfd_upload_type']==1){
                        $files['ids'][$field['list'][$k]['data'][$k2]['tpfd_name']]= $field['list'][$k]['data'][$k2]['value'];
                        }else{
                        $files['url'][$field['list'][$k]['data'][$k2]['tpfd_name']]= $field['list'][$k]['data'][$k2]['value'];
                    }
                }
                if($is_view==1){
                    unset($v2['tpfd_data']);
                }
                $field['list'][$k]['data'][$k2]['rvalue'] = $find[$v2['tpfd_db']];//增加真实值
                $zfield[$field['list'][$k]['data'][$k2]['tpfd_db']]= $field['list'][$k]['data'][$k2]['value'];
            }
        }
        $sublist =[];
        if(isset($field['sublist']) && $field['sublist']!='' && is_array($field['sublist']) && count($field['sublist'])>0){
            foreach($field['sublist'] as $k=>$v){
                foreach($v['data'] as $k2=>$v2) {
                    if (isset($v2['xx_type']) && $v2['xx_type'] == 1 && $v2['td_type']!='time_range' && $v2['td_type']!='date'){
                        if($is_view!=1){
                            $field['sublist'][$k]['data'][$k2]['tpfd_data'] = self::getFun($v2['checkboxes_func'],$all);
                        }
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
                    unset($v[1]['id']);
                    $mkey = array_keys($v[1]);
                    $vvid =$vv['id'];
                    unset($vv['id']);
                    foreach($mkey as $kkk=>$vvv){
                        if($v[1][$vvv]['td_type']=='dropdown'||$v[1][$vvv]['td_type']=='dropdowns'||$v[1][$vvv]['td_type']=='radio'||$v[1][$vvv]['td_type']=='checkboxes'){
                            $value_arr = explode(",",$vv[$v[1][$vvv]['tpfd_db']] ?? '');
                            $fiedsver = '';
                            foreach($value_arr as $v33){
                                if($v[1][$vvv]['xx_type']==0){
                                    $fiedsver .=($v[1][$vvv]['tpfd_data'][$v33] ?? unit::gconfig('data_default')).',';
                                    }else{
                                    $array = self::getFun($v[1][$vvv]['checkboxes_func'],$all,$v33);
                                    $fiedsver .=($array[$v33] ?? unit::gconfig('data_default')).',';
                                }
                            }
                            $v[1][$vvv]['rvalue'] =  $vv[$v[1][$vvv]['tpfd_db']] ?? '';
                            $v[1][$vvv]['value'] =  rtrim($fiedsver, ',');
                        }else{
                            $v[1][$vvv]['value'] =$vv[$v[1][$vvv]['tpfd_db']];
                        }
                    }
                    $v[1]['id'] =$vvid;
                    if($is_view==1){
                        unset($v[1]['tpfd_data']);
                    }
                    $sublist[$k][$kk] = $v[1];
                }
            }
        }
        $field['sublists'] = $sublist;
        return ['field'=>$field,'find'=>$find,'sublists'=>$sublist,'z'=>$zfield,'files'=>$files];
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
    /*函数处理数据*/
    static function getFun($checkboxes_func,$all ='',$mode='show'){
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
                return ['code'=>1,'msg'=>unit::errMsg(3003),'errCode'=>3003];
            }
            $getData = (new $className())->func($checkboxes_func,$all,$mode);
        }
        if ($getData['code'] == -1) {
            return $getData;
        }
        $tpfd_data = [];
        foreach ($getData['msg'] as $k3 => $v3) {
            if(!array_key_exists('name',$v3) || !array_key_exists('id',$v3)){
                return ['code'=>1,'msg'=>unit::errMsg(3004),'errCode'=>3004];
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
                return ['code'=>1,'msg'=>unit::errMsg(3003),'errCode'=>3003];
            }
            $getData = (new $className())->func($checkboxes_func,$all);
        }
        if ($getData['code'] == -1) {
            return $getData;
        }
        foreach ($getData['msg'] as $k3 => $v3) {
            if(!array_key_exists('name',$v3) || !array_key_exists('id',$v3)){
                return ['code'=>1,'msg'=>unit::errMsg(3004),'errCode'=>3004];
            }
        }
        return $getData['msg'];
    }
    /*超级链接获取数据*/
    static function getFun3($field,$act='all'){
        $whereRaw = '';
        $info = Design::find($field['tpfd_suphelp']);
        if(isset($field['tpfd_suphelp_where']) && $field['tpfd_suphelp_where']<>'' && $act=='view'){
            $whereRaw = str_replace("{userid}",unit::getuserinfo('uid'),$field['tpfd_suphelp_where']);
            $whereRaw = str_replace("{roleid}",unit::getuserinfo('role'),$whereRaw);
        }
        $data = (new Data())->mode->funData($info['s_db'],$field['tpfd_suphelp_value'].' as id,'.$field['tpfd_suphelp_label'].' as name',$whereRaw);
        $tpfd_data = [];
        foreach ($data as $k3 => $v3) {
            if(!array_key_exists('name',$v3) || !array_key_exists('id',$v3)){
                return ['code'=>1,'msg'=>unit::errMsg(3004),'errCode'=>3004];
            }
            $tpfd_data[$v3['id']] = $v3['name'];
        }
        return $tpfd_data;
    }


}
