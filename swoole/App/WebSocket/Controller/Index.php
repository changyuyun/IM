<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:19
 */

namespace App\WebSocket\Controller;

use App\Storage\OnlineUser;
use App\WebSocket\Actions\User\UserInfo;
use App\WebSocket\Actions\User\UserOnline;
use Exception;

class Index extends Base
{
    /**
     * 主动获取当前用户信息
     * @throws Exception
     */
    function info()
    {
        $info = $this->currentUser();
        if ($info) {
            $message = new UserInfo($this->currentChannel());
            $message->setIntro('用户信息');
            $message->setUserFd($info['fd']);
            $message->setAvatar($info['avatar']);
            $message->setUsername($info['username']);
            $this->response()->setMessage($message);
        }
    }

    /**
     * 主动获取在线用户列表
     * @throws Exception
     */
    function online()
    {
        $table = $this->currentSysUserTable();
        $users = array();

        foreach ($table as $user) {
            $users["ityun-".$user['fd']] = $user;
        }
        print_r($users);
        if (!empty($users)) {
            $message = new UserOnline($this->currentChannel());
            $message->setList($users);
            $this->response()->setMessage($message);
        }
    }

    function heartbeat()
    {
        $this->response()->setMessage('PONG');
    }
}