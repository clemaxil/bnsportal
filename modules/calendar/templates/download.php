<?php

if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

?>

<div style="text-align:center;">
  <h4><?= $dataView['session']->numero . ", " . $dataView['session']->name; ?></h4>
</div>

<div style="text-align:left;">
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'calendar', 'detail', $dataView['id']); ?>"><i class="fas fa-clipboard"></i> <?= $mod_lang['submenu_detail']; ?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'calendar', 'date', $dataView['id']); ?>"><i class="fas fa-clock"></i> <?= $mod_lang['submenu_date']; ?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'calendar', 'inscrit', $dataView['id']); ?>"><i class="fas fa-user-graduate"></i> <?= $mod_lang['submenu_registred']; ?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'calendar', 'document', $dataView['id']); ?>"><i class="fas fa-folder-open"></i> <?= $mod_lang['submenu_document']; ?></a>
    </li>
  </ul>
</div>

<div id="myTabContent" class="tab-content">
  <div style="text-align: center">
    <iframe src="<?php echo 'upload/session/' . $dataView['session']->id . '/' . $_SESSION['user_id'] . '/' . $dataView['document_directory'] . '/' . $dataView['document_name']; ?>" type="application/pdf" width="100%" height="800px" frameborder="0"></iframe>
  </div>
</div>


<?php

include __DIR__ . '/../../../layouts/footer.php';

?>