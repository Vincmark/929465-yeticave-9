<?php

require 'session.php';
require 'db.php';
require 'functions.php';
require 'helpers.php';

$is_main = 0;


$formError = false;
$lots = [];
$pagination = [];
$pages = [];
$page_items = 9;
$query = '';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['search'])) {
        $query = trim($_GET['search']);
    } else {
        $formError = true;
    }

    if (!$formError && (empty($query) || (strlen($query) <= 3))) {
        $formError = true;
    }

    if (isset($_GET['page'])) {
        if (!intval($_GET['page'])) {
            $cur_page = 1;
        } else {
            $cur_page = $_GET['page'];
        }
    } else {
        $cur_page = 1;
    }
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

if (!$formError) {

    $items_count = getLotCount($dbConnection, $query);

    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;

    $pages = range(1, $pages_count);
    $pagination['prev'] = $cur_page - 1;
    if ($pagination['prev'] < 1) {
        $pagination['prev'] = 1;
    }
    $pagination['next'] = $cur_page + 1;
    if ($pagination['next'] > $pages_count) {
        $pagination['next'] = $cur_page;
    }
    $pagination['current'] = $cur_page;

    // Зачитываем лоты
    $lots = getLotsForSearch($dbConnection, $query, $page_items, $offset);
    if (count($lots) === 0) {
        $formError = true;
    }
}


$pageContent = include_template('search.php', [
    'categories' => $categories,
    'lots' => $lots,
    'query' => $query,
    'formError' => $formError,
    'pages' => $pages,
    'pagination' => $pagination
]);

$layoutContent = include_template('layout.php', [
    'pageContent' => $pageContent,
    'pageTitle' => 'Результаты поиска',
    'is_auth' => $is_auth,
    'is_main' => $is_main,
    'user_name' => $user_name,
    'categories' => $categories,
    'query' => $query
]);

print($layoutContent);