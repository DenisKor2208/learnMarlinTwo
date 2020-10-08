<?php
session_start();
require "functions.php";

$email = $_POST["user_email"];
$password = $_POST['user_password'];

$user = login("two_person", $email, $password);

if($user == "email not found") {
    set_flash_message("danger", "Пользователь не найден!");
    redirect_to("/page_login.php");
}elseif($user == "password not found") {
    set_flash_message("danger", "Ошибка при вводе пароля");
    redirect_to("/page_login.php");
}else {
    $_SESSION['id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['password'] = $user['password'];
    $_SESSION['role'] = $user['role'];
    redirect_to("/users.php");
}



?>