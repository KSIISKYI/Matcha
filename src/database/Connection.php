<?php

class Connection
{
    private $connection;

    function __construct(string $db_host, string $db_user, string $db_pass)
    {
        try {
            $this->connection = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        } catch (PDOException $exception) {
            echo $exception->getMessage();
        }
    }

    function getConnection(): PDO
    {
        return $this->connection;
    }
}
