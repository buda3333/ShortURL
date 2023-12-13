<?php
namespace App\Controller;

use App\Model\ShortURL;

class ShortenController
{
    public function shorted()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $url = $_POST["url"];
            $shortenedUrl = $this->generateShortUrl($url);
            echo $shortenedUrl;
        }
    }
    public function generateShortUrl($url)
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
        }
        $hash = substr(md5($url), 0, 8);
        $shortURL = new ShortURL($_SESSION['user_id']['id']);
        $shortURL->setLong_url($url);
        $shortURL->setHash($hash);
        $shortURL->setQuantity(0);
        $shortURL->saveURL();

        return $hash;
    }
    public function redirectLongUrl($hash)
    {
        $shortURL = ShortURL::findByHash($hash);
        if ($shortURL) {
            $shortURL->increaseQuantity();
            header('Location: ' . $shortURL->getLong_url());
            exit();
        }
    }

}