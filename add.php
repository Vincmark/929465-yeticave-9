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
    http_response_code(403);
    die();
}

$is_main = 0;


$formParams = [];
$formItemErrors = [];
$formError = false;

$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
    die();
} else {
    mysqli_set_charset($dbConnection, "utf8");
}

// Обрабатываем добавление или просто показываем форму?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // lot-name
    if (empty($_POST['lot-name'])) {
        $formItemErrors['lot-name'] = true;
        $formParams['lot-name'] = '';
    } else {
        $formParams['lot-name'] = mysqli_real_escape_string($dbConnection, $_POST['lot-name']);
    }

    // category
    if (empty($_POST['category'])) {
        $formItemErrors['category'] = true;
        $formParams['category'] = '';
    } else {
        $formParams['category'] = $_POST['category'];
    }

    // message
    if (empty($_POST['message'])) {
        $formItemErrors['message'] = true;
        $formParams['message'] = '';
    } else {
        $formParams['message'] = mysqli_real_escape_string($dbConnection, $_POST['message']);
    }

    // lot-rate
    if (empty($_POST['lot-rate'])) {
        $formItemErrors['lot-rate'] = true;
        $formParams['lot-rate'] = '';
    } else {
        $formParams['lot-rate'] = $_POST['lot-rate'];
        if (!is_numeric($formParams['lot-rate'])) {
            $formItemErrors['lot-rate'] = true;
        } else {
            if (intval($formParams['lot-rate']) < 0) {
                $formItemErrors['lot-rate'] = true;
            }
        }
    }

    // lot-step
    if (empty($_POST['lot-step'])) {
        $formItemErrors['lot-step'] = true;
        $formParams['lot-step'] = '';
    } else {
        $formParams['lot-step'] = $_POST['lot-step'];
        if (((string)intval($formParams['lot-step'])) !== $formParams['lot-step']) {
            $formItemErrors['lot-step'] = true;
        } else {
            if (intval($formParams['lot-step']) < 0) {
                $formItemErrors['lot-step'] = true;
            }
        }
    }

    // lot-date
    if (empty($_POST['lot-date'])) {
        $formItemErrors['lot-date'] = true;
        $formParams['lot-date'] = '';
    } else {
        $formParams['lot-date'] = $_POST['lot-date'];
        if (!is_date_valid($formParams['lot-date'])) {
            $formItemErrors['lot-date'] = true;
        } else {
            $dateNow = strtotime('now');
            $dateLot = strtotime($formParams['lot-date']);
            if (($dateLot - $dateNow) < 24 * 60 * 60) {
                $formItemErrors['lot-date'] = true;
            }
        }
    }
    // image
    $formParams['image'] = '';
    if (isset($_FILES['image'])) {
        if ($_FILES['image']['error'] !== 0) {
            $formItemErrors['image'] = true;
        }
        if (!isset($formItemErrors['image'])) {
            $file_temp_name = $_FILES['image']['tmp_name'];
            $file_mime = mime_content_type($file_temp_name);
            if (($file_mime !== 'image/png') && ($file_mime !== 'image/jpeg')) {
                $formItemErrors['image'] = true;
            }
        }
        if (!isset($formItemErrors['image'])) {
            $path_parts = pathinfo($_FILES['image']['name']);
            $file_name = uniqid() . '.' . $path_parts['extension'];
            $file_path = __DIR__ . '/uploads/';
            $file_url = '/uploads/' . $file_name;
            $formParams['image'] = $file_name;
        }
    } else {
        $formItemErrors['image'] = true;
    }
    $formParams['author'] = '1';
    if (count($formItemErrors) > 0) {
        $formError = true;
    }


    // Сохраняем новый лот
    if (!$formError) {

        $sql = 'insert into lots (id_category, id_author, title, description, lot_img, start_price, bet_step, stop_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = mysqli_stmt_init($dbConnection);
        mysqli_stmt_prepare($stmt, $sql);
        $bindResult = mysqli_stmt_bind_param($stmt, 'iisssiis', $formParams['category'], $formParams['author'],
            $formParams['lot-name'], $formParams['message'], $formParams['image'], $formParams['lot-rate'],
            $formParams['lot-step'], $formParams['lot-date']);
        $executeResult = mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (!$executeResult) {
            print("Ошибка MySQL: " . mysqli_errno($dbConnection));
            die();
        } else {
            $newLotId = mysqli_insert_id($dbConnection);
            mysqli_stmt_close($stmt);
            move_uploaded_file($_FILES['image']['tmp_name'], $file_path . $file_name);
            header("Location: /lot.php?id=" . $newLotId);
        }
    }
}

// Зачитываем категории
$sql = 'select id,title,symbol_code from categories';
$result = mysqli_query($dbConnection, $sql);
if (!$result) {
    print("Ошибка MySQL: " . mysqli_error($dbConnection));
} else {
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
}


$pageContent = include_template('add.php', [
    'categories' => $categories,
    'formParams' => $formParams,
    'formError' => $formError,
    'formItemErrors' => $formItemErrors
]);

$layoutContent = include_template('layout.php', [
    'pageContent' => $pageContent,
    'pageTitle' => 'Добавление лота',
    'is_auth' => $is_auth,
    'is_main' => $is_main,
    'user_name' => $user_name,
    'categories' => $categories
]);

print($layoutContent);