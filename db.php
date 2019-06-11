<?php

$dbError = false;
$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection === false) {
    $dbError = true;
    print("Ошибка подключения: " . mysqli_connect_error());
    die();
}
mysqli_set_charset($dbConnection, "utf8");


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
    }
    return (mysqli_fetch_all($result, MYSQLI_ASSOC));
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
 * Получаем список лотов, которые без победителя, но уже закончились
 * @param $dbConnection - база данных
 * @return array - список лотов
 */
function getLotsForWinners($dbConnection): array
{
    $lots = [];

    $sql = 'select 
    id, 
    title 
    from lots 
    where stop_date >="' . date('y-m-d', strtotime('now')) . '" 
    and id_winner is null';

    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    }
    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return ($lots);
}

/**
 * Получаем список лотов, которые без победителя, но уже закончились
 * @param $dbConnection - база данных
 * @return array - список лотов
 */
function getLastBetForWinner($dbConnection): array
{
    $lots = [];

    $sql = 'select
    id, 
    title 
    from lots 
    where stop_date >="' . date('y-m-d', strtotime('now')) . '" 
    and id_winner is null';

    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    }
    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return ($lots);
}

/**
 * Получаем список лотов по поиску с учетом пагинации
 * @param $dbConnection - база данных
 * @param $query - поисковый запрос
 * @param $page_items - элементов на странице
 * @param $offset - страница
 * @return array - список лотов
 */
function getLotsForSearch($dbConnection, $query, $page_items, $offset): array
{
    $lots = [];
    $sql = 'select 
    l.id as lot_id, 
    l.title, 
    l.description, 
    start_price, 
    lot_img, 
    stop_date, 
    c.title as category_title 
    from lots l 
    join categories c on l.id_category = c.id 
    where stop_date >="' . date('y-m-d', strtotime('now')) . '" 
    and MATCH(l.title,l.description) AGAINST("' . mysqli_real_escape_string($dbConnection, $query) . '")  
    order by date_reg desc limit ' . $page_items . ' offset ' . $offset;

    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    }
    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return ($lots);
}

/**
 * Получаем список лотов по категории с учетом пагинации
 * @param $dbConnection - база данных
 * @param $categoryId - категория
 * @param $page_items - элементов на странице
 * @param $offset - страница
 * @return array - список лотов
 */
function getLotsForCategory($dbConnection, $categoryId, $page_items, $offset): array
{
    $lots = [];
    $sql = 'select 
    l.id as lot_id, 
    l.title, 
    l.description, 
    start_price, 
    lot_img, 
    stop_date, 
    c.title as category_title 
    from lots l 
    join categories c on l.id_category = c.id 
    where stop_date >="' . date('y-m-d', strtotime('now')) . '" 
    and l.id_category=' . $categoryId . '  
    order by date_reg desc limit ' . $page_items . ' offset ' . $offset;
    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    }
    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return ($lots);
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
       id_author,
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
    }
    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if (count($lots) > 0) {
        $lot = $lots[0];
    } else {
        $lot = [];
    }

    return ($lot);
}

/**
 * Получаем количество лотов
 * @param $dbConnection - база данных
 * @param $query - поисковый запрос
 * @return int - количество лотов
 */
function getLotCount($dbConnection, $query): int
{
    $lotsCnt = -1;
    $sql = 'select count(*) as cnt from lots l join categories c on l.id_category = c.id where stop_date >="' . date('y-m-d',
            strtotime('now')) . '" and MATCH(l.title,l.description) AGAINST("' . mysqli_real_escape_string($dbConnection,
            $query) . '")';
    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    }
    $lotsCnt = mysqli_fetch_assoc($result)['cnt'];

    return ($lotsCnt);
}

/**
 * Получаем количество лотов для категории
 * @param $dbConnection - база данных
 * @param $categoryId - категория
 * @return int - количество лотов
 */
function getLotCountByCategory($dbConnection, $categoryId): int
{
    $sql = 'select count(*) as cnt 
        from lots l 
        join categories c on l.id_category = c.id
        where stop_date >="' . date('y-m-d', strtotime('now')) . '" 
        and l.id_category=' . $categoryId;
    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    }
    $lotsCnt = mysqli_fetch_assoc($result)['cnt'];

    return ($lotsCnt);
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

    if (!$executeResult) {
        print("Ошибка MySQL: " . mysqli_errno($dbConnection));
        die();
    }
    $newLotId = mysqli_insert_id($dbConnection);
    mysqli_stmt_close($stmt);
    move_uploaded_file($file['filename'], $file['dest']);

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
 * Получаем категорию по id
 * @param $dbConnection - база данных
 * @param $categoryId - категория
 * @return array - категория
 */
function getCategory($dbConnection, $categoryId): array
{
    $sql = 'select id, 
       title, 
       symbol_code 
        from categories  
        where id=?';

    $stmt = mysqli_prepare($dbConnection, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $categoryId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    }
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if (count($categories) > 0) {
        $category = $categories[0];
    } else {
        $category = [];
    }

    return ($category);
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
 * Получаем последнюю ставку по лоту
 * @param $dbConnection - база данных
 * @param $lotId - лот, для которого получаем ставку
 * @return array - список ставок
 */
function getLastBetForLot($dbConnection, $lotId): array
{
    $bets = [];
    $sql = 'select 
        b.id_bettor, 
        b.id_lot, 
        u.name, 
        u.email 
        from bets b 
        join users u on b.id_bettor = u.id 
        where b.id_lot =' . $lotId . ' 
        order by b.bet_date desc';

    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    }
    $bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return ($bets);
}

/**
 * Сохраняем победителя для лота
 * @param $dbConnection - база данных
 * @param $lotId - лот, для которого получаем ставку
 * @param $winnerId - пользователь-победитель
 * @return bool - результат операции
 */
function saveWinner($dbConnection, $lotId, $winnerId): bool
{
    $isSaved = false;
    $sql = 'update 
        lots set 
        id_winner=' . $winnerId . ' 
        where id=' . $lotId;
    $result = mysqli_query($dbConnection, $sql);
    if (isset($result)) {
        if (!$result) {
            print("Ошибка MySQL: " . mysqli_error($dbConnection));
            die();
        }
        $isSaved = true;
    }
    return ($isSaved);
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
    }
    $bets = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
    }
    $isSaved = true;
    mysqli_stmt_close($stmt);

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
    }
    $num_rows = mysqli_num_rows($result);
    if ($num_rows > 0) {
        $isUnique = false;
    }
    mysqli_stmt_close($stmt);

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
    }
    $isSaved = true;
    mysqli_stmt_close($stmt);

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
    }
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


    return ($authResult);
}