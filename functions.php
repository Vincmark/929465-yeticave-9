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
 * Возвращает строку с часами и минутами до конца жизни лота
 *
 * @return string
 */
function getHoursAndMinutesBeforeLotEnd(string $endDate): string
{
    $dateNow = strtotime('now');
    $dateEnd = strtotime($endDate);
    $dateDiff = $dateEnd - $dateNow;
    if ($dateDiff<0){
        $hours = 0;
        $minutes = 0;
    } else {
        $hours = floor($dateDiff/60/60);
        $minutes = floor(($dateDiff-$hours*60*60)/60);
    }
    return sprintf("%02d", $hours).":".sprintf("%02d", $minutes);
}

/**
 * Возвращает true, если от текущей даты до конца жизни лота осталось меньше 1 часа
 *
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