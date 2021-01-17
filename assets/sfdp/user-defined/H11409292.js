function load_end_fun(){
    //进行权限校验
   if(g_uid==9999){
    layer.msg('sorry，您没有此阅读权限！');
     layer.close();
    }
}