<?php
namespace App\Controller;
use App\Model\ShortURL;

class MainController
{
    public function main()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
        }

            $username = $_SESSION['user_id']['email'];
            $hash = ShortURL::getAllURLs($_SESSION['user_id']['id']);
            return [
                'view' => 'main',
                'data' => [
                    'hashs' => $hash,
                    'username' => $username
                ]
            ];

    }
}