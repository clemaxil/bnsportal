<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

if ($dataView['error'] === 1) {
  echo '<div class="alert alert-dismissible alert-danger">
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				<strong>Error : </strong> ' . $dataView['error-message'] . '</div>';
} else {
?>

  <div style="text-align:center;">
    <h4>
      <?= $dataView['session']->numero . " " . $dataView['session']->name; ?>
    </h4>
  </div>

  <div style="text-align:left;">




    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'session', 'detail', $dataView['id']); ?>"><i class="fas fa-clipboard"></i> <?= $mod_lang['submenu_detail']; ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'session', 'date', $dataView['id']); ?>"><i class="fas fa-clock"></i> <?= $mod_lang['submenu_date']; ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'session', 'inscrit', $dataView['id']); ?>"><i class="fas fa-user-graduate"></i> <?= $mod_lang['submenu_registred']; ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="<?= appHelperUrl_link($dataView['lang'], 'session', 'document', $dataView['id']); ?>"><i class="fas fa-folder-open"></i> <?= $mod_lang['submenu_document']; ?></a>
      </li>
    </ul>

    <div id="myTabContent" class="tab-content">

      <div class="tab-pane fade show active" id="allfiles">
        <p><i class="far fa-folder-open"></i><?=  $mod_lang['document_all_files'] ?><br />  
        <!-- liste des fichiers -->    
        </p>
      </div>
      

    </div>
  </div>


<?php
}
include __DIR__ . '/../../../layouts/footer.php';

?>