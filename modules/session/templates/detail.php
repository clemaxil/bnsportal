<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

require_once(__DIR__ . '/../../../helpers/appHelperI18n.php');


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
        <a class="nav-link active" href="<?= appHelperUrl_link($dataView['lang'], 'session', 'detail', $dataView['id']); ?>"><i class="fas fa-clipboard"></i> <?= $mod_lang['submenu_detail']; ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'session', 'date', $dataView['id']); ?>"><i class="fas fa-clock"></i> <?= $mod_lang['submenu_date']; ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'session', 'inscrit', $dataView['id']); ?>"><i class="fas fa-user-graduate"></i> <?= $mod_lang['submenu_registred']; ?></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= appHelperUrl_link($dataView['lang'], 'session', 'document', $dataView['id']); ?>"><i class="fas fa-folder-open"></i> <?= $mod_lang['submenu_document']; ?></a>
      </li>
    </ul>

    <div id="myTabContent" class="tab-content">


      <?php

      echo '<div class="tab-pane fade show active" id="session">';
      echo '<p>';
      echo '<b>' . $mod_lang['type'] . ':</b> ' . ucfirst($dataView['session']->bnstype);
      echo '<br/ ><b>' . $mod_lang['modality'] . ':</b> ' . $dataView['session']->modalitemo;
      echo '<br/ ><b>' . $mod_lang['objective'] . ':</b> ' . html_entity_decode($dataView['session']->bns_session_objectif_c);
      echo '<br/ ><b>' . $mod_lang['public'] . ':</b> ' . html_entity_decode($dataView['session']->publicsession);
      echo '<br/ ><b>' . $mod_lang['program'] . ':</b> ' . html_entity_decode($dataView['session']->trainingprogram);
      echo '<br/ ><b>' . $mod_lang['prerequisites'] . ':</b> ' . html_entity_decode($dataView['session']->requiredpostsession);
      // echo '<br/ ><b>' . $mod_lang['number_of_participants'] . ':</b> ' . $dataView['session']->bns_session_nbstagiaire_c;
      echo '</p>';
      echo '<p></p>';

      echo '</div>';
      ?>

    </div>
  </div>


<?php

}

include __DIR__ . '/../../../layouts/footer.php';

?>