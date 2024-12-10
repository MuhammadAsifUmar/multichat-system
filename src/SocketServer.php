<?php
$host = '127.0.0.1';
$port = 8080;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($socket, $host, $port);
socket_listen($socket);

$clients = [];

while (true) {
    $newSocket = socket_accept($socket);
    $clients[] = $newSocket;
    $data = socket_read($newSocket, 1024);

    foreach ($clients as $client) {
        socket_write($client, $data, strlen($data));
    }
}
socket_close($socket);
