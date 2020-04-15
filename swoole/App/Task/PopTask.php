<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-11-28
 * Time: 20:23
 */

namespace App\Task;

use App\Storage\OnlinePopUsers;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;
use App\WebSocket\WebSocketAction;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Task\AbstractInterface\TaskInterface;

/**
 * 发送点对点消息
 * Class BroadcastTask
 * @package App\Task
 */
class PopTask implements TaskInterface
{
    protected $taskData;

    protected $fd = 0;

    public function __construct($taskData, $fd = 0)
    {
        $this->taskData = $taskData;
        $this->fd = $fd;
    }


    /**
     * 执行投递
     * @param $taskData
     * @param $taskId
     * @param $fromWorkerId
     * @param $flags
     * @return bool
     */
    function run(int $taskId, int $workerIndex)
    {
        $taskData = $this->taskData;
        /** @var \swoole_websocket_server $server */
        $server = ServerManager::getInstance()->getSwooleServer();
        if ($this->fd == 0) {
            foreach (OnlinePopUsers::getInstance()->table() as $userFd => $userInfo) {
                $connection = $server->connection_info($userFd);
                if ($connection['websocket_status'] == 3) {  // 用户正常在线时可以进行消息推送
                    $server->push($userInfo['fd'], $taskData['payload']);
                }
            }
        } else {
            $server->push($this->fd, $taskData['payload']);
        }
        return true;
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        throw $throwable;
    }

}