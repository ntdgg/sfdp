<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::rule('index/sfdp/sfdpApi','\sfdp\Api@sfdpApi');
Route::rule('index/sfdp/sfdpCurd','\sfdp\Api@sfdpCurd');


Route::get('index/sfdp/create','\sfdp\Api@sfdp_create');
Route::post('index/sfdp/sfdp_script_save','\sfdp\Api@sfdp_script_save');
Route::post('index/sfdp/add/sid/:sid','\sfdp\Api@saveadd');
Route::post('index/sfdp/sfdp_fun_save','\sfdp\Api@sfdp_fun_save');
Route::post('index/sfdp/get_function_val','\sfdp\Api@get_function_val');
/*列表路由*/
Route::get('index/sfdp/list/sid/:sid','\sfdp\Api@lists');
Route::post('index/sfdp/list/sid/:sid','\sfdp\Api@lists');
/*查看路由*/
Route::get('index/sfdp/sfdp_view/sid/:sid/bid/:bid','\sfdp\Api@sfdp_view');



