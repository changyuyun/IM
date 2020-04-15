<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:49
 */

namespace App\WebSocket\Actions\Chat;

use App\WebSocket\Actions\ActionPayload;
use App\WebSocket\WebSocketAction;

/**
 * 广场聊天消息
 * Class SquareMessage
 * @package App\WebSocket\Actions\Chat
 */
class SquareMessage extends ActionPayload
{
    protected $action = WebSocketAction::SQUARE_MESSAGE;
    protected $actionDesc = WebSocketAction::SQUARE_MESSAGE_TEXT;
    protected $fromUserFd;
    protected $content;
    protected $type;
    protected $sendTime;

    /**
     * @return mixed
     */
    public function getFromUserFd()
    {
        return $this->fromUserFd;
    }

    /**
     * @param mixed $fromUserFd
     */
    public function setFromUserFd($fromUserFd): void
    {
        $this->fromUserFd = $fromUserFd;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @param mixed $content
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @param mixed $content
     */
    public function setSendTime($sendTime): void
    {
        $this->sendTime = $sendTime;
    }
}