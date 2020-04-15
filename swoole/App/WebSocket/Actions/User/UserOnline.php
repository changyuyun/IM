<?php
/**
 * Created by PhpStorm.
 * User: ityun
 * Date: 2018-12-02
 * Time: 01:49
 */

namespace App\WebSocket\Actions\User;

use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

class UserOnline extends ActionPayload
{
    protected $action = WebSocketAction::SQUARE_USER_LIST;
    protected $actionDesc = WebSocketAction::SQUARE_USER_LIST_TEXT;
    protected $list;

    public function __construct($channel = 1)
    {
        if ($channel == 1) {
            $this->action = WebSocketAction::SQUARE_USER_LIST;
            $this->actionDesc = WebSocketAction::SQUARE_USER_LIST_TEXT;
        } elseif ($channel == 2) {
            $this->action = WebSocketAction::POP_USER_LIST;
            $this->actionDesc = WebSocketAction::POP_USER_LIST_TEXT;
        } else {
            $this->action = WebSocketAction::SERV_USER_LIST;
            $this->actionDesc = WebSocketAction::SERV_USER_LIST_TEXT;
        }
    }
    /**
     * @return mixed
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param mixed $list
     */
    public function setList($list): void
    {
        $this->list = $list;
    }


}