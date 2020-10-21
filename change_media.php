<?php
session_start();
require "functions.php";

$open_user_id = $_GET['id'];

if (empty($_FILES['image']['name'])) {
    redirect_to("/media.php?id=".$open_user_id);
} else {
    upload_avatar($_FILES['image'], "two_person", $open_user_id); //загрузка аватара
}

set_flash_message("success", "Профиль успешно обновлен");
redirect_to("/page_profile.php?id=".$open_user_id);

?>