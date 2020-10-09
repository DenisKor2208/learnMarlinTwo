<?php
session_start();
require "functions.php";

$email = $_POST['email'];
$password = $_POST['password'];
$data = $_POST;

$user = get_user_by_email_or_id("two_person", $email);

if (!empty($user)) {
    set_flash_message("danger", "Этот эл. адрес уже занят другим пользователем");
    redirect_to("/users.php");
}

$user_id = add_user("two_person", $email, $password);
edit("two_person", $data, $user_id);
set_status("two_person", $data['online_status'], $user_id); //установка статуса "Онлайн / Отошел / Не беспокоить"

add_social_links("two_person", $data, $user_id); //добавление ссылок соц. сетей

upload_avatar($_FILES['image'], "two_person", $user_id); //загрузка аватара

set_flash_message("success", "Профиль успешно создан");
redirect_to("/users.php");

?>