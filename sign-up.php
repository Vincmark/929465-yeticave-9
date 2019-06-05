<?php

require 'session.php';
require 'db.php';
require 'functions.php';
require 'helpers.php';

$is_main = 0;


$formParams = [];
$formItemErrors = [];
$formError = false;

$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
    die();
} else {
    mysqli_set_charset($dbConnection, "utf8");
}

// Обрабатываем добавление или просто показываем форму?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // name
    $formParams['name'] = '';
    if (empty($_POST['name'])) {
        $formItemErrors['name'] = true;
    }
    if (!isset($formItemErrors['name']) && (strlen($_POST['name']) > 0)) {
        $formParams['name'] = mysqli_real_escape_string($dbConnection, $_POST['name']);
    }

    // email
    $formParams['email'] = '';
    if (empty($_POST['email'])) {
        $formItemErrors['email'] = true;
    }
    if (!isset($formItemErrors['email']) && (strlen($_POST['email']) === 0)) {
        $formItemErrors['email'] = true;
    }
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
        $sql = 'select * from users where email = ?';
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
                $formItemErrors['email'] = true;
            }
            mysqli_stmt_close($stmt);
        }
    }

    // password
    $formParams['password'] = '';
    if (empty($_POST['password'])) {
        $formItemErrors['password'] = true;
    }
    if (!isset($formItemErrors['password']) && (strlen($_POST['password']) > 0)) {
        $formParams['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // message
    $formParams['message'] = '';
    if (empty($_POST['message'])) {
        $formItemErrors['message'] = true;
    }
    if (!isset($formItemErrors['message']) && (strlen($_POST['message']) > 0)) {
        $formParams['message'] = mysqli_real_escape_string($dbConnection, $_POST['message']);
    }


    if (count($formItemErrors) > 0) {
        $formError = true;
    }


    // Сохраняем нового пользователя
    if (!$formError) {
        $sql = 'insert into users (email, name, password, contact) VALUES (?, ?, ?, ?)';
        $stmt = mysqli_stmt_init($dbConnection);
        mysqli_stmt_prepare($stmt, $sql);
        $bindResult = mysqli_stmt_bind_param($stmt, 'ssss', $formParams['email'], $formParams['name'],
            $formParams['password'], $formParams['message']);
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


$pageContent = include_template('sign-up.php', [
    'formParams' => $formParams,
    'formError' => $formError,
    'formItemErrors' => $formItemErrors
]);

$layoutContent = include_template('layout.php', [
    'pageContent' => $pageContent,
    'pageTitle' => 'Регистрация',
    'is_auth' => $is_auth,
    'is_main' => $is_main,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layoutContent);