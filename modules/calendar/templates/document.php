<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';


if ($dataView['save'] == "ok") {
  echo '<div class="alert alert-dismissible alert-success">
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				<strong>Success: </strong> ' . $dataView['save-message'] . '</div>';
}


if ($dataView['save'] == "false") {
  echo '<div class="alert alert-dismissible alert-warning">
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				<strong>Warning : </strong> ' . $dataView['save-message'] . '</div>';
}


if ($dataView['error'] === 1) {
  echo '<div class="alert alert-dismissible alert-danger">
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				<strong>Error : </strong> ' . $dataView['error-message'] . '</div>';
} else {
?>

  <div style="text-align:center;">
    <h4>
      <?= $dataView['session']->numero . ", " . $dataView['session']->name; ?>
    </h4>
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
        <a class="nav-link active" href="<?= appHelperUrl_link($dataView['lang'], 'calendar', 'document', $dataView['id']); ?>"><i class="fas fa-folder-open"></i> <?= $mod_lang['submenu_document']; ?></a>
      </li>
    </ul>

    <div id="myTabContent" class="tab-content">


      <?php



      echo '<div class="tab-pane fade show active" id="sondage"><p><i class="far fa-folder-open"></i> ' . $mod_lang['document_all_files'] . '<br />';

      if (is_array($dataView['sondages'])) {
        foreach ($dataView['sondages'] as $sondage) {
          echo '<li><i class="fas fa-file-pdf"></i> <a href="' . appHelperUrl_link($dataView['lang'], 'calendar', 'download', $dataView['id'], 'document_directory=allfiles&document_id=' . $sondage->id . '&document_name=' . $sondage->name . '.pdf') . '">' . $sondage->name . '</a></li>';
        }
      }

      echo '</p><hr><p><i class="far fa-folder-open"></i> ' . $mod_lang['document_my_files'];

      if (is_array($dataView['uploads'])) {
        foreach ($dataView['uploads'] as $upload) {
          echo '<li><i class="fas fa-file-pdf"></i> <a href="' . appHelperUrl_link($dataView['lang'], 'calendar', 'download', $dataView['id'], 'document_directory=myfiles&document_id=&document_name=' . $upload['name']) . '">' . $upload['name'] . '</a></li>';
        }
      }

      echo '</p></div>';
      ?>
      <p>
      <form enctype="multipart/form-data" method="POST">

        <div class="form-group">
          <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
          <label for="formFile" class="form-label mt-4"><?= $mod_lang['document_add_file']; ?></label>
          <?= $dataView['upload_message']; ?>
          <input class="form-control" type="file" id="formFile" name="formFile" lang="en">
          <br />
          <div style="text-align: right"><button type="submit" class="btn btn-primary"><?= $mod_lang['button_update_label']; ?></button></div>

          <br />

        </div>

      </form>
      </p>


    </div>
  </div>


<?php

}

include __DIR__ . '/../../../layouts/footer.php';

?>