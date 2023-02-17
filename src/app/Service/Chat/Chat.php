<?php

namespace App\Service\Chat;

use Illuminate\Database\Capsule\Manager as DB;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use App\Models\Message;

// Looing for .env at the root directory
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
$dotenv->load();

$db_s = [
    'driver' => $_ENV['DRIVER'],
    'host' => $_ENV['HOST'],
    'database' => $_ENV['DATABASE'],
    'username' => $_ENV['USERNAME'],
    'password' => $_ENV['PASSWORD'],
    'charset' => $_ENV['CHARSET'],
    'collation' => $_ENV['COLLATION'],
    'prefix' => $_ENV['PREFIX'],
];

// orm initialization
$db = new DB;
$db->addConnection($db_s);
$db->setAsGlobal();
$db->bootEloquent();

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $chat_roms;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $conn->resourceId = parseCookie($conn->httpRequest->getHeaders()['Cookie'][0])['from'];
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data_msg = (json_decode($msg));
        $new_msg = Message::create([
            'chat_id' => $data_msg->chat,
            'participant_id' => $data_msg->from,
            'message' => $data_msg->message
        ]);

        foreach ($this->clients as $client) {

            if ($data_msg->from == $client->resourceId || $data_msg->to == $client->resourceId) {
                $client->send(json_encode($new_msg->toArray()));
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}

function parseCookie($cookie_str)
{
	$cookie_arr_raw = preg_split('/; /', $cookie_str);
	
	$cookie_arr = [];
	
	foreach($cookie_arr_raw as $cookie_raw) {
		$cookie_raw = rtrim($cookie_raw);
		if (preg_match('/(\S+)=(.*)/', $cookie_raw, $matches)) {
			$cookie_arr[$matches[1]] = $matches[2];
		}
	}
	
	return $cookie_arr;
}
