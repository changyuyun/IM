<?php
/**
 * Created by PhpStorm.
 * User: ityun
 * Date: 2018-12-02
 * Time: 01:49
 */

namespace App\WebSocket\Actions\Tool;

use EasySwoole\EasySwoole\ServerManager;
use App\WebSocket\WebSocketAction;

/**
 *
 * 用户个人信息推送
 * Class UserInfo
 * @package App\WebSocket\Actions\User
 */
class UserPusher
{
    /**
     * @desc 打开连接后的用户信息推送
     * @param $userInfo
     * @return void
     */
    public static function currentUserPush($userInfo)
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $pushData = [
            'action' => WebSocketAction::SYS_USER_INFO,
            'username' => $userInfo['username'],
            'userFd' => $userInfo['fd'],
            'avatar' => $userInfo['avatar'],
            'channel' => $userInfo['channel'],
            'identity' => WebSocketAction::IDENTITY_USER
        ];
        $server->push($userInfo['fd'], json_encode($pushData, JSON_UNESCAPED_UNICODE));
    }

}