<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>点对点聊天</title>
    <link rel="stylesheet" href="https://cdn.staticfile.org/amazeui/2.7.2/css/amazeui.min.css">
    <link rel="stylesheet" href="https://cdn.staticfile.org/layer/2.3/skin/layer.css">
    <link rel="stylesheet" href="/css/main.css?v=120203">
    <script src="https://cdn.staticfile.org/vue/2.5.17-beta.0/vue.js"></script>
    <script src="https://cdn.staticfile.org/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.org/layer/2.3/layer.js"></script>
</head>
<body>
<div id="chat">
    <template>
        <div class="online_window">
            <div class="me_info">
                <div class="me_item">
                    <div class="me_avatar">
                        <img :src="currentUser.avatar" alt="">
                    </div>
                    <div class="me_status">
                        <div class="me_username">
                             {{currentUser.username}}
                        </div>
                        <div class="me_income">{{currentUser.intro}}</div>
                    </div>
                    <div class="times-icon"><i class="am-icon am-icon-times"></i></div>
                </div>
            </div>
            <div class="online_list">
                <div class="online_list_header">在线用户</div>
                <div class="online_item" v-for="user in roomUser" v-if="user && (currentUser.fd != user.fd)" :data-fd="user.fd">
                    <!--在线列表排除自己-->
                    <template>
                        <i class="am-icon am-icon-check"></i>
                        <div class="online_avatar">
                            <img :src="user.avatar" alt="">
                        </div>
                        <div class="online_status">
                            <div class="online_username">{{user.username}}</div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="online_count">
                <h6>在线数 <span>{{currentCount}}</span></h6>
            </div>
        </div>
        <div class="talk_window">
            <div class="windows_top">
                <div class="windows_top_left"><i class="am-icon am-icon-list online-list"></i> 点对点聊天系统</div>
                <div class="windows_top_right">
                    <a href="#" target="_blank"
                       style="color: #999">ityun</a>
                </div>
            </div>
            <div class="windows_body" id="chat-window" v-scroll-bottom>
                <ul class="am-comments-list am-comments-list-flip">
                    <template v-for="chat in roomChat">
                        <template v-if="chat.type === 'tips'">
                            <div class="chat-tips">
                                <span class="am-badge am-badge-primary am-radius">{{chat.content}}</span></div>
                        </template>
                        <template v-else>
                            <div v-if="chat.sendTime" class="chat-tips">
                                <span class="am-radius" style="color: #666666">{{chat.sendTime}}</span>
                            </div>
                            <article class="am-comment" :class="{ 'am-comment-flip' : chat.fd == currentUser.userFd }">
                                <a href="#link-to-user-home">
                                    <img :src="chat.avatar" alt="" class="am-comment-avatar"
                                         width="48" height="48"/>
                                </a>
                                <div class="am-comment-main">
                                    <header class="am-comment-hd">
                                        <div class="am-comment-meta">
                                            <a href="#link-to-user" class="am-comment-author">{{chat.username}}</a>
                                        </div>
                                    </header>
                                    <div class="am-comment-bd">
                                        <div class="bd-content">
                                            <template v-if="chat.type === 'text'">
                                                {{chat.content}}
                                            </template>
                                            <template v-else-if="chat.type === 'image'">
                                                <img :src="chat.content" width="100%">
                                            </template>
                                            <template v-else>
                                                {{chat.content}}
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </template>
                </ul>
            </div>
            <div class="windows_input">
                <div class="am-btn-toolbar">
                    <div class="am-btn-group am-btn-group-xs">
                        <button type="button" class="am-btn"><i class="am-icon am-icon-picture-o"></i>
                        </button>
                        <input type="file" id="fileInput" style="display: none" accept="image/*">
                    </div>
                </div>
                <div class="input-box">
                    <label for="text-input" style="display: none"></label>
                    <textarea name="" id="text-input" cols="30" rows="10" title=""></textarea>
                </div>
                <div class="toolbar">
                    <div class="left"><a href="#" target="_blank">POWER BY Ityun Technology</a>
                    </div>
                    <div class="right">
                        <button class="send" @click="clickBtnSend">发送消息 ( Enter )</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
<script>
    var Vm = new Vue({
        el        : '#chat',
        data      : {
            websocketServer  : "ws://111.229.127.18:9501?channel=2",
            websocketInstance: undefined,
            Reconnect        : false,
            ReconnectTimer   : null,
            HeartBeatTimer   : null,
            ReconnectBox     : null,
            currentUser      : {username: '-----', intro: '-----------', fd: 0, avatar: 0},
            roomUser         : [],
            roomChat         : [],
            up_recv_time     : 0
        },
        created:function () {
            this.connect();
        },
        mounted:function () {
            var othis = this;
            var textInput = $("#text-input");
            textInput.on('keydown', function (ev) {
                if (ev.keyCode == 13 && ev.shiftKey) {
                    textInput.val(textInput.val() + "\n");
                    return false;
                } else if (ev.keyCode == 13) {
                    othis.clickBtnSend();
                    ev.preventDefault();
                    return false;
                }
            });
        },
        methods: {
            connect: function () {
                var othis = this;
                var websocketServer = this.websocketServer;
                this.websocketInstance = new WebSocket(websocketServer);
                this.websocketInstance.onopen = function (ev) {
                    // 断线重连处理
                    if (othis.ReconnectBox) {
                        layer.close(othis.ReconnectBox);
                        othis.ReconnectBox = null;
                        clearInterval(othis.ReconnectTimer);
                    }
                    // 前端循环心跳 (1min)
                    othis.HeartBeatTimer = setInterval(function () {
                        othis.websocketInstance.send('PING');
                    }, 1000 * 30);
                    //主动获取在线列表
                    othis.release("Index", "online");
                    /*接收到消息*/
                    othis.websocketInstance.onmessage = function (ev) {
                        try {
                            var data = JSON.parse(ev.data);
                            switch (data.action) {
                                case 10001: {
                                    //刷新自己的信息
                                    othis.currentUser.intro = "ityun technology";
                                    othis.currentUser.avatar = data.avatar;
                                    othis.currentUser.fd = data.userFd;
                                    othis.currentUser.username = data.username;
                                    break;
                                }
                                case 202: {
                                    //刷新在线列表
                                    othis.roomUser = data.list;
                                    break;
                                }
                                case 204: {
                                    //新用户上线
                                    var info = {
                                        avatar:data.info.avatar,
                                        fd:data.info.fd,
                                        username:data.info.username,
                                        channel:2
                                    };
                                    othis.$set(othis.roomUser, "ityun-" + data.info.fd, info)
                                }
                                case 205: {
                                    //用户离线
                                    othis.$delete(othis.roomUser, 'ityun-' + data.userFd);
                                }
                            }
                        } catch (e) {
                            console.warn(e);
                        }
                    };
                    othis.websocketInstance.onclose = function (ev) {
                        othis.doReconnect();
                    };
                    othis.websocketInstance.onerror = function(ev) {
                        othis.doReconnect();
                    };
                }
            },
            doReconnect : function () {
                var othis = this;
                clearInterval(othis.HeartBeatTimer);
                othis.ReconnectBox = layer.msg('已断开，正在重连...', {
                    scrollbar : false,
                    shade     : 0.3,
                    shadeClose: false,
                    time      : 0,
                    offset    : 't'
                });
                othis.ReconnectTimer = setInterval(function () {
                    othis.connect();
                }, 1000)
            },
            /**
             * 向服务器发送消息
             * @param cmd 请求控制器
             * @param action 请求操作方法
             */
            release : function (cmd, action) {
                cmd = cmd || 'Index';
                action = action || 'index';
                channel = 2;
                var message = {cmd: cmd, action: action, channel: channel};
                this.websocketInstance.send(JSON.stringify(message))
            },
            release_chat : function(cmd, action, channel, content, toUserFd, type) {
                cmd = cmd || 'Index';
                action = action || 'index';
                var message = {cmd: cmd, action: action, channel: channel, content:content, toUserFd:toUserFd, type, type};
                this.websocketInstance.send(JSON.stringify(message))
            },
            /**
             * 发送文本消息
             * @return void
             */
            clickBtnSend : function () {
                var textInput = $("#text-input");
                var content = textInput.val();
                if (content.trim() != '') {
                    if (this.websocketInstance && this.websocketInstance.readyState === 1) {
                        this.sendTextMessage(content);
                        textInput.val('');
                    } else {
                        layer.tips('连接已断开', '.windows_input', {
                            tips: [1, '#ff4f4f'],
                            time: 2000
                        });
                    }

                } else {
                    layer.tips('请输入消息内容', '.windows_input', {
                        tips: [1, '#3595CC'],
                        time: 2000
                    });
                }
            },
            /**
             * 发送文本消息
             * @param content
             */
            sendTextMessage : function (content) {
                //TODO:实现获取已选取的用户
                this.release_chat("PopChat", "chat", 2, content, 17, "text");
            }
        },
        computed: {
            currentCount() {
                return Object.getOwnPropertyNames(this.roomUser).length - 1;
            }
        },
        directives: {
            scrollBottom: {
                componentUpdated: function (el) {
                    el.scrollTop = el.scrollHeight
                }
            }
        }
    });
</script>
</body>
</html>