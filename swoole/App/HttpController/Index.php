<?php


namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Component\TableManager;

class Index extends Base
{
    /**
     * @desc 渠道进入页
     */
    function index()
    {
        $this->render('index', []);
    }

    /**
     * @desc 打印当前在线用户
     */
    function test()
    {
        echo "--------------------OnlineSquareUsers----------------------\n";
        $table = TableManager::getInstance()->get('OnlineSquareUsers');
        foreach ($table as $userFd => $userInfo) {
            var_dump($userInfo);
        }
        echo "--------------------OnlinePopUsers----------------------\n";
        $table = TableManager::getInstance()->get('OnlinePopUsers');
        foreach ($table as $userFd => $userInfo) {
            var_dump($userInfo);
        }
        echo "--------------------OnlineServUsers----------------------\n";
        $table = TableManager::getInstance()->get('OnlineServUsers');
        foreach ($table as $userFd => $userInfo) {
            var_dump($userInfo);
        }
        $this->response()->write("test!!!");
    }

    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT.'/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
        if(!is_file($file)){
            $file = EASYSWOOLE_ROOT.'/src/Resource/Http/404.html';
        }
        $this->response()->write(file_get_contents($file));
    }
}
