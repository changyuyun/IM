<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:19
 */

namespace App\WebSocket\Controller;

use App\Task\PopTask;
use App\WebSocket\Actions\Chat\PopMessage;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;

class PopChat extends Base
{
    /**
     * 发送消息给目标用户
     * @throws \Exception
     */
    public function chat()
    {
        /** @var WebSocketClient $client */
        $client = $this->caller()->getClient();
        $popPayload = $this->caller()->getArgs();
        if (!empty($popPayload) && isset($popPayload['content'])) {
            $toUserFd = $popPayload['toUserFd'];
            $message = new PopMessage;
            $message->setFromUserFd($client->getFd());
            $message->setContent($popPayload['content']);
            $message->setType($popPayload['type']);
            $message->setSendTime(date('Y-m-d H:i:s'));
            TaskManager::getInstance()->async(new PopTask(['payload' => $message->__toString(), 'fromFd' => $client->getFd()], $toUserFd));
        }
        $this->response()->setStatus($this->response()::STATUS_OK);
    }
}