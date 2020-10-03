<?php
/**
    Parameters:
        string - $email

    Description: вывод всех пользователей с возможностью поиска конкретного пользователя по email

    Return value: array
**/
function get_user_by_email($email = "")
{
    $pdo = new PDO("mysql:host=localhost;dbname=edu_marlin", "root", "root");
    if (empty($email)) {
            $sql = "SELECT * FROM two_person";
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $user = $statement->fetchAll(PDO::FETCH_ASSOC);
    }else{
            $sql = "SELECT * FROM two_person WHERE email=:email";
            $statement = $pdo->prepare($sql);
            $statement->execute(["email" => $email]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);
    }

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
        $_SESSION['role'] = $user['role'];
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
    $_SESSION['status_message'] = $name;
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
        unset($_SESSION['status_message']);
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

/**
Parameters:

Description: проверка на неавторизованность пользователя

Return value: boolean
**/
function is_not_logged_in () {

    if(isset($_SESSION['email']) && !empty($_SESSION['email'])) {
        return false;
    }

    return true;
}

/**
Parameters:

Description: проверка на роль администратора

Return value: boolean
 **/
function check_admin () {
    if($_SESSION['role'] == "admin") {
        return true;
    }
    return false;
}

function vardump($value) {
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    die();
}
?>