<?php
session_start();
require "functions.php";

$email = $_POST['user_email'];
$password = $_POST['user_password'];

$user = get_user_by_email($email);

if (!empty($user)) {
    set_flash_message("danger", "Этот эл. адрес уже занят другим пользователем");
    redirect_to("/page_register.php");
}

add_user($email, $password);
set_flash_message("success", "Регистрация успешна");
redirect_to("/page_login.php");

?>