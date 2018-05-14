<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/9/2018
 * Time: 7:29 PM
 */

namespace Course\Services\Socket;

use Amp\Socket\ServerSocket;

class SocketClient
{
    private $socket;
    private $userId;

    public function __construct(ServerSocket $socket, int $userId)
    {
        $this->socket = $socket;
        $this->userId = $userId;
    }

    public function write(Message $message)
    {
        $this->socket->write($message->getJsonResponse());
    }

    public function getUserId()
    {
        return $this->userId;
    }
}