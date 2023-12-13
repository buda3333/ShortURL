<?php
namespace App\Controller;
use App\Model\User;
class UserController
{
    public function signup(): array
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $errors = $this->isValidSign($_POST);

            if (empty($errors)) {

                $user = User::getUserEmail($_POST['email']);
                if (!empty($user)) {
                    $errors['email'] = "Такой e-mail уже существует";
                } else {
                    session_start();
                    header('Location: /login');

                    $password = $_POST['password'];
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $user = new User($_POST['name'], $_POST['email'], $hash);
                    $user->save();
                }
            }
        }
            session_start();

            if (isset($_SESSION['user_id'])) {
                header('Location: /main');
            }
            return [
                'view' => 'signup',
                'data' => [
                    'errors' => $errors
                ]
            ];
    }
    public function login()
    {
        $errors = [];
        $errorsLogin = [];
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $errors = $this->isValidLogin($_POST);
            if (empty($errors)) {
                $password = $_POST['password'];

                $user = User::getUserEmail($_POST['email']);

                if (empty($user)) {
                    $errors['email'] = 'Пользователь не зарегестрирован c таким email';
                } elseif (!empty($user) && (password_verify($password, $user->getPassword()))) {
                    session_start();
                    $_SESSION['user_id'] = ['id' => $user->getId(), 'email' => $user->getEmail(), 'name' => $user->getName()];
                    header('Location: /main');
                } else {
                    $errorsLogin['errors'] = '* Неверный пароль';
                }
            }
        }
        return [
            'view' => 'login',
            'data' => [
                'errors' => $errors,
                'errorsLogin' => $errorsLogin
            ]
        ];
    }
    public function logout()
    {
        session_start();
        unset($_SESSION['user_id']);
        header('Location: /login');
    }
    private function isValidSign(array $data) :array
    {
        $errors = [];
        if (!isset($data['name'])) {
            $errors['name'] = 'name is required';
        } elseif (empty($data['name'])) {
            $errors['name'] = 'имя не доджно быть пустой';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'имя не доджно быть меньше двух';
        }

        if (!isset($data['email'])) {
            $errors['email'] = 'email is required';
        } elseif (empty($data['email'])) {
            $errors['email'] = 'email не доджно быть пустой';
        } elseif (strlen($data['email']) < 2) {
            $errors['email'] = 'email не доджно быть меньше двух';
        }

        if (!isset($data['password'])) {
            $errors['password'] = 'password is required';
        } elseif (empty($data['password'])) {
            $errors['password'] = 'password не доджно быть пустой';
        } elseif (strlen($data['password']) < 4) {
            $errors['password'] = 'password не доджно быть меньше четырех';
        }

        if ($data['passwordp'] !== $data['password']) {
            $errors['passwordp'] = 'password не совпадает';
        }
        return $errors;
    }
    private function isValidLogin(array $data) :array
    {
        $errors = [];

        if (!isset($data['email'])) {
            $errors['email'] = 'email is required';
        } elseif (empty($data['email'])) {
            $errors['email'] = 'email не доджно быть пустой';
        }

        if (!isset($data['password'])) {
            $errors['password'] = 'password is required';
        } elseif (empty($data['password'])) {
            $errors['password'] = 'password не доджно быть пустой';
        }
        return $errors;
    }
}
?>