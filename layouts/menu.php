<?php

$uri = '';
if (isset($_REQUEST['lang']) && !empty($_REQUEST['lang'])) {
  $uri = 'index.php?q=' . $_REQUEST['lang'] . '/home/index';
}
?>


<div class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
  <div class="container">
    <a href="<?= $uri; ?>" class="navbar-brand"><i class="fas fa-graduation-cap"></i> <?= $app_lang['brand']; ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">

      <ul class="navbar-nav ms-md-auto">

        <?php if (empty($_SESSION['user_id'])) : ?>
          <li class="nav-item">
            <a rel="noopener" class="nav-link" href="<?= appHelperUrl_link($_REQUEST['lang'], 'auth', 'index'); ?>"><?= $app_lang['mod_auth_login']; ?></a>
          </li>
        <?php endif; ?>

        <?php if (!empty($_SESSION['user_id'])) : ?>
          <li class="nav-item">
            <a rel="noopener" class="nav-link 
              <?php if ($module == "profil") {
                echo 'active';
              } ?>" href="<?= appHelperUrl_link($_REQUEST['lang'], 'profil', 'index'); ?>"><i class="fas fa-user"></i>&nbsp;<?= $app_lang['mod_profil']; ?></a>
          </li>
         
          
          <?php if(appHelperRole_isGranted("administrator") || appHelperRole_isGranted("learner"))
          {
            ?>
          <li class="nav-item">
            <a rel="noopener" class="nav-link <?php if ($module == "session") {
                                                echo 'active';
                                              } ?>" href="<?= appHelperUrl_link($_REQUEST['lang'], 'session', 'index'); ?>">
                                              <i class="fa fa-list"></i>&nbsp;<?= $app_lang['mod_session']; ?></a>
          </li>
          <?php
            }
          ?>

          
          <?php if(appHelperRole_isGranted("former"))
          {
            ?>
          <li class="nav-item">
            <a rel="noopener" class="nav-link <?php if ($module == "calendar") {
                                                echo 'active';
                                              } ?>" href="<?= appHelperUrl_link($_REQUEST['lang'], 'calendar', 'index'); ?>"><i class="fas fa-calendar-alt"></i>&nbsp;<?= $app_lang['mod_calendar']; ?></a>
          </li>
          <?php
            }
          ?>


          <li class="nav-item">
            <a rel="noopener" class="nav-link" href="<?= appHelperUrl_link($_REQUEST['lang'], 'auth', 'logout'); ?>"><i class="fas fa-sign-out-alt"></i>&nbsp;<?= $app_lang['mod_auth_signout']; ?></a>
          </li>
        <?php endif; ?>

        </li>
      </ul>
    </div>
  </div>
</div>