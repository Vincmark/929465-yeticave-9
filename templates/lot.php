
<section class="lot-item container">
      <h2><?= $lot['title'] ?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="/uploads/<?= $lot['lot_img'] ?>" width="730" height="548" alt="Сноуборд">
          </div>
          <p class="lot-item__category">Категория: <span><?= $lot['category_title'] ?></span></p>
          <p class="lot-item__description"><?= $lot['description'] ?></p>
        </div>
        <div class="lot-item__right">
          <?php if ($is_auth):?>
            <div class="lot-item__state">
            <div class="lot-item__timer timer <?= lessThanHourBeforeLotEnd($lot['stop_date']) ? 'timer--finishing' : ''?> ">
                <?= getHoursAndMinutesBeforeLotEnd($lot['stop_date']); ?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?= preparePrice($betForm['currentPrice']) ?></span>
</div>
<div class="lot-item__min-cost">
    Мин. ставка <span><?= preparePrice($betForm['minBetPrice']) ?></span>
</div>
</div>
<form class="lot-item__form" action="lot.php" method="post" autocomplete="off">
    <p class="lot-item__form-item form__item <?= $formError ? 'form__item--invalid' : ''?>">   <!-- form__item--invalid -->
        <label for="cost">Ваша ставка</label>
        <input id="cost" type="text" name="cost" placeholder="<?= $betForm['minBetPrice'] ?>" value="<?= $formError ? $formParams['cost'] : ''?>">
        <input type="hidden" name="lot_id" value="<?= $lot['id'] ?>">
        <input type="hidden" name="min_price" value="<?= $betForm['minBetPrice'] ?>">
        <input type="hidden" name="lot_life_time" value="<?= $lot['stop_date'] ?>">
        <span class="form__error">Введите наименование лота</span>
    </p>
    <button type="submit" class="button">Сделать ставку</button>
</form>
</div>
            <?php endif; ?>
<div class="history">
    <h3>История ставок (<span><?= count($bets) ?></span>)</h3>
    <?php if (count($bets)>0):?>
    <table class="history__list">
        <?php foreach ($bets as $bet): ?>
        <tr class="history__item">
            <td class="history__name"><?= $bet['name'] ?></td>
            <td class="history__price"><?= $bet['bet_price'] ?> р</td>
            <td class="history__time"><?= getTimeString($bet['bet_date']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
</div>
</div>
</section>