<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';

?>

<link rel="stylesheet" href="modules/auth/assets/css/styles.css" type="text/css">
<div style="text-align:center">

  <?php if (!empty($dataView['resultMail'])) : ?>

    <?php if ($dataView['resultMail'] == 1) : ?>

      <main class="form-signin">
        <form method="post" action="<?= appHelperUrl_link($lang, 'auth', 'password-forgot'); ?>">
          <div style="text-align: center;"><?= $mod_lang['password_new_send']; ?></div>
          <div style="text-align: center;"><br /></div>
          <a href="<?= appHelperUrl_link($lang, 'auth', 'index'); ?>"><button class="w-100 btn btn-lg btn-primary" type="button"><?= $mod_lang['login_submit']; ?></button></a>
        </form>
      </main>

    <?php endif; ?>

    <?php if ($dataView['resultMail'] != 1) : ?>
      <p><?= $dataView['resultMail']; ?></p>
    <?php endif; ?>

  <?php endif; ?>


  <?php if (empty($dataView['resultMail'])) : ?>

    <main class="form-signin">
      <form method="post" action="<?= appHelperUrl_link($lang, 'auth', 'password-forgot'); ?>">
        <div class="form-floating">
          <input name="email" type="email" class="form-control" id="floatingInput" placeholder="name@example.com" oninvalid="this.setCustomValidity('no valid')" oninput="setCustomValidity('')">
          <label for="floatingInput"><?= $mod_lang['email']; ?></label>
        </div>
        <button class="w-100 btn btn-lg btn-primary" type="submit"><?= $mod_lang['password_new']; ?></button>
      </form>
    </main>

  <?php endif; ?>

</div>

<?php

include __DIR__ . '/../../../layouts/footer.php';

?>