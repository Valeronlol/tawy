<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request as TeleRequest;

class TelegramController extends Controller
{
    // Telegram Bot Init
    private $API_KEY = '261994906:AAGQ7wBUGUfm_liPJMERgiXI7AO2nAbejs8';
    private $BOT_NAME = 'Valeronlol_bot';
    private $chat_id = 83654187;
    private $mysql_credentials = [
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => '',
    'database' => 'valeron_db',
    ];

    public function teleSend($message)
    {
        try {
            // Create Telegram API object
            $telegram = new Telegram($this->API_KEY, $this->BOT_NAME);

            // Enable MySQL
            $telegram->enableMySQL($this->mysql_credentials, 'bot_');

            // Handle telegram getUpdate request
//            $telegram->handleGetUpdates();
            $params = [
                'chat_id' => $this->chat_id,
                'text' => $message,
            ];
//            $url = 'https://api.telegram.org/bot' . $API_KEY . '/sendMessage?' . http_build_query($params);
//            file_get_contents($url);
            $teleReq = new TeleRequest();
            $teleReq::sendMessage($params);
//            $teleReq::sendChatAction(['chat_id' => $this->chat_id, 'action' => 'typing']);

        } catch (TelegramException $e) {
            echo '<pre>' . ($e) . '</pre>';
        }
    }
}