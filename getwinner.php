<?php

echo "Get Winner Works!";
$winnerList = [];

$dbConnection = mysqli_connect("localhost", "root", "", "yeticave");
if ($dbConnection == false) {
    print("Ошибка подключения: ". mysqli_connect_error());
    die();
} else {
    mysqli_set_charset($dbConnection, "utf8");

    // Зачитываем лоты, которые без виннера, и уже закончились
    $sql = 'select id, title from lots where stop_date >="'.date('y-m-d',strtotime('now')).'" and id_winner is null';
    echo $sql."<br>";
    $result = mysqli_query($dbConnection, $sql);
    if (!$result) {
        print("Ошибка MySQL: " . mysqli_error($dbConnection));
        die();
    } else {
        $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach ($lots as $lot){
            // выбираем самую последнюю ставку по лоту
            $sql = 'select b.id_bettor, b.id_lot, u.name, u.email from bets b join users u on b.id_bettor = u.id where b.id_lot ='.$lot['id'].' order by b.bet_date desc';
            echo $sql."<br>";
            $result = mysqli_query($dbConnection, $sql);
            if (!$result) {
                print("Ошибка MySQL: " . mysqli_error($dbConnection));
                die();
            } else {
                $bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo count($bets)."<br>";
                if (count($bets) > 0) {
                    $winnerList[$lot['id']]['lot_title'] = $lot['title'];
                    $winnerList[$lot['id']]['id_user'] = $bets[0]['id_bettor'];
                    $winnerList[$lot['id']]['user_name'] = $bets[0]['name'];
                    $winnerList[$lot['id']]['user_email'] = $bets[0]['email'];

                    // записываем победителя
                    $sql = 'update lots set id_winner='.$bets[0]['id_bettor'].' where id='.$bets[0]['id_lot'];
                    echo $sql."<br>";
                    $result = mysqli_query($dbConnection, $sql);
                    if (!$result) {
                        print("Ошибка MySQL: " . mysqli_error($dbConnection));
                        die();
                    } else {
                        // отправляем письма
                        sendWinnerMails($winnerList);
                    }
                }
            }
        }
    }
    echo "<pre>";
    var_dump($winnerList);
    echo "</pre>";
}