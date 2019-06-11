<?php

require 'session.php';
require 'db.php';
require 'functions.php';
require 'helpers.php';

$is_main = 0;


$lots = [];
$pagination = [];
$pages = [];
$page_items = 9;


// if page has GET
if (!$_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(404);
    die();
}

// if category parameter is set
if (!isset($_GET['category'])) {
    http_response_code(404);
    die();
}

// if category parameter is valid as number
$categoryId = intval($_GET['category']);
if ($categoryId === 0) {
    http_response_code(404);
    die();
}

// if category is present in DB
$category = getCategory($dbConnection, $categoryId);
if (count($category) === 0) {
    http_response_code(404);
    die();
}

// checking for page parameter
if (isset($_GET['page'])) {
    if (!intval($_GET['page'])) {
        $cur_page = 1;
    } else {
        $cur_page = $_GET['page'];
    }
} else {
    $cur_page = 1;
}

// setting up pagination parameters
$items_count = getLotCountByCategory($dbConnection, $categoryId);
$pages_count = (int)ceil($items_count / $page_items);
if ($pages_count === 0) {
    $pages_count = 1;
}
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

// Reading lots
$lots = getLotsForCategory($dbConnection, $categoryId, $page_items, $offset);
if (count($lots) === 0) {
    $formError = true;
}

$categories = getCategories($dbConnection);

$pageContent = include_template('all-lots.php', [
    'categories' => $categories,
    'category' => $category,
    'lots' => $lots,
    'pages' => $pages,
    'pagination' => $pagination
]);

$layoutContent = include_template('layout.php', [
    'pageContent' => $pageContent,
    'pageTitle' => 'Все лоты в категории ' . $category['title'],
    'is_auth' => $is_auth,
    'is_main' => $is_main,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layoutContent);