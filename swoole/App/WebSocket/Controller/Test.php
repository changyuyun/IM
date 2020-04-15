<?php
namespace App\WebSocket\Controller;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;
/**
 * Class Index
 * 此类是一些基础服务类，
 * */
class Test extends Base
{
    /**
     * @desc 测试通讯
     */
    public function hello()
    {
        $arg = $this->caller()->getArgs();
        $data = ['arg' => $arg];
        $this->response()->setMessage(self::responseData($data));
    }

    /**
     * @desc 获取通工id
     */
    public function who()
    {
        $fd = $this->caller()->getClient()->getFd();
        $data = ['fd' => $fd];
        $this->response()->setMessage(self::responseData($data));
    }

    /**
     * @desc 延迟
     */
    public function delay()
    {
        $this->response()->setMessage('this is delay action');
        $client = $this->caller()->getClient();

        // 异步推送, 这里直接 use fd也是可以的
        TaskManager::getInstance()->async(function () use ($client){
            $server = ServerManager::getInstance()->getSwooleServer();
            $i = 0;
            while ($i < 5) {
                sleep(1);
                $server->push($client->getFd(),'push in http at '. date('H:i:s'));
                $i++;
            }
        });
    }
}