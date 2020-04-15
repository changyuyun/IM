<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:54
 */

namespace App\WebSocket\Controller;

use App\Storage\OnlineSquareUsers;
use App\Storage\OnlinePopUsers;
use App\Storage\OnlineServUsers;;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Client\WebSocket as WebSocketClient;
use Exception;

/**
 * 基础控制器
 * Class Base
 * @package App\WebSocket\Controller
 */
class Base extends Controller
{
    /**
     * 获取当前的渠道
     * @return int
     * @throws Exception
     */
    protected function currentChannel()
    {
        $args = $this->caller()->getArgs();
        return $args['channel'];
    }

    /**
     * 获取当前的用户
     * @return array|string
     * @throws Exception
     */
    protected function currentUser()
    {
        /** @var WebSocketClient $client */
        $client = $this->caller()->getClient();
        $args = $this->caller()->getArgs();
        $channel = $args['channel'];
        if ($channel == 1) { //广场聊天
            $currentUser = OnlineSquareUsers::getInstance()->get($client->getFd());
        } elseif ($channel == 2) { //点对点聊天
            $currentUser = OnlinePopUsers::getInstance()->get($client->getFd());
        } elseif ($channel == 3) { //客服系统
            $currentUser = OnlineServUsers::getInstance()->get($client->getFd());
        } else { //广场聊天(默认)
            // 插入广场在线用户表
            $currentUser = OnlineSquareUsers::getInstance()->get($client->getFd());
        }
        return $currentUser;
    }

    protected function currentSysUserTable()
    {
        $client = $this->caller()->getClient();
        $args = $this->caller()->getArgs();
        $channel = $args['channel'];
        if ($channel == 1) { //广场聊天
            $table = OnlineSquareUsers::getInstance()->table();
        } elseif ($channel == 2) { //点对点聊天
            $table = OnlinePopUsers::getInstance()->table();
        } elseif ($channel == 3) { //客服系统
            $table = OnlineServUsers::getInstance()->table();
        } else { //广场聊天(默认)
            $table = OnlineSquareUsers::getInstance()->table();
        }
        return $table;
    }

    /**
     * @desc 响应json数据
     * @param $data
     * @return false|string
     */
    public static function responseData($data)
    {
        if (is_string($data)) {
            return json_encode(['content' => $data], JSON_UNESCAPED_UNICODE);
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}