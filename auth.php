<?php
session_start();
require "functions.php";

$email = $_POST["user_email"];
$password = $_POST['user_password'];

$user = login($email, $password);

?>