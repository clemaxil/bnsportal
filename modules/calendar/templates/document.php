<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';


?>

  <div style="text-align:center;">
    <h4>
      <?= $_SESSION['session_numero'] . " " . $_SESSION['session_name']; ?>
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


    <p>
      <form enctype="multipart/form-data" method="POST">
        <div class="form-group">
          <input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
          <label for="formFile" class="form-label mt-4"><?= $mod_lang['document_add_file']; ?></label>
          <?= $dataView['upload_message']; ?>
          <input class="form-control" type="file" id="formFile" name="formFile" lang="en">
          <br />
          <div style="text-align: right"><button type="submit" class="btn btn-primary"><?= $mod_lang['button_update_label']; ?></button></div>
        </div>
      </form>
      </p>


    <?php
        echo '<div style="padding-left: 20px;"><p>';

        if (is_array($dataView['uploads']) && count($dataView['uploads']) > 0) {
          foreach ($dataView['uploads'] as $upload) {
            echo '<li><i class="fas fa-file-pdf"></i> <a href="' . appHelperUrl_link($dataView['lang'], $dataView['module'], 'download', $dataView['id'], '&document_name=' . $upload['name']) . '">' . $upload['name'] . '</a></li>';
          }
        }

        echo '</p></div>';
    ?>




    </div>
  </div>


<?php

include __DIR__ . '/../../../layouts/footer.php';

?>