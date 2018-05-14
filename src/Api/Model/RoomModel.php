<?php

namespace Course\Api\Model;

use Course\Services\Persistence\Exceptions\NoResultsException;
use Course\Services\Persistence\MySql;

/**
 * Class UserModel
 * Active Record for the users table
 * @package Course\Api\Model
 */
class RoomModel extends ActiveRecord
{
    /**
     * @var array
     * Contains the database configuration used in the ActiveRecord parent
     */
    private static $config = [
        ActiveRecord::CONFIG_TABLE_NAME   => 'rooms',
        ActiveRecord::CONFIG_PRIMARY_KEYS => ['id'],
        ActiveRecord::CONFIG_DB_COLUMNS   => ['id', 'name'],
    ];

    /**
     * Fetches the user by it's id and returns an UserModel
     *
     * @param int $id - User id
     * @return RoomModel
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
     * @param string $name
     * @return RoomModel
     * @throws NoResultsException
     * @throws \Course\Services\Persistence\Exceptions\ConnectionException
     * @throws \Course\Services\Persistence\Exceptions\QueryException
     * @internal param int $userId
     */
    public static function create(string $name): self
    {
        $id = MySql::insert(self::getTableName(), ['name' => $name]);
        return self::loadById($id);
    }

    /**
     * @param string $name
     * @return bool
     * @throws \Course\Services\Persistence\Exceptions\ConnectionException
     * @throws \Course\Services\Persistence\Exceptions\QueryException
     */
    public static function roomExists(string $name): bool
    {
        try {
            MySql::getOne(self::getTableName(), ['name' => $name]);
            return true;
        } catch (NoResultsException $e) {
            return false;
        }
    }

    /**
     * @param string $name
     * @return RoomModel
     * @throws NoResultsException
     * @throws \Course\Services\Persistence\Exceptions\ConnectionException
     * @throws \Course\Services\Persistence\Exceptions\QueryException
     */
    public static function getByRoomName(string $name): self
    {
        $result = MySql::getOne(self::getTableName(), ['name' => $name]);
        return new self($result);
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