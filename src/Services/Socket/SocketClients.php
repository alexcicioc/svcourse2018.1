<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/9/2018
 * Time: 7:29 PM
 */

namespace Course\Services\Socket;

use Amp\Socket\ServerSocket;

class SocketClients
{
    private static $clients = [];

    public static function addClientToRoom(int $roomId, SocketClient $socketClient)
    {
        $userId = $socketClient->getUserId();
        if (!isset(self::$clients[$roomId])) {
            self::$clients[$roomId] = [$userId => $socketClient];
        } elseif (!isset(self::$clients[$roomId][$userId])) {
            self::$clients[$roomId][$userId] = $socketClient;
        }
    }

    public static function broadcastToRoom(Broadcast $message)
    {
        var_dump(self::$clients);
        foreach (self::$clients[$message->getRoomId()] as $client) {
            // We don't yield the promise returned from $client->write() here as we don't care about
            // other clients disconnecting and thus the write failing.
            $client->write($message);
        }
    }
}
