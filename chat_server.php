<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface {
    protected $clients;
    protected $usernames;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->usernames = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $conn->send(json_encode(['type' => 'info', 'message' => 'Welcome to Multichat!']));
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        if (!isset($this->usernames[$from->resourceId])) {
            $this->usernames[$from->resourceId] = $data['username'];
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    $client->send(json_encode(['type' => 'info', 'message' => "{$data['username']} joined the chat."]));
                }
            }
            return;
        }

        $username = $this->usernames[$from->resourceId];
        if ($data['type'] === 'group') {
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    $client->send(json_encode(['type' => 'group', 'user' => $username, 'message' => $data['message']]));
                }
            }
        } elseif ($data['type'] === 'private') {
            foreach ($this->clients as $client) {
                if ($this->usernames[$client->resourceId] === $data['to']) {
                    $client->send(json_encode(['type' => 'private', 'from' => $username, 'message' => $data['message']]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        $username = $this->usernames[$conn->resourceId] ?? "User";
        unset($this->usernames[$conn->resourceId]);
        foreach ($this->clients as $client) {
            $client->send(json_encode(['type' => 'info', 'message' => "{$username} left the chat."]));
        }
    }
}

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    8080
);

$server->run();
