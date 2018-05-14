<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 5/9/2018
 * Time: 6:47 PM
 */

namespace Course\Services\Socket;


abstract class Message
{
    const END_OF_RESPONSE = "\n";

    private $roomId;
    private $eventName;
    private $body;

    public function __construct(int $roomId, string $eventName, $body)
    {
        // TODO validate if room exists
        $this->roomId = $roomId;
        // TODO validate if event is valid
        $this->eventName = $eventName;
        $this->body = $body;
    }

    public function getRoomId(): int
    {
        return $this->roomId;
    }

    public function getJsonResponse(): string
    {
        return "$this->eventName:" . json_encode($this->body) . self::END_OF_RESPONSE;
    }
}