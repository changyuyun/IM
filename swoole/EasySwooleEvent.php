<?php
namespace EasySwoole\EasySwoole;

use App\Storage\OnlineSquareUsers;
use App\Storage\OnlinePopUsers;
use App\Storage\OnlineServUsers;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Socket\Dispatcher;
use App\WebSocket\WebSocketParser;
use App\WebSocket\WebSocketEvents;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register):void
    {
        /*
         * **************** websocket控制器 **********************
         * */
        $server = ServerManager::getInstance()->getSwooleServer();

        // 注册服务事件
        $register->add(EventRegister::onOpen, [WebSocketEvents::class, 'onOpen']);
        $register->add(EventRegister::onClose, [WebSocketEvents::class, 'onClose']);

        OnlineSquareUsers::getInstance();
        OnlinePopUsers::getInstance();
        OnlineServUsers::getInstance();
        // 收到用户消息时处理
        $conf = new \EasySwoole\Socket\Config();
        $conf->setType(\EasySwoole\Socket\Config::WEB_SOCKET);
        $conf->setParser(new WebSocketParser());
        $dispatch = new Dispatcher($conf);
        $register->set(EventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame->data, $frame);
        });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // 可实现一些鉴权逻辑
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // 可在该事件中做trace 进行请求的追踪监视,以及获取此次的响应内容
    }
}