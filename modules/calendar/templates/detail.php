<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

require_once(__DIR__ . '/../../../helpers/appHelperI18n.php');


if ($dataView['error_fatal'] !== 1)
{

?>

  <div style="text-align:center;">
    <h4>
      <?= $dataView['session']->numero . ", " . $dataView['session']->name; ?>
    </h4>
  </div>

  <div style="text-align:left;">




    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link active" href="<?= appHelperUrl_link($dataView['lang'], 'calendar', 'detail', $dataView['id']); ?>"><i class="fas fa-clipboard"></i> <?= $mod_lang['submenu_detail']; ?></a>
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
      echo '<br/ ><b>' . $mod_lang['number_of_participants'] . ':</b> ' . $dataView['session']->bns_session_nbstagiaire_c;
      echo '</p>';
      echo '<p></p>';

      if (is_array($dataView['session_fields'])) {
        echo '<br /><hr>';
        echo '<form name="sessionupdate" method="post" action="index.php?q=">';
        echo '<input type="hidden" name="q" value="' . $dataView['lang'] . '/calendar/detail/' . $dataView['id'] . '">';
        echo '<p><b><span class="text-primary"><i class="fas fa-comment-dots"></i> ' . $mod_lang['notes'] . '</span></b>';
        foreach ($dataView['session_fields'] as $session_fields) {
          echo $session_fields;
        }
        echo '<br /><div align="right"><button type="submit" class="btn btn-primary btn-sm">' . $mod_lang['button_update_label'] . '</button></div>';
        echo "</p></form>";
      }

      echo '</div>';
      ?>

    </div>
  </div>


<?php

}

include __DIR__ . '/../../../layouts/footer.php';

?>