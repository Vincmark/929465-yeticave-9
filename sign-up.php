<?php

require 'session.php';
require 'db.php';
require 'functions.php';
require 'helpers.php';

$is_main = 0;


$formParams = [];
$formItemErrors = [];
$formError = false;

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

    // unique email check
    if (!isset($formItemErrors['email'])) {
        if (!checkForUniqueEmail($dbConnection, $formParams['email'])) {
            $formItemErrors['email'] = true;
        }
    }


    // password check
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
        if (saveNewUser($dbConnection, $formParams)) {
            header("Location: /login.php");
        }
    }
}

$categories = getCategories($dbConnection);


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