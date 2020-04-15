<?php
/**
 * Created by PhpStorm.
 * User: ityun
 * Date: 2018-12-02
 * Time: 01:44
 */

namespace App\WebSocket;

class WebSocketAction
{
    // 100xx USER 系统用户类消息
    const SYS_USER_INFO = 10001;         // 刷新自己的用户信息
    const SYS_USER_ONLINE = 10002;       // （刷新）在线列表

    // 1xx 广场聊天类消息
    const SQUARE_USER_INFO = 101;  // 刷新自己的用户信息
    const SQUARE_USER_INFO_TEXT = '刷新自己的用户信息';  // 刷新自己的用户信息
    const SQUARE_USER_LIST = 102; //（刷新）在线列表
    const SQUARE_USER_LIST_TEXT = '刷新在线列表'; //（刷新）在线列表
    const SQUARE_MESSAGE = 103; //收到用户消息
    const SQUARE_MESSAGE_TEXT = '收到用户消息'; //收到用户消息
    const SQUARE_USER_IN = 104; // 新用户上线
    const SQUARE_USER_IN_TEXT = '新用户上线'; // 新用户上线
    const SQUARE_USER_OUT = 105; // 用户离线
    const SQUARE_USER_OUT_TEXT = '用户离线'; // 用户离线

    // 2xx 点对点聊天类消息
    const POP_USER_INFO = 201;  // 刷新自己的用户信息
    const POP_USER_INFO_TEXT = '刷新自己的用户信息';  // 刷新自己的用户信息
    const POP_USER_LIST = 202; //（刷新）在线列表
    const POP_USER_LIST_TEXT = '刷新在线列表'; //（刷新）在线列表
    const POP_MESSAGE = 203; //收到用户消息
    const POP_MESSAGE_TEXT = '收到用户消息'; //收到用户消息
    const POP_USER_IN = 204; // 新用户上线
    const POP_USER_IN_TEXT = '新用户上线'; // 新用户上线
    const POP_USER_OUT = 205; // 用户离线
    const POP_USER_OUT_TEXT = '用户离线'; // 用户离线

    // 3xx 客服类聊天消息
    const SERV_USER_INFO = 301;  // 刷新自己的用户信息
    const SERV_USER_INFO_TEXT = '刷新自己的用户信息';  // 刷新自己的用户信息
    const SERV_USER_LIST = 302; //（刷新）在线列表
    const SERV_USER_LIST_TEXT = '刷新在线列表'; //（刷新）在线列表

    // 身份
    const IDENTITY_USER = 1001; //普通用户
    const IDENTITY_SERVER = 2001; //客服

}