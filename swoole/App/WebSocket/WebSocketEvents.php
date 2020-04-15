<?php

namespace App\WebSocket;

use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Utility\Random;
use App\Storage\OnlineSquareUsers;
use App\Storage\OnlinePopUsers;
use App\Storage\OnlineServUsers;
use App\WebSocket\Actions\User\UserInSquare;
use App\WebSocket\Actions\User\UserOutSquare;
use App\WebSocket\Actions\User\UserInPop;
use App\WebSocket\Actions\User\UserOutPop;
use App\Task\SquareTask;
use App\Task\PopTask;
use App\Utility\Gravatar;
use App\WebSocket\Actions\Tool\UserPusher;

use \swoole_server;
use \swoole_websocket_server;
use \swoole_http_request;
use \Exception;

/**
 * WebSocket Events
 * Class WebSocketEvents
 * @package App\WebSocket
 */
class WebSocketEvents
{
    /**
     * 打开了一个链接
     * @param swoole_websocket_server $server
     * @param swoole_http_request $request
     */
    static function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {
        // 为用户分配身份并插入到用户表
        $fd = $request->fd;
        $info = $server->connection_info($fd);
        echo "------------------open:{$fd}--------------------------\n";
        print_r($info);
        // 输入的渠道
        if (isset($request->get['channel']) && !empty($request->get['channel'])) {
            $channel = $request->get['channel'];
        } else {
            $channel = 1;
        }
        if (isset($request->get['username']) && !empty($request->get['username'])) {
            $username = $request->get['username'];
            $avatar = Gravatar::makeGravatar($username . '@ityun.com');
        } else {
            $random = Random::character(8);
            $avatar = Gravatar::makeGravatar($random . '@ityun.com');
            $username = 'ITYUN' . $random;
        }
        //根据不同的渠道，存储在线用户
        if ($channel == 1) { //广场聊天
            // 插入广场在线用户表
            OnlineSquareUsers::getInstance()->set($fd, $username, $avatar, $channel);
            $currentUser = OnlineSquareUsers::getInstance()->get($fd);
            // 发送广播告诉广场里的用户 有新用户上线
            $userInSquareMessage = new UserInSquare;
            $userInSquareMessage->setInfo(['fd' => $fd, 'avatar' => $avatar, 'username' => $username]);
            TaskManager::getInstance()->async(new SquareTask(['payload' => $userInSquareMessage->__toString(), 'fromFd' => $fd]));
        } elseif ($channel == 2) { //点对点聊天
            OnlinePopUsers::getInstance()->set($fd, $username, $avatar, $channel);
            $currentUser = OnlinePopUsers::getInstance()->get($fd);
            $userInPopMessage = new UserInPop;
            $userInPopMessage->setInfo(['fd' => $fd, 'avatar' => $avatar, 'username' => $username]);
            TaskManager::getInstance()->async(new PopTask(['payload' => $userInPopMessage->__toString(), 'fromFd' => $fd]));
        } elseif ($channel == 3) { //客服系统
            OnlineServUsers::getInstance()->set($fd, $username, $avatar, $channel);
            $currentUser = OnlineServUsers::getInstance()->get($fd);
        } else { //广场聊天(默认)
            // 插入广场在线用户表
            OnlineSquareUsers::getInstance()->set($fd, $username, $avatar, $channel);
            $currentUser = OnlineSquareUsers::getInstance()->get($fd);
            // 发送广播告诉广场里的用户 有新用户上线
            $userInSquareMessage = new UserInSquare;
            $userInSquareMessage->setInfo(['fd' => $fd, 'avatar' => $avatar, 'username' => $username]);
            TaskManager::getInstance()->async(new SquareTask(['payload' => $userInSquareMessage->__toString(), 'fromFd' => $fd]));
        }
        // 向当前用户推送个人信息
        UserPusher::currentUserPush($currentUser);

    }

    /**
     * 链接被关闭时
     * @param swoole_server $server
     * @param int $fd
     * @param int $reactorId
     * @throws Exception
     */
    static function onClose(\swoole_server $server, int $fd, int $reactorId)
    {
        $info = $server->connection_info($fd);
        echo "------------------close:{$fd}--------------------------\n";
        print_r($info);
        if (isset($info['websocket_status']) && $info['websocket_status'] !== 0) {

            // 移除用户并广播告知 可以根据能否查出来数据做分渠道通知
            if (OnlineSquareUsers::getInstance()->get($fd)) {
                OnlineSquareUsers::getInstance()->delete($fd);
                $message = new UserOutSquare;
                $message->setUserFd($fd);
                TaskManager::getInstance()->async(new SquareTask(['payload' => $message->__toString(), 'fromFd' => $fd]));
            }

            if (OnlinePopUsers::getInstance()->get($fd)) {
                OnlinePopUsers::getInstance()->delete($fd);
                $message = new UserOutPop;
                $message->setUserFd($fd);
                TaskManager::getInstance()->async(new PopTask(['payload' => $message->__toString(), 'fromFd' => $fd]));
            }

            if (OnlineServUsers::getInstance()->get($fd)) {
                OnlineServUsers::getInstance()->delete($fd);
            }
        }
    }
}
