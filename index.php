<?php

require 'functions.php';
require 'helpers.php';
$is_auth = rand(0, 1);
$user_name = 'Алексей Кошевой';
$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];
$lots = [
    ['title' => '2014 Rossignol District Snowboard1',
        'category' => 'Доски и лыжи',
        'price' => '10999',
        'imageUrl' => 'img/lot-1.jpg'],

    ['title' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => '159999',
        'imageUrl' => 'img/lot-2.jpg'],

    ['title' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => '8000',
        'imageUrl' => 'img/lot-3.jpg'],

    ['title' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => '10999',
        'imageUrl' => 'img/lot-4.jpg'],

    ['title' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => '7500',
        'imageUrl' => 'img/lot-5.jpg'],

    ['title' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => '5400',
        'imageUrl' => 'img/lot-6.jpg']
];


$pageContent = include_template('index.php', ['categories' => $categories , 'lots' => $lots]);
$layoutContent = include_template('layout.php',['pageContent' => $pageContent, 'pageTitle' => 'Главная', 'is_auth' => $is_auth, 'user_name' => $user_name, 'categories' => $categories]);
print($layoutContent);