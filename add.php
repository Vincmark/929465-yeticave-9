<?php

require 'functions.php';
require 'helpers.php';
$is_auth = rand(0, 1);
$user_name = 'Алексей Кошевой';
$is_main = 0;

$formParams = [];
$formItemErrors = [];
$formError = false;

$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection == false) {
    print("Ошибка подключения: ". mysqli_connect_error());
    die();
} else {
    mysqli_set_charset($dbConnection, "utf8");
}

// Обрабатываем добавление или просто показываем форму?
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // lot-name
    if (empty($_POST['lot-name'])) {
        $formItemErrors['lot-name'] = true;
        $formParams['lot-name'] = '';
    } else {
        $formParams['lot-name'] = $_POST['lot-name'];
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
        $formParams['message'] = $_POST['message'];
    }

    // lot-rate
    if (empty($_POST['lot-rate'])) {
        $formItemErrors['lot-rate'] = true;
        $formParams['lot-rate'] = '';
    } else {
        $formParams['lot-rate'] = $_POST['lot-rate'];
        if (!is_numeric($formParams['lot-rate']))
            $formItemErrors['lot-rate'] = true;
    }

    // lot-step
    if (empty($_POST['lot-step'])) {
        $formItemErrors['lot-step'] = true;
        $formParams['lot-step'] = '';
    } else {
        $formParams['lot-step'] = $_POST['lot-step'];
        if (((string)intval($formParams['lot-step'])) !== $formParams['lot-step'])
            $formItemErrors['lot-step'] = true;
    }

    // lot-date
    if (empty($_POST['lot-date'])) {
        $formItemErrors['lot-date'] = true;
        $formParams['lot-date'] = '';
    } else {
        $formParams['lot-date'] = $_POST['lot-date'];
        if (!is_date_valid($formParams['lot-date']))
            $formItemErrors['lot-date'] = true;
    }
    // image
    if (isset($_FILES['image'])) {
        $file_name = $_FILES['image']['name'];
        $file_path = __DIR__. '/uploads/';
        $file_url = '/uploads/' . $file_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $file_path . $file_name);
        print("<a href='$file_url'>$file_name<a>");
    }

    if (count($formItemErrors)>0)
        $formError = true;


    echo "<pre>";
    var_dump($formParams);
    var_dump($formItemErrors);
    var_dump($formError);
    echo "</pre>";

    // Сохраняем новый лот

    if (!$formError) {
    //            id_category int not null,
    //            id_author int not null,
    //            id_winner int default null,
    //            title char(128),
    //            description varchar(4096),
    //            lot_img char(255),
    //            start_price decimal,
    //            bet_step decimal,
    //            stop_date date,
    //    insert into lots (id_category,id_author,title,description,lot_img,start_price,bet_step,stop_date) VALUES (5, 2,  'Маска Oakley Canopy', 'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчкоми четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.', 'lot-6.jpg', 5400, 1000, '2017-05-26');
//        $sql = 'select l.id, l.title, description, start_price, lot_img, stop_date, c.title as category_title from lots l join categories c on l.id_category = c.id where l.id=?';

        $sql = 'insert into lots (id_category,id_author,title,description,lot_img,start_price,bet_step,stop_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = mysqli_prepare($dbConnection, $sql);
        mysqli_stmt_bind_param($stmt, 'iisssdis', $formParams['category'], 1, $formParams['author'], $formParams['lot-name'], $formParams['message'],$formParams['image'],$formParams['lot_rate'],$formParams['lot_step'],$formParams['lot-date']);
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
                print('no such id');
                http_response_code(404);
                die();
            }
        }

        // перенаправление

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


$pageContent = include_template('add.php', ['categories' => $categories, 'formParams' => $formParams, 'formError' => $formError, 'formItemErrors' => $formItemErrors]);
$layoutContent = include_template('layout.php',['pageContent' => $pageContent, 'pageTitle' => 'Добавить лот', 'is_auth' => $is_auth, 'is_main' => $is_main, 'user_name' => $user_name, 'categories' => $categories]);
print($layoutContent);