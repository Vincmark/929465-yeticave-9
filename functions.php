<?php


/**
 * Защита от XSS.
 *
 * @param string $str - Обрабатываемая строка.
 * @return string     - Обработанная строка.
 */
function noXSS($rawStr): string
{
    $safeStr = '';
    if (!empty($rawStr)) {
        $safeStr = htmlspecialchars($rawStr);
    }
    return $safeStr;
}

/**
 * Форматирует цену:
 * - округляет до целого
 * - разделяет пробелом, если больше 1000
 * - добавляет знак рубля
 *
 * @param int $price Стоимость товара
 * @return string
 */
function preparePrice(int $price): string
{
    $price = ceil($price);
    $priceStr = number_format($price, 0, '', ' ');
    $priceStr = $priceStr . ' ₽';
    return $priceStr;
}

/**
 * Возвращает строку с часами и минутами до конца жизни лота
 *
 * @return string
 */
function getHoursAndMinutesBeforeLotEnd(string $endDate): string
{
    $dateNow = strtotime('now');
    $dateEnd = strtotime($endDate);
    $dateDiff = $dateEnd - $dateNow;
    if ($dateDiff < 0) {
        $hours = 0;
        $minutes = 0;
    } else {
        $hours = floor($dateDiff / 60 / 60);
        $minutes = floor(($dateDiff - $hours * 60 * 60) / 60);
    }
    return sprintf("%02d", $hours) . ":" . sprintf("%02d", $minutes);
}

/**
 * Возвращает количество минут до конца жизни ставки
 *
 * @return int
 */
function getMinutesBeforeLotEnd(string $endDate): int
{
    $dateNow = strtotime('now');
    $dateEnd = strtotime($endDate);
    return floor(($dateEnd - $dateNow) / 60);
}

/**
 * Возвращает true, если от текущей даты до конца жизни лота осталось меньше 1 часа
 *
 * @param string $endDate дата окончания жизни лота
 * @return bool
 */
function lessThanHourBeforeLotEnd(string $endDate): bool
{
    $dateNow = strtotime('now');
    $dateEnd = strtotime($endDate);
    $dateDiff = $dateEnd - $dateNow;
    if ($dateDiff < 0) {
        return true;
    } else {
        if ($dateDiff / 60 >= 60) {
            return false;
        } else {
            return true;
        }
    }
}

/**
 * Возвращает разницу от даты публикации ставки в виде количества минут, часов или отформатированной даты
 *
 * @param string $postDate дата опубликации лота
 * @return string
 */
function getTimeString(string $postDate): string
{
    $dateNow = strtotime('now');
    $datePost = strtotime($postDate);
    $dateDiff = $dateNow - $datePost;

    if ($dateDiff < 60 * 60) {
        $result = floor($dateDiff / 60) . ' мин назад';
    } else {
        if (($dateDiff > 60 * 60) && ($dateDiff < 60 * 60 * 24)) {
            $result = floor($dateDiff / 60 / 60) . ' ч назад';
        } else {
            $result = date("d.m.y в h:i", $datePost);         //19.03.17 в 12:20
        }
    }
    return $result;
}

/**
 * Рассылает письма победителям
 *
 * @param array $winnerList список победителей с параметрами для отправки писем
 * @return bool
 */
function sendWinnerMails(array $winnerList)
{
    // Конфигурация траспорта
    $transport = (new Swift_SmtpTransport('phpdemo.ru', 25))
        ->setUsername('keks@phpdemo.ru')
        ->setPassword('htmlacademy');
    foreach ($winnerList as $winner) {
        // Формирование сообщения
        $message = new Swift_Message("Ваша ставка победила");
        $message->setTo([$winner['user_email'] => $winner['user_name']]);

        $mailContent = include_template('email.php', [
            'userName' => $winner['user_name'],
            'lotId' => $winner['lot_id'],
            'lotTitle' => $winner['lot_title']
        ]);

        $message->setBody($mailContent);
        $message->setFrom("keks@phpdemo.ru", "keks@phpdemo.ru");

        // Отправка сообщения
        $mailer = new Swift_Mailer($transport);
        $result = $mailer->send($message);
        print($result);
    }
}