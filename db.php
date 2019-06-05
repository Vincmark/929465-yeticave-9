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
 *
 * @return mixed - список лотов
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
 * Получаем список категорий
 * @return mixed - список лотов
 */
function getCategories($dbConnection): array
{
    // Зачитываем категории
    $sql = 'select 
        title,
        symbol_code 
        from categories';

    return (sendQuery($dbConnection, $sql));
}

/**
 * Получаем список категорий
 * @return mixed - список лотов
 */
function getBets($dbConnection): array
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
