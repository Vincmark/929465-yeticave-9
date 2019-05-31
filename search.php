<?php

require 'functions.php';
require 'helpers.php';

session_start();
if (isset($_SESSION['username'])) {
    $is_auth = 1;
    $user_name = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
} else {
    $is_auth = 0;
    $user_name = '';
}

$is_main = 0;

$formParams = [];
$formItemErrors = [];
$formError = false;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['search']))
        $query = trim($_GET['search']);
    else
        $formError = true;
    if (!$formError && !empty($query)) {

    } else
        $formError = true;
}

$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection == false) {
    print("Ошибка подключения: ". mysqli_connect_error());
    die();
} else {
    mysqli_set_charset($dbConnection, "utf8");
    // Зачитываем лоты
    //SELECT * FROM gifs WHERE MATCH(title,description) AGAINST('слово')

    $sql = 'select l.id as lot_id, l.title, start_price, lot_img, stop_date, c.title as category_title from lots l join categories c on l.id_category = c.id where stop_date >="'.date('y-m-d',strtotime('now')).'"  order by date_reg desc';
    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    } else {
        $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Зачитываем категории
    $sql = 'select title,symbol_code from categories';
    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    } else {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

$pageContent = include_template('search.php', ['categories' => $categories , 'lots' => $lots]);
$layoutContent = include_template('layout.php',['pageContent' => $pageContent, 'pageTitle' => 'Главная', 'is_auth' => $is_auth, 'is_main' => $is_main, 'user_name' => $user_name, 'categories' => $categories]);
print($layoutContent);