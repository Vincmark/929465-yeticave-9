<?php

$dbError = false;
$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection == false) {
    $dbError = true;
    print("Ошибка подключения: " . mysqli_connect_error());
    die();
} else {
    mysqli_set_charset($dbConnection, "utf8");
}

/**
 * Делаем запрос к базе данных
 * @param $dbConnection - база данных
 * @param $sql - запрос
 * @return array - результирующий список
 */
function sendQuery($dbConnection, $sql): array
{
    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    } else {
        return (mysqli_fetch_all($result, MYSQLI_ASSOC));
    }
}

/**
 * Получаем список актуальных лотов
 * @param $dbConnection - база данных
 * @return array - список лотов
 */
function getLots($dbConnection): array
{
    $sql = 'select 
        l.id as lot_id, 
        l.title, 
        start_price, 
        lot_img, 
        stop_date, 
        c.title as category_title 
        from lots l 
        join categories c on l.id_category = c.id 
        where stop_date >="' . date('y-m-d', strtotime('now')) . '" 
        order by date_reg desc';

    return (sendQuery($dbConnection, $sql));
}

/**
 * Зачитываем лот
 * @param $dbConnection - база данных
 * @param $lotId - лот
 * @return array - параметры лота
 */
function getLot($dbConnection, $lotId): array
{
    $sql = 'select l.id as id, 
        l.title, 
        description, 
        start_price, 
        bet_step, 
        lot_img, 
        stop_date, 
        c.title as category_title 
        from lots l 
        join categories c on l.id_category = c.id 
        where l.id=?';

    $stmt = mysqli_prepare($dbConnection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $lotId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    } else {
        $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if (count($lots) > 0) {
            $lot = $lots[0];
        } else {
            $lot = [];
        }
    }
    return ($lot);
}

/**
 * Сохраняем новый лот
 * @param $dbConnection - база данных
 * @param $lot - данные лота для сохранения
 * @param $file - данные сохраняемого файла
 * @return int - id нового лота
 */
function saveNewLot($dbConnection, $lot, $file): int
{
    $newLotId = -1;
    $sql = 'insert into lots 
    (id_category, 
     id_author, 
     title, 
     description, 
     lot_img, 
     start_price, 
     bet_step, 
     stop_date) 
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

    $stmt = mysqli_stmt_init($dbConnection);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, 'iisssiis',
        $lot['category'],
        $lot['author'],
        $lot['lot-name'],
        $lot['message'],
        $lot['image'],
        $lot['lot-rate'],
        $lot['lot-step'],
        $lot['lot-date']);

    $executeResult = mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);
    echo $sql;

    if (!$executeResult) {
        print("Ошибка MySQL: " . mysqli_errno($dbConnection));
        die();
    } else {
        $newLotId = mysqli_insert_id($dbConnection);
        mysqli_stmt_close($stmt);
        move_uploaded_file($file['filename'], $file['dest']);
    }
    return ($newLotId);
}

/**
 * Получаем список категорий
 * @param $dbConnection - база данных
 * @return array - список категорий
 */
function getCategories($dbConnection): array
{
    // Зачитываем категории
    $sql = 'select 
        id, 
        title,
        symbol_code 
        from categories';

    return (sendQuery($dbConnection, $sql));
}

/**
 * Получаем список ставок
 * @param $dbConnection - база данных
 * @param $lotId - лот, для которого получаем список ставок
 * @return array - список ставок
 */
function getBets($dbConnection, $lotId): array
{
    $sql = 'select 
      b.id, 
      id_bettor, 
      id_lot, 
      bet_date, 
      bet_price, 
      u.name, 
      u.id 
      from bets b 
      join users u on u.id = id_bettor 
      where b.id_lot = ' . $lotId . ' 
      order by bet_date desc';

    return (sendQuery($dbConnection, $sql));
}

/**
 * Получаем список ставок для пользователя
 * @param $dbConnection - база данных
 * @param $userId - пользователь, для которого получаем ставки
 * @return array - список ставок
 */
function getBetsForUser($dbConnection, $userId): array
{
    $bets = [];
    $sql = 'select 
        b.id,
        u.contact, 
        b.bet_price,
        l.id_winner, 
        l.title as lot_title, 
        b.bet_date, 
        l.stop_date, 
        b.id_bettor, 
        b.id_lot, 
        l.lot_img as lot_img, 
        l.id as lot_id, 
        u.id, 
        c.title as category_title  
        from bets b 
        join lots l on b.id_lot=l.id 
        join users u on b.id_bettor=u.id 
        join categories c on l.id_category=c.id 
        where b.id_bettor=? 
        order by bet_date desc ';

    $stmt = mysqli_prepare($dbConnection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    } else {
        $bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    return ($bets);
}

/**
 * Сохраняем новую ставку
 * @param $dbConnection - база данных
 * @param $bet - параметры ставки
 * @return bool - результат добавления
 */
function saveNewBet($dbConnection, $bet): bool
{
    $isSaved = false;
    $sql = 'insert into bets (id_bettor, id_lot, bet_price) VALUES (?, ?, ?)';
    $stmt = mysqli_stmt_init($dbConnection);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, 'iii', $bet['user_id'], $bet['lot_id'], $bet['cost']);
    $executeResult = mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);
    if (!$executeResult) {
        print("Ошибка MySQL: " . mysqli_errno($dbConnection));
        die();
    } else {
        $isSaved = true;
        mysqli_stmt_close($stmt);
    }
    return ($isSaved);
}


/**
 * Проверяем емейл на уникальность
 * @param $dbConnection - база данных
 * @param $email - емейл для проверки
 * @return bool - уникальный ли емейл
 */
function checkForUniqueEmail($dbConnection, $email): bool
{
    $isUnique = true;
    $sql = 'select * from users where email = ?';
    $stmt = mysqli_stmt_init($dbConnection);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    $executeResult = mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$executeResult) {
        print("Ошибка MySQL: " . mysqli_errno($dbConnection));
        die();
    } else {
        $num_rows = mysqli_num_rows($result);
        if ($num_rows > 0) {
            $isUnique = false;
        }
        mysqli_stmt_close($stmt);
    }
    return ($isUnique);
}

/**
 * Сохраняем пользователя
 * @param $dbConnection - база данных
 * @param $user - параметры пользователя для сохранения
 * @return bool - сохранен ли пользователь
 */
function saveNewUser($dbConnection, $user): bool
{
    $isSaved = false;

    $sql = 'insert into users (email, name, password, contact) VALUES (?, ?, ?, ?)';
    $stmt = mysqli_stmt_init($dbConnection);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, 'ssss', $user['email'], $user['name'], $user['password'], $user['message']);
    $executeResult = mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);
    if (!$executeResult) {
        print("Ошибка MySQL: " . mysqli_errno($dbConnection));
        die();
    } else {

        $isSaved = true;
        mysqli_stmt_close($stmt);
    }

    return ($isSaved);
}

/**
 * Проверяем аутентификацию
 * @param $dbConnection - база данных
 * @param $user - параметры пользователя
 * @return array - результат аутентификация
 */
function userAuthentication($dbConnection, $user): array
{
    $authResult['result'] = true;

    $sql = 'select id, email, name, password from users where email = ?';
    $stmt = mysqli_stmt_init($dbConnection);
    mysqli_stmt_prepare($stmt, $sql);
    mysqli_stmt_bind_param($stmt, 's', $user['email']);
    $executeResult = mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$executeResult) {
        print("Ошибка MySQL: " . mysqli_errno($dbConnection));
        die();
    } else {
        $num_rows = mysqli_num_rows($result);
        if ($num_rows > 0) {
            $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if (password_verify($user['password'], $users[0]['password'])) {
                $authResult['username'] = $users[0]['name'];
                $authResult['user_id'] = $users[0]['id'];
            } else {
                $authResult['result'] = false;
            }
        } else {
            $authResult['result'] = false;
        }
        mysqli_stmt_close($stmt);
    }

    return ($authResult);
}