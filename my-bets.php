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


$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection == false) {
    print("Ошибка подключения: ". mysqli_connect_error());
    die();
} else {
    mysqli_set_charset($dbConnection, "utf8");

    // Зачитываем ставки
    $sql = 'select b.id, b.bet_price, l.title as lot_title, b.bet_date, l.stop_date, b.id_bettor, b.id_lot, l.lot_img as lot_img, l.id as lot_id, u.id, c.title as category_title  from bets b join lots l on b.id_lot=l.id join users u on b.id_bettor=u.id join categories c on l.id_category=c.id where b.id_bettor=?';

    $stmt = mysqli_prepare($dbConnection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    } else {
        $bets = mysqli_fetch_all($result, MYSQLI_ASSOC);

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

$pageContent = include_template('my-bets.php', ['bets' => $bets]);
$layoutContent = include_template('layout.php',['pageContent' => $pageContent, 'pageTitle' => 'Мои ставки', 'is_auth' => $is_auth, 'is_main' => $is_main, 'user_name' => $user_name, 'categories' => $categories]);
print($layoutContent);
