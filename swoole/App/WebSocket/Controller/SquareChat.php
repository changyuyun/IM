<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:19
 */

namespace App\WebSocket\Controller;

use App\Task\SquareTask;
use App\WebSocket\Actions\Chat\SquareMessage;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;

class SquareChat extends Base
{
    /**
     * 发送消息给广场内的所有人
     * @throws \Exception
     */
    public function chat()
    {
        /** @var WebSocketClient $client */
        $client = $this->caller()->getClient();
        $squarePayload = $this->caller()->getArgs();
        if (!empty($squarePayload) && isset($squarePayload['content'])) {
            $message = new SquareMessage;
            $message->setFromUserFd($client->getFd());
            $message->setContent($squarePayload['content']);
            $message->setType($squarePayload['type']);
            $message->setSendTime(date('Y-m-d H:i:s'));
            TaskManager::getInstance()->async(new SquareTask(['payload' => $message->__toString(), 'fromFd' => $client->getFd()]));
        }
        $this->response()->setStatus($this->response()::STATUS_OK);
    }
}