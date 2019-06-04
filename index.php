<?php

require_once "vendor/autoload.php";


require 'session.php';
require 'db.php';
require 'functions.php';
require 'helpers.php';
require 'getwinner.php';

$is_main = 1;

$lots = getLots($dbConnection);
$categories = getCategories($dbConnection);

$pageContent = include_template('index.php', [
    'categories' => $categories,
    'lots' => $lots
]);

$layoutContent = include_template('layout.php', [
    'pageContent' => $pageContent,
    'pageTitle' => 'Главная',
    'is_auth' => $is_auth,
    'is_main' => $is_main,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layoutContent);