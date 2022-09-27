<?php
/**
 *+------------------
 * Gadmin 3.0 企业级开发平台
 *+------------------
 */
namespace app\api\controller\m;

use app\api\controller\Data as Api;
use app\common\server\Sfdp;
use think\facade\Session;
use think\facade\Db;

class [class]  extends Api{
    public function index()
    {
        if($this->numb>0 && $this->numb <> '') {
            $count = Db::name("[class]")->count();
            if ($count > $this->numb) {
             return view(root_path() . '/app/api/view/success.html', ['info' => '对不起，数据已收集完成！']);
            }
        }
        if($this->password !='' && !session('wj_pass')){
            return  redirect('/api/m/'.$this->route.'_pass?token='.$this->token);
        }

        if ($this->request->isPost()) {
            $data = $this->request->post();
            return sfdp::Sfdp_save($data,'',$this->s_sys_check);
        }
        return view(root_path().'/app/api/view/show.html', ['content'=>$this->content,'showtype' => 'add','title'=>(json_decode($this->g_data,true))['name'],'config' => $this->g_config, 'data' => $this->g_data]);
    }
    public function pass(){
        if ($this->request->isPost()) {
        $pass = input('password');
        if($pass==$this->password){
            Session::set('wj_pass', true);
            return msg_return('/api/m/'.$this->route.'?token='.$this->token);
                }else{
            return msg_return('对不起,您输入的密码错误！',1);
            }
        }
        return view(root_path().'/app/api/view/check_pass.html',['content'=>$this->content,'title'=>(json_decode($this->g_data,true))['name']]);
    }
}
?>