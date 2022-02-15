<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';

?>

<link rel="stylesheet" href="modules/auth/assets/css/styles.css" type="text/css">

<main class="form-signin">
  <form method="post" action="<?php echo appHelperUrl_link($lang, 'auth', 'index'); ?>">
    <div style="text-align: center;"><img class="mb-4" src="assets/img/company_logo.png"></div>
    <div class="form-floating">
      <input name="email" type="email" class="form-control" id="floatingInput" placeholder="name@example.com" oninvalid="this.setCustomValidity('no valid')" oninput="setCustomValidity('')">
      <label for="floatingInput"><?= $mod_lang['email']; ?></label>
    </div>
    <div class="form-floating">
      <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password">
      <label for="floatingPassword"><?= $mod_lang['password']; ?></label>
    </div>
    <button class="w-100 btn btn-lg btn-primary" type="submit"><?= $mod_lang['login_submit']; ?></button>
  </form>
  <?php
  if (isset($_SESSION['auth_verify']) && $_SESSION['auth_verify'] == 0) {
    echo '<p><b>' . $mod_lang['login_false'] . '</b><br>';
    echo '<a href="' . appHelperUrl_link($lang, 'auth', 'password-forgot') . '">' . $mod_lang['password_forgot_question'] . '</a></p>';
  }
  ?>
</main>

<?php

include __DIR__ . '/../../../layouts/footer.php';

?>