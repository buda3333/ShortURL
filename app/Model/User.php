<?php

namespace App\Model;

class User
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;
    public function __construct(string $name, string $email, string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId():int
    {
        return $this->id;
    }

    public function getName():string
    {
        return $this->name;
    }

    public function getEmail():string
    {
        return $this->email;
    }

    public function getPassword():string
    {
        return $this->password;
    }
    public static function getUserEmail(string $email):User|null
    {
        $stmt = ConnectFactory::create()->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        $user = new User($data['name'], $data['email'], $data['password']);
        $user->setId($data['id']);

        return $user;
    }

    public function save(): void
    {
        $stmt = ConnectFactory::create()->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->execute(['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);

    }
}
?>