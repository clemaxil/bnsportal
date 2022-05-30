<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

?>

  <div style="text-align:center;">
    <h4>
      <?= $dataView['session_numero'] . " " . $dataView['session_name']; ?>
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
        //file in directory
        echo '<div style="padding-left: 20px;"><p>';

        if (is_array($dataView['uploads']) && count($dataView['uploads']) > 0) {
          foreach ($dataView['uploads'] as $upload) {
            echo '<li><i class="fas fa-file-pdf"></i> <a href="' . appHelperUrl_link($dataView['lang'], $dataView['module'], 'download', $dataView['id'], '&document_name=' . $upload['name']) . '">' . str_replace("_"," ",$upload['name']) . '</a></li>';
          }
        }

        echo '</p></div>';


        //file not downloaded
        echo '<div style="padding-left: 20px;"><p>';

        if (is_array($dataView['documents']) && count($dataView['documents']) > 0) {
          foreach ($dataView['documents'] as $document) {
            echo '<li><i class="fas fa-file-pdf"></i> <a href="' . appHelperUrl_link($dataView['lang'], $dataView['module'], 'download', $dataView['id'], '&document_id=' . $document->id . '&document_name=' . $document->name) . '">' . $document->display_name . '</a></li>';
          }
        }

        echo '</p></div>';
    ?>
      

    </div>


  </div>


<?php

include __DIR__ . '/../../../layouts/footer.php';

?>