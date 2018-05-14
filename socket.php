<?php
include "autoload.php";

use Amp\Loop;
use Amp\Socket\ServerSocket;
use function Amp\asyncCall;
use Course\Api\Exceptions\ApiException;
use Course\Api\Model\Events;
use Course\Services\Socket\Broadcast;
use Course\Services\Socket\Message;


class SocketHandler
{
    public $clients = [];

    /**
     * @throws Error
     * @throws TypeError
     * @throws \Amp\Socket\PendingAcceptError
     * @throws \Amp\Socket\SocketException
     */
    public function process()
    {
        $uri = "tcp://127.0.0.1:1337";

        $clients[] = $clientHandler = function (ServerSocket $socket) {
            $fullCommand = '';
            while (null !== $chunk = yield $socket->read()) {
                $fullCommand .= $chunk;
                if (strpos($chunk, "\n") !== false) {
                    try {
                        Events::processEvent($socket, $fullCommand);

                        $fullCommand = '';
                    } catch (ApiException $e) {
                        yield $socket->write(json_encode(['errorMessage' => $e->getMessage()]));
                    }
                }
            }
        };

        $server = Amp\Socket\listen($uri);

        while ($socket = yield $server->accept()) {
            asyncCall($clientHandler, $socket);
        }
    }
}

$socketHandler = new SocketHandler();
Loop::run([new SocketHandler(), 'process']);
