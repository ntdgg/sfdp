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

Route::get('index/sfdp/list/sid/:sid','\sfdp\Api@lists');
Route::get('index/sfdp/add/sid/:sid','\sfdp\Api@add');
Route::get('index/sfdp/desc','\sfdp\Api@sfdp');
Route::get('index/sfdp/sfdp_desc/sid/:sid','\sfdp\Api@sfdp_desc');
Route::post('index/sfdp/sfdp_desc_save','\sfdp\Api@sfdp_save');
Route::get('index/sfdp/db/sid/:sid','\sfdp\Api@sfdp_db');

