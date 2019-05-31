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


$formError = false;
$lots = [];
//$notFound = false;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['search']))
        $query = trim($_GET['search']);
    else
        $formError = true;

    if (!$formError && (empty($query) || (strlen($query) <= 3)))
        $formError = true;
}

$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection == false) {
    print("Ошибка подключения: ". mysqli_connect_error());
    die();
} else {
    mysqli_set_charset($dbConnection, "utf8");


    // Зачитываем категории
    $sql = 'select title,symbol_code from categories';
    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    } else {
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    if (!$formError) {
        // Зачитываем лоты
        $sql = 'select l.id as lot_id, l.title, l.description, start_price, lot_img, stop_date, c.title as category_title from lots l join categories c on l.id_category = c.id where stop_date >="'.date('y-m-d',strtotime('now')).'" and MATCH(l.title,l.description) AGAINST("'.$query.'")  order by date_reg desc';
        //echo $sql;
        $result = mysqli_query($dbConnection, $sql);
        if (!$result) {
            print("Ошибка MySQL: " . mysqli_error($dbConnection));
            die();
        } else {
            $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if (count($lots) === 0)
                $formError = true;
        }
    }
}

$pageContent = include_template('search.php', ['categories' => $categories , 'lots' => $lots, 'query' => $query, 'formError' => $formError]);
$layoutContent = include_template('layout.php',['pageContent' => $pageContent, 'pageTitle' => 'Результаты поиска', 'is_auth' => $is_auth, 'is_main' => $is_main, 'user_name' => $user_name, 'categories' => $categories, 'query' => $query]);
print($layoutContent);