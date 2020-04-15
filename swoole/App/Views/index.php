<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div>
    <div>
        <p>渠道页面</p>
        <ul  id="line">
        </ul>
    </div>
    <div>
        <select id="channel">
            <option value="0">===请选择===</option>
            <option value="1">聊天广场</option>
            <option value="2">普通点对点聊天</option>
            <option value="3">客服点对点聊天</option>
        </select>
        <button onclick="into()">进入</button>
    </div>
</div>
</body>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
    function into() {
        var channel = $("#channel").val();
        if (channel == '0'){
            alert('请先选择模式');
            return false;
        }
        location.href = '/WebSocket/index?channel='+channel;
    }
</script>
</html>