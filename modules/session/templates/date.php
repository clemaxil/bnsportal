<?php
if (!defined('APP_ENTRY') || !APP_ENTRY) die('Not A Valid Entry Point');

include __DIR__ . '/../../../layouts/header.php';
include __DIR__ . '/../../../layouts/menu.php';

require_once(__DIR__ . '/../../../helpers/appHelperI18n.php');

$date_format = 'd/m/Y';
if ($lang == 'en') {
  $date_format = 'Y-m-d';
}

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
        <a class="nav-link active" href="<?= appHelperUrl_link($dataView['lang'], 'calendar', 'date', $dataView['id']); ?>"><i class="fas fa-clock"></i> <?= $mod_lang['submenu_date']; ?></a>
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



      echo '<div class="tab-pane fade show active" id="dates">';
      echo '<table class="table table-hover">
        <thead>
            <tr>
            <th scope="col">' . $mod_lang['date_start'] . '</th>
            <th scope="col">' . $mod_lang['date_end'] . '</th>
            <th scope="col">' . $mod_lang['date_duration'] . '</th>
            <th scope="col">' . $mod_lang['date_schedules'] . '</th>
            <th scope="col">' . $mod_lang['date_location'] . '</th>
            <th scope="col">' . $mod_lang['date_address'] . '</th>
            <th scope="col">' . $mod_lang['date_trainer'] . '</th>
            </tr>
        </thead>
        <tbody>';

      if (is_array($dataView['dates'])) {
        foreach ($dataView['dates'] as $date) {
          $formateurId = $date->formateur_id;
          if ($date->portal_selected == 1) {
            echo '<tr class="table-primary">';
          } else {
            echo '<tr class="table-info">';
          }
          echo '<th scope="row">' . appHelperI18n_convertDateFromTimezone($date->date_start, "GMT", "Europe/Paris", $date_format . " H:i") . '</th>';
          echo '<td>' . appHelperI18n_convertDateFromTimezone($date->fin, "GMT", "Europe/Paris", $date_format) . '</td>';
          echo '<td>' . $date->duree . ' ' . $date->unit . '</td>';
          echo '<td>' . $date->horairemat1 . ' ' . $date->horairemat2 . ' ' . $date->horaireapm1 . ' ' . $date->horaireapm2 . '</td>';
          echo '<td>' . $dataView['account']->name . '</td>';
          echo '<td>' . $date->account_address . '</td>';
          echo '<td>' . $dataView['formateurs']->$formateurId->first_name . ' ' . $dataView['formateurs']->$formateurId->last_name . '</td>';
          echo '</tr>';
        }
      }

      echo '</tbody>
    </table>';
      echo '</div>';
      ?>

    </div>
  </div>


<?php

}

include __DIR__ . '/../../../layouts/footer.php';

?>