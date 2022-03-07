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


    <?php
        echo '<div class="tab-pane fade show active" id="sondage"><p><i class="far fa-folder-open"></i> ' . $mod_lang['document_all_files'] . '<br />';

        /*if (is_array($dataView['sondages'])) {
          foreach ($dataView['sondages'] as $sondage) {
            echo '<li><i class="fas fa-file-pdf"></i> <a href="' . appHelperUrl_link($dataView['lang'], 'calendar', 'download', $dataView['id'], 'document_directory=allfiles&document_id=' . $sondage->id . '&document_name=' . $sondage->name . '.pdf') . '">' . $sondage->name . '</a></li>';
          }
        }*/

        echo '</p><hr><p><i class="far fa-folder-open"></i> ' . $mod_lang['document_my_files'];

        if (is_array($dataView['uploads']) && count($dataView['uploads']) > 0) {
          foreach ($dataView['uploads'] as $upload) {
            echo '<li><i class="fas fa-file-pdf"></i> <a href="' . appHelperUrl_link($dataView['lang'], $dataView['module'], 'download', $dataView['id'], 'document_directory=myfiles&document_id=&document_name=' . $upload['name']) . '">' . $upload['name'] . '</a></li>';
          }
        }

        echo '</p></div>';
    ?>
      

    </div>
  </div>


<?php
}
include __DIR__ . '/../../../layouts/footer.php';

?>