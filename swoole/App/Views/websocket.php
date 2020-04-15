<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div>
    <div>
        <p>info below</p>
        <ul id="line">
        </ul>
    </div>
    <div>
        <select id="cmd">
            <option value="Test">Test</option>
            <option value="Index">Index</option>
            <option value="SquareChat">SquareChat</option>
            <option value="PopChat">PopChat</option>
        </select>
        <select id="action">
            <option value="who">test-who（测试获取fd）</option>
            <option value="hello">test-hello（测试通信）</option>
            <option value="delay">test-delay（测试断开连接）</option>
            <option value="404">test-404（测试404）</option>
            <option value="info">current-user-info（主动获取当前用户信息）</option>
            <option value="online">current-sys-online-user-list（主动获取当前渠道在线用户信息）</option>
            <option value="chat">square-chat（广场聊天）</option>
            <option value="chat">pop-chat（点对点聊天）</option>
        </select>
        <select id="toUserFd">
            <option value="0">==广场用户==</option>
        </select>
        <select id="type">
            <option value="text">text</option>
        </select>
        <input type="text" id="content">
        <button onclick="say()">发送</button>
    </div>
</div>
</body>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
    var channel = "<?= $channel ?>"
    var wsServer = "<?= $server ?>"+'?channel='+channel;
    var websocket = new WebSocket(wsServer);
    window.onload = function () {
        websocket.onopen = function (evt) {
            addLine("Connected to WebSocket server.");
        };
        websocket.onclose = function (evt) {
            addLine("Disconnected");
        };
        websocket.onmessage = function (evt) {
            var data = JSON.parse(evt.data);
            console.log(data);
            addLine('Retrieved data from server: ' + evt.data);
            squareHtml(data); //广场
            popHtml(data); //点对点
        };
        websocket.onerror = function (evt, e) {
            addLine('Error occured: ' + evt.data);
        };
    };
    // 广场
    function squareHtml(data) {
        switch (data.action) {
            case 102: {
                var options = '<option value="0">==广场用户==</option>';
                for (var i=0; i< data['list'].length; i++) {
                    options += "<option value='"+data['list'][i]['fd']+"'>"+data['list'][i]['fd']+'--'+data['list'][i]['username']+"</option>"
                }
                $("#toUserFd").html(options);
            }
            case 104: {
                var options = "<option value='"+data['info']['fd']+"'>"+data['info']['fd']+'--'+data['info']['username']+"</option>";
                $("#toUserFd").append(options);
            }
            case 105:{
                var fd = data.userFd;
                $("#toUserFd option[value='"+fd+"']").remove();
            }
        }
    }

    // 点对点
    function popHtml(data) {
        switch (data.action) {
            case 202: {
                var options = '<option value="0">==点对点用户==</option>';
                for (var i=0; i< data['list'].length; i++) {
                    options += "<option value='"+data['list'][i]['fd']+"'>"+data['list'][i]['fd']+'--'+data['list'][i]['username']+"</option>"
                }
                $("#toUserFd").html(options);
            }
            case 204: {
                var options = "<option value='"+data['info']['fd']+"'>"+data['info']['fd']+'--'+data['info']['username']+"</option>";
                $("#toUserFd").append(options);
            }
            case 205:{
                var fd = data.userFd;
                $("#toUserFd option[value='"+fd+"']").remove();
            }
        }
    }

    function addLine(data) {
        $("#line").append("<li>-------------------------------------</li>");
        $("#line").append("<li>"+data+"</li>");
    }
    function say() {
        var content = $("#content").val();
        var type = $("#type").val();
        var toUserFd = $("#toUserFd").val();
        var cmd = $("#cmd").val();
        var action = $("#action").val();
        //$("#content").val('');
        websocket.send(JSON.stringify({
            channel:channel,
            cmd:cmd,
            action:action,
            content:content,
            type:type,
            toUserFd:toUserFd
        }));
    }
</script>
</html>