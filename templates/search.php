<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span>Union</span>»</h2>
        <ul class="lots__list">
            <?php foreach ($lots as $lot): ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="/uploads/<?= $lot['lot_img'] ?>" width="350" height="260" alt="<?= htmlspecialchars($lot['title']) ?>">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?= $lot['category_title'] ?></span>
                        <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?= $lot['lot_id'] ?>"><?= htmlspecialchars($lot['title']) ?></a></h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">Стартовая цена</span>
                                <span class="lot__cost"><?= preparePrice($lot['start_price']) ?></span>
                            </div>
                            <div class="lot__timer timer <?= lessThanHourBeforeLotEnd($lot['stop_date']) ? 'timer--finishing' : ''?> ">
                                <?= getHoursAndMinutesBeforeLotEnd($lot['stop_date']); ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
        <li class="pagination-item pagination-item-active"><a>1</a></li>
        <li class="pagination-item"><a href="#">2</a></li>
        <li class="pagination-item"><a href="#">3</a></li>
        <li class="pagination-item"><a href="#">4</a></li>
        <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
    </ul>
</div>