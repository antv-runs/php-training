<?php

class Database {
    public static function connect() {
        return new PDO(
            "mysql:host=db;dbname=shop;charset=utf8",
            "root",
            "root"
        );
    }
}