<?php
function preparePrice($amount){
    if (!is_numeric($amount)) {
        return $amount;
    }
    $amount = ceil($amount);
    $amountStr = (string)$amount;

    //$amountStr = number_format($amount, 0, '', ' ');

    if ($amount > 1000){
        $amountStrLen = strlen($amountStr);
        $amountStr = substr($amountStr,0,$amountStrLen-3).' '.substr($amountStr,$amountStrLen-3,$amountStrLen);
    }

    $amountStr = $amountStr . ' ₽';
    return $amountStr;
}
