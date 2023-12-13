<?php

namespace App\Model;


use PDO;

class ShortURL
{
    private int $user_id;
    private string $long_url;
    private string $hash;
    private int $quantity;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
    }

    public static function findByHash(string $hash): ?ShortURL
    {
        $stmt = ConnectFactory::create()->prepare("SELECT * FROM urls WHERE hash = :hash");
        $stmt->execute(['hash' => $hash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $shortURL = new ShortURL($row['user_id']);
            $shortURL->setLong_url($row['long_url']);
            $shortURL->setHash($row['hash']);
            return $shortURL;
        }
        return null;
    }
    public function increaseQuantity()
    {
        $pdo = ConnectFactory::create();
        $stmt = $pdo->prepare("SELECT * FROM urls WHERE hash = :hash");

        $stmt->bindParam(':hash', $this->hash);

        $stmt->execute();

        $url = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($url) {
            $quantity = $url['quantity'];
            $this->quantity = $quantity +1;

            $updateStmt = $pdo->prepare("UPDATE urls SET quantity = :quantity WHERE hash = :hash");
            $updateStmt->bindParam(':quantity', $this->quantity);
            $updateStmt->bindParam(':hash', $this->hash);
            $updateStmt->execute();
        }
    }

    public function setLong_url(string $long_url): void
    {
        $this->long_url = $long_url;
    }

    public function getLong_url(): string
    {
        return $this->long_url;
    }
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
    public function saveURL(): void
    {
        $stmt = ConnectFactory::create()->prepare("INSERT INTO urls (user_id, long_url, hash) 
    VALUES (:user_id, :long_url, :hash)");

        $stmt->execute([
            'user_id' => $this->user_id,
            'long_url' => $this->getLong_url(),
            'hash' => $this->getHash(),
        ]);
    }

    public static function getAllURLs(int $userID):array
    {
        $stmt = ConnectFactory::create()->prepare("SELECT * FROM urls WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userID]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}