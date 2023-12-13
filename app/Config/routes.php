<?php


use App\Controller\MainController;
use App\Controller\ShortenController;
use App\Controller\UserController;

return [
    '/signup' => [UserController::class, 'signup'],
    '/login' => [UserController::class, 'login'],
    '/logout' => [UserController::class, 'logout'],
    '/main' => [MainController::class, 'main'],
    '/short' => [ShortenController::class,'shorted']
];