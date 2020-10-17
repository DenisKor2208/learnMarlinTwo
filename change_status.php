<?php
session_start();
require "functions.php";

$open_user_id = $_GET['id'];
$current_user_email = $_SESSION['email'];
$entered_status = $_POST['online_status'];

set_status("two_person", $entered_status, $open_user_id);

set_flash_message("success", "Профиль успешно обновлен");
redirect_to("/status.php?id=".$open_user_id);

?>