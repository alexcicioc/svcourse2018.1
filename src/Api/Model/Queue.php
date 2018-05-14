<?php

namespace Course\Api\Model;

class Queue {

    public static $users = [];

    public static function addUser($userId) {
        self::$users[] = $userId;
    }

    public static function areThereAreEnoughUsers() {
        return count(self::$users) >= 2;
    }

    public static function reset() {
        self::$users = [];
    }
}