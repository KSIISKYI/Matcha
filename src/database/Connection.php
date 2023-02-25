<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Looing for .env at the root directory
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class Connection
{
    private $connection;

    public function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host={$_ENV['HOST']}", $_ENV['USERNAME'], $_ENV['PASSWORD']);
        } catch (PDOException $exception) {
            echo $exception->getMessage();
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
