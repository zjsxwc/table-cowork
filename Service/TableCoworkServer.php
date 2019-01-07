<?php
/**
 * Created by PhpStorm.
 * User: wangchao
 * Date: 05/01/2019
 * Time: 9:51 AM
 */

namespace Service;

use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

class TableCoworkServer
{

    const CODE_SUCCESS = -1;
    const CODE_ERROR = 0;

    private function arrayGet($array, $key)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }
        return null;
    }

    /**
     * @param string $message
     * @param int[] $ignoreFdList
     */
    public function broadcast($message, $ignoreFdList = [])
    {
        foreach ($this->server->connections as $fd) {
            if (in_array($fd, $ignoreFdList)) {
                continue;
            }
            if (in_array($fd, $this->httpFdList)) {
                continue;
            }
            $this->server->push($fd, $message);
        }
    }

    /** @var int[]  */
    private $httpFdList = [];

    /** @var Server */
    public $server;
    public $serverIp;
    public $serverPort;

    /** @var TablesManager */
    public $tablesManager;

    public function __construct($ip = "0.0.0.0", $port = 9501)
    {
        $this->serverIp = $ip;
        $this->serverPort = $port;

        $this->tablesManager = new TablesManager(new TablesUserStatusManager());

        $this->server = new Server($this->serverIp, $this->serverPort);
        $this->server->on('open', function (Server $server, $request) {
            echo "webSocket Fd $request->fd \n";

            echo "server: handshake success with fd{$request->fd}\n";
        });
        $this->server->on('message', function (Server $server, $frame) {
            echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
            $server->push($frame->fd, json_encode(["action" => "messageEcho", "message" => $frame->data]));
        });
        $this->server->on('close', function (Server $server, $fd) {
            echo "client {$fd} closed\n";
        });
        $this->server->on('request', function (Request $request, Response $response) {


            $this->httpFdList[] = $request->fd;
            echo "http Fd $request->fd \n";

            if ($request->server['request_uri'] == '/favicon.ico') {
                return;
            }

            $tableId = $this->arrayGet($request->get, 'tableId');
            $tableId = intval($tableId);
            if (!$tableId) {
                $tableId = $this->tablesManager->createTable($tableId);
                $response->redirect("?tableId=" . $tableId);
                return;
            }
            if (!$this->tablesManager->isTableExist($tableId)) {
                echo "not exist table id\n";
                $tableId = $this->tablesManager->createTable($tableId);
            }

            $action = $this->arrayGet($request->get, 'action');
            //开始根据action值分别处理
            if ($action) {
                if ($this->arrayGet($request->server, 'request_method') == 'POST') {
                    if ($action == 'getTable') {
                        $data = $this->tablesManager->getTable($tableId);
                        $response->header("Content-Type", "application/json");
                        $response->end(json_encode([
                            "code" => TableCoworkServer::CODE_SUCCESS,
                            "data" => $data
                        ]));
                        return;
                    }

                    if ($action == 'updateCell') {
                        $x = $this->arrayGet($request->post, 'x');
                        $y = $this->arrayGet($request->post, 'y');
                        $cellPosition = [intval($x), intval($y)];
                        $value = $this->arrayGet($request->post, 'value');
                        $nickname = $this->arrayGet($request->post, 'nickname');

                        $this->tablesManager->updateTable($tableId, $cellPosition, $value);
                        $this->tablesManager->updateStatus($tableId, $nickname, $cellPosition);
                        $response->header("Content-Type", "application/json");
                        $response->end(json_encode([
                            "code" => TableCoworkServer::CODE_SUCCESS,
                            "data" => null
                        ]));

                        $this->broadcast(json_encode(["action" => $action, "tableId" => $tableId, "cellPosition" => $cellPosition, "value" => $value, "nickname" => $nickname]), [$request->fd]);
                        return;
                    }

                    if ($action == 'cellPosition') {
                        $x = $this->arrayGet($request->post, 'x');
                        $y = $this->arrayGet($request->post, 'y');
                        $cellPosition = [intval($x), intval($y)];
                        $nickname = $this->arrayGet($request->post, 'nickname');

                        $this->tablesManager->updateStatus($tableId, $nickname, $cellPosition);
                        $response->header("Content-Type", "application/json");
                        $response->end(json_encode([
                            "code" => TableCoworkServer::CODE_SUCCESS,
                            "data" => null
                        ]));

                        $this->broadcast(json_encode(["action" => $action, "tableId" => $tableId, "cellPosition" => $cellPosition, "nickname" => $nickname]), [$request->fd]);
                        return;
                    }
                }


                if ($this->arrayGet($request->server, 'request_method') == 'GET') {
                    //这里处理GET请求
                }


            } else {
                // 这里返回前端代码
                $response->header("Content-Type", "text/html; charset=UTF-8");

                $path = __DIR__ . "/../Html/index.html";
                $content = file_get_contents($path);

                //用字符串替换来传参数，😓
                $content = str_replace("__TABLE_ID__", $tableId, $content);

                $response->end($content);
                return;
            }

        });
    }

    public function run()
    {
        $this->server->start();
    }
}
