<?php

require 'session.php';
require 'db.php';
require 'functions.php';
require 'helpers.php';

$is_main = 0;


$formParams = [];
$formItemErrors = [];
$formError = false;
$userIdentificationError = false;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // password
    $formParams['password'] = '';
    if (empty($_POST['password'])) {
        $formItemErrors['password'] = true;
    }
    if (!isset($formItemErrors['password']) && (strlen($_POST['password']) === 0)) {
        $formItemErrors['password'] = true;
    }
    if (!isset($formItemErrors['password'])) {
        $formParams['password'] = $_POST['password'];
    }


    if (count($formItemErrors) > 0) {
        $formError = true;
    }

    // authentication
    if (!$formError) {
        $auth = userAuthentication($dbConnection, $formParams);
        if ($auth['result']) {
            session_start();
            $_SESSION['username'] = $auth['username'];
            $_SESSION['user_id'] = $auth['user_id'];
            header("Location: /index.php");
        }
    }
}

$categories = getCategories($dbConnection);

$pageContent = include_template('login.php', [
    'formParams' => $formParams,
    'formError' => $formError,
    'formItemErrors' => $formItemErrors,
    'userIdentificationError' => $userIdentificationError
]);

$layoutContent = include_template('layout.php', [
    'pageContent' => $pageContent,
    'pageTitle' => 'Вход',
    'is_auth' => $is_auth,
    'is_main' => $is_main,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layoutContent);