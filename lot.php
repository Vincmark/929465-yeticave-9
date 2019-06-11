<?php

require 'session.php';
require 'db.php';
require 'functions.php';
require 'helpers.php';

$is_main = 0;
$lotId = -1;
$formParams = [];
$formItemErrors = [];
$formError = false;


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $lotId = intval($_GET['id']);
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $lotId = intval($_POST['lot_id']);
    }
}

if ($lotId < 0) {
    http_response_code(404);
    die();
}

// Записываем новую ставку
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['cost'])) {
        $formItemErrors['cost'] = true;
        $formParams['cost'] = '';
    } else {
        $formParams['cost'] = $_POST['cost'];
        if (!is_numeric($formParams['cost'])) {
            $formItemErrors['cost'] = true;
        } else {
            if (intval($formParams['cost']) < 0) {
                $formItemErrors['cost'] = true;
            } else {
                if ($formParams['cost'] < $_POST['min_price']) {
                    $formItemErrors['cost'] = true;
                }
            }
        }
    }

    $dateNow = strtotime('now');
    $dateLotStop = strtotime($_POST['lot_life_time']);
    if ($dateLotStop <= $dateNow) {
        $formError = true;
    }

    if (count($formItemErrors) > 0) {
        $formError = true;
    }


    if (!$formError) {
        $bet = ['user_id' => $user_id, 'lot_id' => $lotId, 'cost' => $formParams['cost']];
        if (saveNewBet($dbConnection, $bet)) {
            header("Location: /lot.php?id=" . $lotId);
        }
    }
}

// Зачитываем лот
$lot = getLot($dbConnection, $lotId);
if (count($lot) === 0) {
    http_response_code(404);
    die();
}

$categories = getCategories($dbConnection);
$bets = getBets($dbConnection, $lotId);


// Отображаем форму для ставки
$betForm = [];
$betForm['currentPrice'] = $lot['start_price'];
if (count($bets) > 0) {
    $betForm['currentPrice'] = $bets[0]['bet_price'];
}
$betForm['minBetPrice'] = $betForm['currentPrice'] + $lot['bet_step'];


$showBetForm = true;
if (!$is_auth) {
    $showBetForm = false;
}
if (strtotime($lot['stop_date']) < strtotime("now")) {
    $showBetForm = false;
}
if (count($bets) > 0) {
    if ((int)$bets[0]['id_bettor'] === $user_id) {
        $showBetForm = false;
    }
}
if ((int)$lot['id_author'] === $user_id) {
    $showBetForm = false;
}

$pageContent = include_template('lot.php', [
    'categories' => $categories,
    'lot' => $lot,
    'is_auth' => $is_auth,
    'bets' => $bets,
    'betForm' => $betForm,
    'formError' => $formError,
    'formParams' => $formParams,
    'showBetForm' => $showBetForm
]);

$layoutContent = include_template('layout.php', [
    'pageContent' => $pageContent,
    'pageTitle' => $lot['title'],
    'is_auth' => $is_auth,
    'is_main' => $is_main,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layoutContent);