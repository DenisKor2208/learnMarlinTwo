<?php
/**
    Parameters:
        string - $email

    Description: поиск пользователя по эл. адресу

    Return value: array
**/
function get_user_by_email($email) {
    $pdo = new PDO("mysql:host=localhost;dbname=edu_marlin","root", "root");
    $sql = "SELECT * FROM two_person WHERE email=:email";
    $statement = $pdo->prepare($sql);
    $statement->execute(["email" => $email]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    return $user;
};

/**
Parameters:
string - $email
string - $password

Description: авторизовать пользователя

Return value: boolean
 **/
function login($email, $password) {

    $user = get_user_by_email($email);

    if (empty($user)) {
        set_flash_message("danger", "Пользователь не найден!");
        redirect_to("/page_login.php");
    }elseif (!password_verify($password, $user['password'])) {
        set_flash_message("danger", "Ошибка при вводе пароля");
        redirect_to("/page_login.php");
    }else {
        $_SESSION['id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['password'] = $user['password'];
        redirect_to("/users.php");
    }
}

/**
    Parameters:
        string - $email
        stirng - $password

    Description: добавить пользователя в БД

    Return value: int (user_id)
**/
function add_user($email, $password) {
    $pdo = new PDO("mysql:host=localhost;dbname=edu_marlin", "root", "root");
    $sql = "INSERT INTO two_person (email, password) VALUES (:email, :password)";
    $statement = $pdo->prepare($sql);
    $result = $statement->execute(["email" => $email,
                                   "password" => password_hash($password, PASSWORD_DEFAULT)
    ]);

    return $pdo->lastInsertId();
};

/**
    Parameters:
        string - $name (ключ)
        string - $message (значение, текст сообщения)

    Description: подготовить текст сообщения

    Return value: null
**/
function set_flash_message($name, $message) {
    $_SESSION[$name] = $message;
    $_SESSION['text_name'] = $name;
};

/**
    Parameters:
        string - $name (ключ)

    Description: вывести флэш сообщение

    Return value: null
 **/
function display_flash_message($name) {
    if (!empty($_SESSION[$name])) {
        echo "<div class=\"alert alert-{$name} text-dark\" role=\"alert\">{$_SESSION[$name]}</div>";
        unset($_SESSION[$name]);
        unset($_SESSION['text_name']);
    }
};

/**
    Parameters:
        string - $path (ключ)

    Description: перенаправить на другую страницу

    Return value: null
 **/
function redirect_to($path) {
    header('Location: ' . $path);
    exit();
}

?>