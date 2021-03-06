<?php

namespace Course\Api\Model;

use Course\Services\Persistence\Exceptions\NoResultsException;
use Course\Services\Persistence\MySql;

/**
 * Class UserModel
 * Active Record for the users table
 * @package Course\Api\Model
 */
class RoomUsersModel extends ActiveRecord
{
    private static $roomsInQue = [];

    /**
     * @var array
     * Contains the database configuration used in the ActiveRecord parent
     */
    private static $config = [
        ActiveRecord::CONFIG_TABLE_NAME   => 'room_users',
        ActiveRecord::CONFIG_PRIMARY_KEYS => ['id'],
        ActiveRecord::CONFIG_DB_COLUMNS   => ['id', 'roomId', 'userId'],
    ];

    /**
     * Fetches the user by it's id and returns an UserModel
     *
     * @param int $id - User id
     * @return RoomUsersModel
     * @throws NoResultsException
     * @throws \Course\Services\Persistence\Exceptions\ConnectionException
     * @throws \Course\Services\Persistence\Exceptions\QueryException
     */
    public static function loadById(int $id): self
    {
        $result = MySql::getOne(self::getTableName(), ['id' => $id]);
        return new self($result);
    }

    /**
     * Inserts a new record into the users table and returns the newly created user as a UserModel
     *
     * @param int $roomId
     * @param int $userId
     * @return RoomUsersModel
     * @throws NoResultsException
     * @throws \Course\Services\Persistence\Exceptions\ConnectionException
     * @throws \Course\Services\Persistence\Exceptions\QueryException
     * @internal param string $name
     * @internal param int $userId
     */
    public static function create(int $roomId, int $userId): self
    {
        $id = MySql::insert(self::getTableName(), ['roomId' => $roomId, 'userId' => $userId]);
        return self::loadById($id);
    }

    /**
     * @param int $roomId
     * @return bool
     * @throws \Course\Services\Persistence\Exceptions\ConnectionException
     * @throws \Course\Services\Persistence\Exceptions\QueryException
     */
    public static function areThereAreEnoughUsers(int $roomId): bool
    {
        return count(MySql::getMany(self::getTableName(), ['roomId' => $roomId])) > 2;
    }

    /**
     * @param int $roomId
     * @return bool
     * @throws \Course\Services\Persistence\Exceptions\ConnectionException
     * @throws \Course\Services\Persistence\Exceptions\QueryException
     */
    public static function isThereAtleastOneUser(int $roomId): bool
    {
        return count(MySql::getMany(self::getTableName(), ['roomId' => $roomId])) > 1;
    }


    /**
     * @param $userId
     * @return RoomUsersModel
     * @throws NoResultsException
     * @throws \Course\Services\Persistence\Exceptions\ConnectionException
     * @throws \Course\Services\Persistence\Exceptions\QueryException
     */
    public static function getOrCreateRoomUser($userId): self
    {
        if (empty(self::$roomsInQue)) {
            $room = RoomModel::create('room1');
            self::$roomsInQue[$room->id] = $room;
            $roomUserModel = RoomUsersModel::create($room->id, $userId);
            return $roomUserModel;
        }

        // Returns the first room available
        $room = reset(self::$roomsInQue);
        $roomUserModel = RoomUsersModel::create($room->id, $userId);

        return $roomUserModel;
    }

    public static function removeRoomFromQue($roomId) {
        unset(self::$roomsInQue[$roomId]);
    }

    /**
     * Used in the ActiveRecord parent to fetch the db config
     *
     * @return array
     */
    protected static function getConfig(): array
    {
        return self::$config;
    }
}