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
$lotId = -1;
$formParams = [];
$formItemErrors = [];
$formError = false;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $lotId = intval($_GET['id']);
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lotId = intval($_POST['lot_id']);
    echo "POST";
}
if ($lotId < 0) {
    http_response_code(404);
    die();
}

$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection == false) {
    print("Ошибка подключения: ". mysqli_connect_error());
    die();
} else {
    mysqli_set_charset($dbConnection, "utf8");
    // Записываем новую ставку
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (empty($_POST['cost'])) {
            $formItemErrors['cost'] = true;
            $formParams['cost'] = '';
        } else {
            $formParams['cost'] = $_POST['cost'];
            if (!is_numeric($formParams['cost']))
                $formItemErrors['cost'] = true;
            else if (intval($formParams['cost'])<0)
                $formItemErrors['cost'] = true;
            else if ($formParams['cost'] < $_POST['min_price'])
                $formItemErrors['cost'] = true;
        }


        if (count($formItemErrors)>0)
            $formError = true;
        if (!$formError) {
            $sql = 'insert into bets (id_bettor, id_lot, bet_price) VALUES (?, ?, ?)';
            $stmt = mysqli_stmt_init($dbConnection);
            mysqli_stmt_prepare($stmt, $sql);
            $bindResult = mysqli_stmt_bind_param($stmt, 'iii', $user_id, $lotId, $formParams['cost']);
            $executeResult = mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (!$executeResult) {
                print("Ошибка MySQL: " . mysqli_errno($dbConnection));
                die();
            } else {
                mysqli_stmt_close($stmt);
                echo "saved";
            }
        }
    }

    // Зачитываем лот
    $sql = 'select l.id as id, l.title, description, start_price, bet_step, lot_img, stop_date, c.title as category_title from lots l join categories c on l.id_category = c.id where l.id=?';
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

    // Отображаем историю ставок
    $sql = 'select b.id, id_bettor, id_lot, bet_date, bet_price, u.name, u.id from bets b join users u on u.id = id_bettor where b.id_lot = '.$lotId. ' order by bet_date desc';
    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    } else {
        $bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo count($bets);
    }

    // Отображаем форму для ставки
    $betForm = [];
    if (count($bets) > 0) {
        $betForm['currentPrice'] = $bets[0]['bet_price'];
    } else {
        $betForm['currentPrice'] = $lot['start_price'];
    }
    $betForm['minBetPrice'] = $betForm['currentPrice'] + $lot['bet_step'];
}

$pageContent = include_template('lot.php', ['categories' => $categories , 'lot' => $lot, 'is_auth' => $is_auth, 'bets' => $bets, 'betForm' => $betForm, 'formError' => $formError, 'formParams' => $formParams]);
$layoutContent = include_template('layout.php',['pageContent' => $pageContent, 'pageTitle' => $lot['title'], 'is_auth' => $is_auth, 'is_main' => $is_main, 'user_name' => $user_name, 'categories' => $categories]);
print($layoutContent);