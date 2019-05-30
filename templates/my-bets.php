<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($bets as $bet): ?>
        <?php if ($bet['lotState'] === 'red'):?>
                <tr class="rates__item">
                    <td class="rates__info">
                        <div class="rates__img">
                            <img src="/uploads/<?= $bet['lot_img'] ?>" width="54" height="40" alt="<?= $bet['lot_title'] ?>">
                        </div>
                        <h3 class="rates__title"><a href="lot.php?id=<?= $bet['lot_id'] ?>"><?= $bet['lot_title'] ?></a></h3>
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
        <?php elseif ($bet['lotState'] === 'winner'): ?>
                <tr class="rates__item rates__item--win">
                    <td class="rates__info">
                        <div class="rates__img">
                            <img src="/uploads/<?= $bet['lot_img'] ?>" width="54" height="40" alt="<?= $bet['lot_title'] ?>">
                        </div>
                        <div>
                            <h3 class="rates__title"><a href="lot.php?id=<?= $bet['lot_id'] ?>"><?= $bet['lot_title'] ?></a></h3>
                            <p><?= $bet['contact'] ?></p>
                        </div>
                    </td>
                    <td class="rates__category">
                        <?= $bet['category_title'] ?>
                    </td>
                    <td class="rates__timer">
                        <div class="timer timer--win">Ставка выиграла</div>
                    </td>
                    <td class="rates__price">
                        <?= preparePrice($bet['bet_price']) ?>
                    </td>
                    <td class="rates__time">
                        <?= getTimeString($bet['bet_date']) ?>
                    </td>
                </tr>
        <?php elseif ($bet['lotState'] === 'closed'): ?>
                <tr class="rates__item rates__item--end">
                    <td class="rates__info">
                        <div class="rates__img">
                            <img src="/uploads/<?= $bet['lot_img'] ?>" width="54" height="40" alt="<?= $bet['lot_title'] ?>">
                        </div>
                        <h3 class="rates__title"><a href="lot.php?id=<?= $bet['lot_id'] ?>"><?= $bet['lot_title'] ?></a></h3>
                    </td>
                    <td class="rates__category">
                        <?= $bet['category_title'] ?>
                    </td>
                    <td class="rates__timer">
                        <div class="timer timer--end">Торги окончены</div>
                    </td>
                    <td class="rates__price">
                        <?= preparePrice($bet['bet_price']) ?>
                    </td>
                    <td class="rates__time">
                        <?= getTimeString($bet['bet_date']) ?>
                    </td>
                </tr>
        <?php elseif ($bet['lotState'] === 'normal'): ?>
                <tr class="rates__item">
                    <td class="rates__info">
                        <div class="rates__img">
                            <img src="/uploads/<?= $bet['lot_img'] ?>" width="54" height="40" alt="<?= $bet['lot_title'] ?>">
                        </div>
                        <h3 class="rates__title"><a href="lot.php?id=<?= $bet['lot_id'] ?>"><?= $bet['lot_title'] ?></a></h3>
                    </td>
                    <td class="rates__category">
                        <?= $bet['category_title'] ?>
                    </td>
                    <td class="rates__timer">
                        <div class="timer"><?= getHoursAndMinutesBeforeLotEnd($bet['stop_date']); ?></div>
                    </td>
                    <td class="rates__price">
                        <?= preparePrice($bet['bet_price']) ?>
                    </td>
                    <td class="rates__time">
                        <?= getTimeString($bet['bet_date']) ?>
                    </td>
                </tr>
        <?php endif; ?>
        <?php endforeach; ?>
    </table>
</section>