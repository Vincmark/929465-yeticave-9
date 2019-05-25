<form class="form form--add-lot container <?= $formError ? 'form--invalid' : ''?>" action="add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?= $formItemErrors['lot-name'] ? 'form__item--invalid' : ''?>"> <!-- form__item--invalid -->
            <label for="lot-name">Наименование <sup>*</sup></label>
            <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?= $formError ? $formParams['lot-name'] : ''?>">
            <span class="form__error">Введите наименование лота</span>
        </div>
        <div class="form__item <?= $formItemErrors['category'] ? 'form__item--invalid' : ''?>">
            <label for="category">Категория <sup>*</sup></label>
            <select id="category" name="category">
                <option value="">Выберите категорию</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= $formError&&($category['id']==$formParams['category']) ? 'selected' : ''?>><?= $category['title'] ?></option>
                <?php endforeach; ?>
            </select>
            <span class="form__error">Выберите категорию</span>
        </div>
    </div>
    <div class="form__item form__item--wide <?= $formItemErrors['message'] ? 'form__item--invalid' : ''?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?= $formError ? $formParams['message'] : ''?></textarea>
        <span class="form__error">Напишите описание лота</span>
    </div>
    <div class="form__item form__item--file <?= $formItemErrors['image'] ? 'form__item--invalid' : ''?>">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" name="image" id="lot-img" value="">
            <label for="lot-img">
                Добавить
            </label>
            <span class="form__error">Выберите картинку для лота</span>
        </div>
    </div>
    <div class="form__container-three">
        <div class="form__item form__item--small <?= $formItemErrors['lot-rate'] ? 'form__item--invalid' : ''?>">
            <label for="lot-rate">Начальная цена <sup>*</sup></label>
            <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?= $formError ? $formParams['lot-rate'] : ''?>">
            <span class="form__error">Введите начальную цену</span>
        </div>
        <div class="form__item form__item--small <?= $formItemErrors['lot-step'] ? 'form__item--invalid' : ''?>">
            <label for="lot-step">Шаг ставки <sup>*</sup></label>
            <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?= $formError ? $formParams['lot-step'] : ''?>">
            <span class="form__error">Введите шаг ставки</span>
        </div>
        <div class="form__item <?= $formItemErrors['lot-date'] ? 'form__item--invalid' : ''?>">
            <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
            <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?= $formError ? $formParams['lot-date'] : ''?>">
            <span class="form__error">Введите дату завершения торгов</span>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button" name="button_submit">Добавить лот</button>
</form>