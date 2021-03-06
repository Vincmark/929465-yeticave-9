<form class="form container <?= $formError ? 'form--invalid' : ''?>" action="login.php" method="post">
    <h2>Вход</h2>
    <?php if($userIdentificationError):?>
    <span class="form__error form__error--bottom">Вы ввели неверный email/пароль</span>
    <?php endif; ?>
    <div class="form__item <?= isset($formItemErrors['email']) ? 'form__item--invalid' : ''?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= $formError ? $formParams['email'] : ''?>">
        <span class="form__error">Введите e-mail</span>
    </div>
    <div class="form__item form__item--last <?= isset($formItemErrors['password']) ? 'form__item--invalid' : ''?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль">
        <span class="form__error">Введите пароль</span>
    </div>
    <button type="submit" class="button">Войти</button>
</form>