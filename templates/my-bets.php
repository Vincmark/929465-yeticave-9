<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($bets as $bet): ?>
        <tr class="rates__item">
            <td class="rates__info">
                <div class="rates__img">
                    <img src="/uploads/<?= $bet['lot_img'] ?>" width="54" height="40" alt="Сноуборд">
                </div>
                <h3 class="rates__title"><a href="lot.html?id=<?= $bet['lot_id'] ?>"><?= $bet['lot_title'] ?></a></h3>
            </td>
            <td class="rates__category">
                <?= $bet['category_title'] ?>
            </td>
            <td class="rates__timer">
                <div class="timer timer--finishing"><?= getHoursAndMinutesBeforeLotEnd($bet['stop_date']); ?></div>
            </td>
            <td class="rates__price">
                <?= preparePrice($bet['bet_price']) ?>
            </td>
            <td class="rates__time">
                <?= getTimeString($bet['bet_date']) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>