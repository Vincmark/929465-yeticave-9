<?php

$winnerList = [];
$error = false;


// Зачитываем лоты, которые без победителя, но уже закончились
if (!$error) {
    $lots = getLotsForWinners($dbConnection);
}

if (!$error) {
    foreach ($lots as $lot) {
        $bets = getLastBetForLot($dbConnection, $lot['id']);

        if (count($bets) > 0) {
            $winnerList[$lot['id']]['lot_id'] = $lot['id'];
            $winnerList[$lot['id']]['lot_title'] = $lot['title'];
            $winnerList[$lot['id']]['id_user'] = $bets[0]['id_bettor'];
            $winnerList[$lot['id']]['user_name'] = $bets[0]['name'];
            $winnerList[$lot['id']]['user_email'] = $bets[0]['email'];

            // записываем победителя
            if (!saveWinner($dbConnection, $bets[0]['id_lot'], $bets[0]['id_bettor'])) {
                print("Ошибка Winner не сохранен: Лот: " . $bets[0]['id_lot'] . ", победитель:" . $bets[0]['id_bettor'] . "<br>");
            }

        }
    }
}

// отправляем письма
if (!$error) {
    sendWinnerMails($winnerList);
}



