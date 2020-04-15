<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:49
 */

namespace App\WebSocket\Actions\User;

use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class UserInPop extends ActionPayload
{
    protected $action = WebSocketAction::POP_USER_IN;
    protected $actionDesc = WebSocketAction::POP_USER_IN_TEXT;
    protected $info;

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info): void
    {
        $this->info = $info;
    }
}