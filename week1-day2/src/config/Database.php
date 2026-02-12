<?php

class Database
{
    public static function connect()
    {
        $host = 'db';
        $dbname = 'demo';
        $username = 'root';
        $password = 'root';

        return new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}