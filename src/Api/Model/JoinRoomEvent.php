<?php
namespace Course\Api\Model;

use Amp\Socket\ServerSocket;
use Course\Services\Socket\Broadcast;
use Course\Services\Socket\SocketClient;
use Course\Services\Socket\SocketClients;

class JoinRoomEvent extends Event {

    /**
     * StartGameEvent constructor.
     * @param object $data
     * @throws \Course\Api\Exceptions\PreconditionException
     * @throws \Course\Services\Authentication\Exceptions\DecryptException
     */
    public function __construct(object $data)
    {
        parent::__construct(Events::JOIN_ROOM, $data);
    }

    /**
     * @param ServerSocket $serverSocket
     * @return void
     * @throws \Course\Services\Persistence\Exceptions\ConnectionException
     * @throws \Course\Services\Persistence\Exceptions\NoResultsException
     * @throws \Course\Services\Persistence\Exceptions\QueryException
     */
    public function handle(ServerSocket $serverSocket) {
        $userId = $this->getUserModel()->id;
        $roomUserModel = RoomUsersModel::getOrCreateRoomUser($userId);
        Queue::addUser($userId);

        $socketClient = new SocketClient($serverSocket, $userId);

        SocketClients::addClientToRoom($roomUserModel->roomId, $socketClient);

        if (Queue::areThereAreEnoughUsers()) {
            $body = ['ok'=> true];
            $broadcast = new Broadcast($roomUserModel->roomId, ResponseEvents::START_GAME, $body);
            SocketClients::broadcastToRoom($broadcast);
            RoomUsersModel::removeRoomFromQue($roomUserModel->roomId);
            Queue::reset();
        }
    }
}