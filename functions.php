<?php
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
 * Возвращает строку с часами и минутами до конца текущих суток
 *
 * @return string
 */
function getHoursAndMinutesBeforeMidnight(): string
{
    $dateNow = strtotime('now');
    $dateNextDay = strtotime('next day midnight');
    $dateDiff = $dateNextDay - $dateNow;
    $hours = floor($dateDiff/60/60);
    $minutes = floor(($dateDiff-$hours*60*60)/60);
    return sprintf("%02d", $hours).":".sprintf("%02d", $minutes);
}

/**
 * Возвращает true, если от текущей даты до конца суок осталось меньше 1 часа
 *
 * @return bool
 */
function lessThanHourBeforeMidnight(): bool
{
    $dateNow = strtotime('now');
    $dateNextDay = strtotime('next day midnight');
    $dateDiff = $dateNextDay - $dateNow;
    if ($dateDiff/60 >= 60) {
        return false;
    } else {
        return true;
    }
}