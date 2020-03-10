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

	