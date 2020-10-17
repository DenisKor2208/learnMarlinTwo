<?php
/**
    Parameters:
        string - $table
        string - $email

    Description: вывод всех пользователей с возможностью поиска конкретного пользователя по email или id

    Return value: array
**/
function get_user_by_email_or_id($table, $email = "", $user_id = "")
{
    $pdo = new PDO("mysql:host=localhost;dbname=edu_marlin", "root", "root");
    if (!empty($email)) {
            $sql = "SELECT * FROM $table WHERE email=:email";
            $statement = $pdo->prepare($sql);
            $statement->execute(["email" => $email]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);
    }elseif (!empty($user_id)) {
            $sql = "SELECT * FROM $table WHERE id=:id";
            $statement = $pdo->prepare($sql);
            $statement->execute(["id" => $user_id]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);
    }else {
            $sql = "SELECT * FROM $table";
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $user = $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    return $user;
};

/**
Parameters:
    string - $table
    string - $email
    string - $password

Description: авторизовать пользователя

Return value: boolean
 **/
function login($table, $email, $password) {

    $user = get_user_by_email_or_id($table, $email);

    if(empty($user)) {
        return "email not found";
    }elseif(!password_verify($password, $user['password'])) {
        return "password not found";
    }else {
        return $user;
    }
}

/**
 * Parameters:
        string - $table
        string - $email
        stirng - $password

    Description: добавить пользователя в БД

    Return value: int (user_id)
**/
function add_user($table, $email, $password) {
    $pdo = new PDO("mysql:host=localhost;dbname=edu_marlin", "root", "root");
    $sql = "INSERT INTO $table (email, password) VALUES (:email, :password)";
    $statement = $pdo->prepare($sql);
    $result = $statement->execute(["email" => $email,
                                   "password" => password_hash($password, PASSWORD_DEFAULT)
    ]);

    return $pdo->lastInsertId();
};

/**
    Parameters:
        string - $table
        array - $data
        $integer - $user_id

    Description: редактировать профиль

    Return value: boolean
**/
function edit($table, $data, $user_id) {
    $fields = '';

    foreach($data as $key => $value) {
        if($key == "first_name" || $key == "last_name" || $key == "company" || $key == "phone_number" || $key == "address" || $key == "role"){
            $fields .= $key . "=:" . $key . ",";
        }else {
            unset($data[$key]);
        }
        }

    $data += ['id'=>$user_id];
    $fields = rtrim($fields, ',');

    $pdo = new PDO("mysql:host=localhost;dbname=edu_marlin", "root", "root");
    $sql = "UPDATE $table SET $fields WHERE id=:id";
    $statement = $pdo->prepare($sql);
    $statement->execute($data);
}

/**
Parameters:
    string - $table
    string - $status
    $integer - $user_id

Description: установить статус

Return value: null
 **/
function set_status($table, $status, $user_id) {
    $pdo = new PDO("mysql:host=localhost;dbname=edu_marlin", "root", "root");
    $sql = "UPDATE $table SET online_status=:online_status WHERE id=:id";
    $statement = $pdo->prepare($sql);
    $statement->execute(["online_status" => $status,
                         "id" => $user_id
                        ]);
}

/**
Parameters:
    string - $table
    array - $data
    $integer - $user_id

Description: добавить ссылки на соц. сети

Return value: null
 **/
function add_social_links($table, $data, $user_id) {
    $fields = '';

    foreach($data as $key => $value) {
        if($key == "vk" || $key == "telegram" || $key == "instagram"){
            $fields .= $key . "=:" . $key . ",";
        }else {
            unset($data[$key]);
        }
    }

    $data += ['id'=>$user_id];
    $fields = rtrim($fields, ',');

    $pdo = new PDO("mysql:host=localhost;dbname=edu_marlin", "root", "root");
    $sql = "UPDATE $table SET $fields WHERE id=:id";
    $statement = $pdo->prepare($sql);
    $statement->execute($data);
}

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
    if($_SESSION['role'] == "Администратор") {
        return true;
    }
    return false;
}

/**
Parameters:
    $image array

Description: загрузить аватар

Return value: null | string (path)
**/
function upload_avatar ($image, $table, $user_id) {

    /* удаление файла аватара из папки
    $img_for_delete = get_user_by_email($table, "chtil@list.ru");
    unlink("img/avatar/" . $img_for_delete['img_avatar']);
    */

    $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . "." . $extension;

    move_uploaded_file($image['tmp_name'], "img/avatar/" . $filename);


    $pdo = new PDO("mysql:host=localhost;dbname=edu_marlin", "root", "root");
    $sql = "UPDATE $table SET img_avatar=:img_avatar WHERE id=:id";
    $statement = $pdo->prepare($sql);
    $statement->execute(["img_avatar" => $filename,
                                "id" => $user_id
                               ]);
}

/**
Parameters:
    $logger_user_id int
    $edit_user_id int

Description: проверить, автор ли текущий авторизованный пользователь

Return value: boolean
 **/
function is_author ($logger_user_id, $edit_user_id) {
    if ($logger_user_id == $edit_user_id) {
        return true;
    }
    return false;
}

/**
Parameters:
    $user_id int
    $email string
    $password string

Description: редактировать входные данные: email и password

Return value: null | boolean
 **/
function edit_credentials($table, $user_id, $email = null, $password = null) {
    $pdo = new PDO("mysql:host=localhost;dbname=edu_marlin", "root", "root");

    if($email == null) {
        $sql = "UPDATE $table SET password=:password WHERE id=:id";
        $statement = $pdo->prepare($sql);
        $statement->execute(["id" => $user_id,
                             "password" => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }elseif($password == null) {
        $sql = "UPDATE $table SET email=:email WHERE id=:id";
        $statement = $pdo->prepare($sql);
        $statement->execute(["id" => $user_id,
                             "email" => $email
        ]);
    }else{
        $sql = "UPDATE $table SET email=:email, password=:password  WHERE id=:id";
        $statement = $pdo->prepare($sql);
        $statement->execute(["id" => $user_id,
                             "email" => $email,
                             "password" => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }
}

function vardump($value) {
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
    die();
}

?>