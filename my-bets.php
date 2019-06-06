<?php

require 'session.php';
require 'db.php';
require 'functions.php';
require 'helpers.php';

if ($is_auth === 0) {
    http_response_code(403);
    die();
}

$is_main = 0;

// Зачитываем ставки
$bets = getBetsForUser($dbConnection, $user_id);
$i = 0;
foreach ($bets as $bet) {
    $bet['minutesBeforeLotEnd'] = getMinutesBeforeLotEnd($bet['stop_date']);
    $bets[$i]['minutesBeforeLotEnd'] = $bet['minutesBeforeLotEnd'];
    if ($bet['minutesBeforeLotEnd'] > 0) {
        if ($bet['minutesBeforeLotEnd'] < 60) {
            $bets[$i]['lotState'] = 'red';
            // красный
        } else {
            $bets[$i]['lotState'] = 'normal';
            // нормальный
        }
    } else {
        if ($bet['id_winner'] === $user_id) {
            // победитель
            $bets[$i]['lotState'] = 'winner';
        } else {
            // лот закрыт
            $bets[$i]['lotState'] = 'closed';
        }
    }
    $i++;
}

$categories = getCategories($dbConnection);

$pageContent = include_template('my-bets.php', [
    'bets' => $bets
]);

$layoutContent = include_template('layout.php', [
    'pageContent' => $pageContent,
    'pageTitle' => 'Мои ставки',
    'is_auth' => $is_auth,
    'is_main' => $is_main,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layoutContent);
