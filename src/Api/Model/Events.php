<?php
namespace Course\Api\Model;

use Amp\Socket\ServerSocket;
use Course\Api\Exceptions\ApiException;
use Course\Api\Exceptions\Precondition;
use Course\Services\Socket\Message;
use Course\Services\Socket\Response;
use Course\Services\Socket\SocketClient;

class Events {
//    const AUTHORIZE = 'authorize';
    const JOIN_ROOM = 'joinRoom';
    const ALLOWED_EVENTS = [
        self::JOIN_ROOM
    ];

    private static $room = null;

    /**
     * @param ServerSocket $serverSocket
     * @param $data
     * @throws ApiException
     * @throws \Course\Api\Exceptions\PreconditionException
     */
    public static function processEvent(ServerSocket $serverSocket, $data) {
        $explode = explode(':', $data, 2);
        if (count($explode) !== 2) {
            throw new ApiException('socket message should be in this form event:jsonBody');
        }
        list($eventType, $jsonBody) = $explode;
        Precondition::isInArray($eventType, self::ALLOWED_EVENTS, 'eventType');
        $decodedBody = @json_decode($jsonBody);
        Precondition::isNotEmpty($decodedBody, 'decodedBody');
        $eventClassName = __NAMESPACE__ . "\\" .ucfirst($eventType) . "Event";
        $event = new $eventClassName($decodedBody);
        if (!method_exists($event, 'handle')) {
            throw new ApiException("method handle doesn't exist for event $eventType");
        }

        $event->handle($serverSocket);
    }
}