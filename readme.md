

#### 业务描述

- 先暂时table是一个100*100大小的范围，每个table也就是一个二位数组表示

- url地址里有个tableId代表当前table，如果用户打开时没有，那么我们随机生成一个（时间戳拼上随机数），该tableId作为tables的key

- 用户都应该有个昵称，不能为空

- server会广播同一个table里各种事件给该table下所有用户：某用户选中某个cell、某用户确定修改某个cell

- server能够返回某个table当前table的所有cell数据给用户

- server能够返回某个table当前所有用户状态数据给用户

- 前端能够渲染整个table，能够对收到对广播事件处理

- 前端会发出事件：某用户选中某个cell、某用户确定修改某个cell



#### 数据结构

$tables = [
    "tableId001": [
        [cell01, cell02, ..., cell100],
        [cell01, cell02, ..., cell100],
    ]
];

$cell = "value01"

$tableUserStatus = [
    "tableId001": [
        "nickname01": [
            "cellPosition": [1, 11]
        ],
    ]
];


#### 运行

代码基于Swoole，目前也只是依赖Swoole，你先要给PHP添加Swoole拓展，我目前PHP版本是7.1，Swoole版本是4.0.3

运行：

```php server.php```


浏览器访问 `localhost:9501`体验

#### TODO

优化广播，不应该全局广播，导致客户端自己筛选tableId


#### License

MIT
