/*!
 * SFDP 表单设计器---公用方法库
 * http://cojz8.com
 *
 * 
 * Released under the MIT license
 * http://cojz8.com
 *
 * Date: 2020年3月4日23:34:39
 */
var Debug = false;//是否开启打印模式
var commonfun = {
	ShowTip:function(tip) {
		var $tip = $('#tip');
		if ($tip.length == 0) {
			$tip = $('<strong id="tip" style="border-radius:10px;position:absolute;top:50%;left: 50%;z-index:9999;background-color: black;text-align: center;color: white;font-size: 20px;"><div><svg style="width: 3rem;padding: 1.5rem; padding-bottom: 1.1rem; box-sizing: content-box;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="7996"><path d="M509.979 959.316c-247.308 0-447.794-200.486-447.794-447.794S262.671 63.728 509.979 63.728s447.794 200.486 447.794 447.794-200.485 447.794-447.794 447.794z m0-814.171c-202.346 0-366.377 164.031-366.377 366.377s164.031 366.377 366.377 366.377c202.342 0 366.377-164.031 366.377-366.377S712.321 145.145 509.979 145.145z m-40.708 610.628c-40.709 0-40.709-40.708-40.709-40.708l40.709-203.543s0-40.709-40.709-40.709c0 0-40.709 0-40.709-40.709h122.126s40.709 0 40.709 40.709-40.709 162.834-40.709 203.543 40.709 40.709 40.709 40.709h40.709c-0.001 0-0.001 40.708-122.126 40.708z m81.417-407.085c-22.483 0-40.709-18.225-40.709-40.709s18.225-40.709 40.709-40.709 40.709 18.225 40.709 40.709-18.226 40.709-40.709 40.709z" p-id="7997" fill="#ffffff"></path></svg></div><div>  '+tip+'  </div></strong>');
			$('body').append($tip);
		}
		$tip.stop(true).css('margin-left', -$tip.outerWidth() / 2).fadeIn(2000).delay(2000).fadeOut(2000);
		setTimeout(function(){ $tip.remove(); }, 2200);
	},
	dateFormat : function (oDate, fmt){
		var o = {
			"M+": oDate.getMonth() + 1, //月份
			"d+": oDate.getDate(), //日
			"h+": oDate.getHours(), //小时
			"m+": oDate.getMinutes(), //分
			"s+": oDate.getSeconds(), //秒
			"q+": Math.floor((oDate.getMonth() + 3) / 3), //季度
			"S": oDate.getMilliseconds()//毫秒
		};
		if (/(y+)/.test(fmt)) {
			fmt = fmt.replace(RegExp.$1, (oDate.getFullYear() + "").substr(4 - RegExp.$1.length));
		}
		for (var k in o) {
			if (new RegExp("(" + k + ")").test(fmt)) {
				fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
			}
		}
		return fmt;
	},
	fromdata : function (froms){
		var o = {};  
        var arr = froms.serializeArray();  
        $.each(arr,function(){  
            if (o[this.name]) {  //返回json中有该属性
                if (!o[this.name].push) { //将已存在的属性值改成数组
                    o[this.name] = [ o[this.name] ];
                }
                o[this.name].push(this.value || ''); //将值存放到数组中
            } else {  //返回json中没有有该属性
                o[this.name] = this.value || '';  //直接将属性和值放入返回json中
            }  
        });  
        return o; 
	},
	sPost : function(url,data,success){
        if(isDebug){
            console.log('[URL]'+url);
            console.log('[PARM]'+JSON.stringify(data));
        }
        $.post(url, data, function(obj){ if(isDebug) console.log('[Res]'+JSON.stringify(obj)); success(obj); }, 'json' );
    },
	sGet : function(url,success,error){
        if(isDebug){
            console.log('[URL]'+url);
        } 
        $.get(url,function(obj){ obj = JSON.parse(obj); if(isDebug) console.log('[Res]'+JSON.stringify(obj)); success(obj); });
    }  
}

	