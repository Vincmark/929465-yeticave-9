<?php

require 'functions.php';
require 'helpers.php';
$is_auth = rand(0, 1);
$user_name = 'Алексей Кошевой';
$is_main = 0;


$formParams = [];
$formItemErrors = [];
$formError = false;

$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection == false) {
    print("Ошибка подключения: ". mysqli_connect_error());
    die();
} else {
    mysqli_set_charset($dbConnection, "utf8");
}

// Обрабатываем добавление или просто показываем форму?
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // email
    $formParams['email'] = '';
    if (empty($_POST['email']))
        $formItemErrors['email'] = true;
    if (!isset($formItemErrors['email']) && (strlen($_POST['email']) === 0))
        $formItemErrors['email'] = true;
    if (!isset($formItemErrors['email'])) {
        $formParams['email'] = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if ($formParams['email'] !== false) {
            $formParams['email'] = mysqli_real_escape_string($dbConnection, $_POST['email']);
        } else {
            $formParams['email'] = mysqli_real_escape_string($dbConnection, $_POST['email']);
            $formItemErrors['email'] = true;
        }
    }
    if (!isset($formItemErrors['email'])) {
        $sql = 'select id, email, password from users where email = ?';
        $stmt = mysqli_stmt_init($dbConnection);
        mysqli_stmt_prepare($stmt, $sql);
        $bindResult = mysqli_stmt_bind_param($stmt, 's', $formParams['email']);
        $executeResult = mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$executeResult) {
            print("Ошибка MySQL: " . mysqli_errno($dbConnection));
            die();
        } else {
            $num_rows = mysqli_num_rows($result);
            if ($num_rows > 0) {
                $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo $users[0]['email'];
                echo $users[0]['password'];
            }

            mysqli_stmt_close($stmt);
        }
    }

    // password
    $formParams['password'] = '';
    if (empty($_POST['password']))
        $formItemErrors['password'] = true;
    if (!isset($formItemErrors['password']) && (strlen($_POST['password']) > 0))
        $formParams['password'] = password_hash($_POST['password'],PASSWORD_DEFAULT);

    if (count($formItemErrors)>0)
        $formError = true;


    // Сохраняем нового пользователя
    if (!$formError) {
        $sql = 'insert into users (email, name, password, contact) VALUES (?, ?, ?, ?)';
        $stmt = mysqli_stmt_init($dbConnection);
        mysqli_stmt_prepare($stmt, $sql);
        $bindResult = mysqli_stmt_bind_param($stmt, 'ssss', $formParams['email'], $formParams['name'], $formParams['password'], $formParams['message']);
        $executeResult = mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (!$executeResult) {
            print("Ошибка MySQL: " . mysqli_errno($dbConnection));
            die();
        } else {
            mysqli_stmt_close($stmt);
            header("Location: /login.php");
        }
    }
}

// Зачитываем категории
$sql = 'select id,title,symbol_code from categories';
$result = mysqli_query($dbConnection, $sql);
if (!$result) {
    print("Ошибка MySQL: " . mysqli_error($dbConnection));
} else {
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
}


$pageContent = include_template('login.php', ['formParams' => $formParams, 'formError' => $formError, 'formItemErrors' => $formItemErrors]);
$layoutContent = include_template('layout.php',['pageContent' => $pageContent, 'pageTitle' => 'Добавить лот', 'is_auth' => $is_auth, 'is_main' => $is_main, 'user_name' => $user_name, 'categories' => $categories]);
print($layoutContent);