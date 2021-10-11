<?php
/**
 *+------------------
 * Gadmin 3.0 企业级开发平台
 *+------------------
 */
namespace app\api\controller\m;

use app\api\controller\Data as Api;
use app\common\server\Sfdp;

class [class]  extends Api{
    public function index()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            return sfdp::Sfdp_save($data);
        }
        return view(root_path().'/app/api/view/show.html', ['showtype' => 'add','title'=>(json_decode($this->g_data,true))['name'],'config' => $this->g_config, 'data' => $this->g_data]);
    }
}
?>