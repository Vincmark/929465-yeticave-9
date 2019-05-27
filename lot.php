<?php

require 'functions.php';
require 'helpers.php';

session_start();
if (isset($_SESSION['username'])) {
    $is_auth = 1;
    $user_name = $_SESSION['username'];
} else {
    $is_auth = 0;
    $user_name = '';
}

$is_main = 0;

// Проверяем параметр из URL
if (isset($_GET['id'])) {
    $lotId = intval($_GET['id']);
} else {
    http_response_code(404);
    die();
}

$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection == false) {
    print("Ошибка подключения: ". mysqli_connect_error());
    die();
} else {
    mysqli_set_charset($dbConnection, "utf8");
    // Зачитываем лот
    $sql = 'select l.id, l.title, description, start_price, lot_img, stop_date, c.title as category_title from lots l join categories c on l.id_category = c.id where l.id=?';
    $stmt = mysqli_prepare($dbConnection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $lotId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    } else {
        $records_count = mysqli_num_rows($result);
        if ($records_count>0){
            $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $lot = $lots[0];
        } else {
            http_response_code(404);
            die();
        }
    }

    // Зачитываем категории
    $sql = 'select title,symbol_code from categories';
    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
    } else {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

$pageContent = include_template('lot.php', ['categories' => $categories , 'lot' => $lot, 'is_auth' => $is_auth]);
$layoutContent = include_template('layout.php',['pageContent' => $pageContent, 'pageTitle' => $lot['title'], 'is_auth' => $is_auth, 'is_main' => $is_main, 'user_name' => $user_name, 'categories' => $categories]);
print($layoutContent);